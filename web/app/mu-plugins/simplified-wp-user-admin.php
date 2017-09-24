<?php
/**
 * Plugin Name: Simplified user roles
 * Plugin URI: https://github.com/roots/bedrock/
 * Description: Idea of this plugin is to reserve administrator roles only for developers and sys admins. Removes authors and contributors. Gives Editors capability to add new editors and subcsribers. Hides Administrators from editors.
 * Version: 1.0.0
 * Author: Onni Hakala
 * Author URI: https://github.com/onnimonni
 * License: MIT License
 */
namespace Koodimonni;
use WP_User;
class User_Caps {

  // Add our filters
  function __construct(){
    add_filter( 'views_users',    array(&$this, 'change_user_views'),10,1);
    add_action( 'init',           array(&$this, 'change_roles'));
    add_action( 'init',           array(&$this, 'allow_editor_manage_users'));
    add_action( 'pre_user_query', array(&$this, 'pre_user_query'));
    add_filter( 'editable_roles', array(&$this, 'editable_roles'));
    add_filter( 'map_meta_cap',   array(&$this, 'map_meta_cap'),10,4);
  }
  /**
   * Edit different sections in the top of user listing
   * These include normally: All, Administrators, Editors, Authors, Subscribers
   */
  function change_user_views($views) {
    // If current user is not admin hide administrators count from authors
    if ( !current_user_can( 'manage_options' ) ) {
      // Count how many admins and remove it from total count
      $admin_count = filter_var($views['administrator'], FILTER_SANITIZE_NUMBER_INT);
      $user_count = filter_var($views['all'], FILTER_SANITIZE_NUMBER_INT);
      $all_but_admin = $user_count - $admin_count;
      // Replace the count without admins
      $views['all'] = preg_replace('/\([0-9]+\)/', '('.$all_but_admin.')', $views['all']);
      // Hide admin option
      unset($views['administrator']);
    }
    return $views;
  }
  /**
   * Remove some unneeded roles and change role names
   */
  function change_roles() {
      global $wp_roles;
      if ( ! isset( $wp_roles ) )
          $wp_roles = new WP_Roles();
      // Remove roles which are not used
      $wp_roles->remove_role("contributor");
      $wp_roles->remove_role("author");
      // Rename other roles for more usability
      // Change them for your language
      $wp_roles->roles['subscriber']['name'] = __('Subscriber');
      $wp_roles->role_names['subscriber'] = __('Subscriber');
      $wp_roles->roles['editor']['name'] = __('Editor');
      $wp_roles->role_names['editor'] = __('Editor');
      $wp_roles->roles['administrator']['name'] = __('Admin');
      $wp_roles->role_names['administrator'] = __('Admin');
  }
  /**
   * Give editors option to edit users
   */
  function allow_editor_manage_users() {
      if ( get_option( strtolower(__CLASS__).'_add_cap_editor_once' ) != 'done' ) {

          // let editor manage users
          $edit_editor = get_role('editor');
          $edit_editor->add_cap('edit_users');
          $edit_editor->add_cap('list_users');
          $edit_editor->add_cap('promote_users');
          $edit_editor->add_cap('create_users');
          $edit_editor->add_cap('add_users');
          $edit_editor->add_cap('delete_users');
          // Only do this once
          update_option( strtolower(__CLASS__).'_add_cap_editor_once', 'done' );
      }
  }
  /**
   * Disallow editor from choosing administrator from list of available roles
   */
  function editable_roles( $roles ){
    if( isset( $roles['administrator'] ) && !current_user_can('administrator') ){
      unset( $roles['administrator']);
    }
    return $roles;
  }
  /**
   * If someone is trying to edit or delete an admin and that user isn't an admin, don't allow it
   */
  function map_meta_cap( $caps, $cap, $user_id, $args ){
    switch( $cap ){
        case 'edit_user':
        case 'remove_user':
        case 'promote_user':
            if( isset($args[0]) && $args[0] == $user_id )
                break;
            elseif( !isset($args[0]) )
                $caps[] = 'do_not_allow';
            $other = new WP_User( absint($args[0]) );
            if( $other->has_cap( 'administrator' ) ){
                if(!current_user_can('administrator')){
                    $caps[] = 'do_not_allow';
                }
            }
            break;
        case 'delete_user':
        case 'delete_users':
            if( !isset($args[0]) )
                break;
            $other = new WP_User( absint($args[0]) );
            if( $other->has_cap( 'administrator' ) ){
                if(!current_user_can('administrator')){
                    $caps[] = 'do_not_allow';
                }
            }
            break;
        default:
            break;
    }
    return $caps;
  }
  /**
   * Hide admins from user list
   */
  function pre_user_query($user_search) {
    $user = wp_get_current_user();
    if (!current_user_can('manage_options')) { // Is Not Administrator - Remove Administrator
      global $wpdb;
      $user_search->query_where =
          str_replace('WHERE 1=1',
              "WHERE 1=1 AND {$wpdb->users}.ID IN (
                   SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta
                      WHERE {$wpdb->usermeta}.meta_key = '{$wpdb->prefix}capabilities'
                      AND {$wpdb->usermeta}.meta_value NOT LIKE '%administrator%')",
              $user_search->query_where
          );
    }
  }
}
new User_Caps();
