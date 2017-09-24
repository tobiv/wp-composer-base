# A Composer-managed WordPress Starter

A primer for Composer-managed WordPress installation with a custom bespoke theme.

## Overview

### Composer/WordPress
* WordPress core
* WP languages installer (https://wp-languages.github.io/)
* wp-configs and htaccess for development, staging and production.

### Plugins
* UpdraftPlus for Backups
* iThemes Security (better-wp-security)
* <tv> WP-Primordial mu-plugin (https://github.com/tobiv/wp-primordial)

### Must-use Plugins
* Remove accents on upload (https://gist.github.com/onnimonni/d58bdcff44f8208a15c7)
* Simplified WP User model (https://gist.github.com/onnimonni/3bdfe1d8b8b0addc4321)

### Theme Base

## Usage

0. Point your webroot to the `web/` folder.

1. Add/remove plugins to `composer.json` to fit your needs, then install WordPress core, languages, plugins and mu-plugins:

        $ composer install

2. Update `wp-config.php` with your database details, authentication keys, domain(s) and other settings.

4. Start building your theme/website/app!

5. Todo: Task runner for building theme assets

Make sure to update the `htaccess` files with your domain(s) and config.
