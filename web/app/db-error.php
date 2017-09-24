<?php
// custom WordPress database error page

header('HTTP/1.1 503 Service Temporarily Unavailable');
header('Status: 503 Service Temporarily Unavailable');
header('Retry-After: 600'); // 1 hour = 3600 seconds

// If you wish to email yourself upon an error
// mail("your@email.com", "Database Error", "There is a problem with the database!", "From: Db Error Watching");

?>

<!DOCTYPE HTML>
<html>
<head>
<title>Vorübergehend nicht erreichbar</title>
<style>
body { padding: 20px; background: #3377aa; color: white; font-size: 2em; }
</style>
</head>
<body>
  Wegen technischen Problem oder Wartungsarbeiten ist diese Website vorübergehend nicht erreichbar.
</body>
</html>
