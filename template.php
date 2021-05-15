<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
  define('ACCESSIBLE', false);
}

require_once('util/autoload.php');
use \Util\Session;
use \Util\Logger;

if (!Session::logged_in() && !strpos($_SERVER['PHP_SELF'], 'login.php')) {
  http_response_code(302);
  header('Location: login.php?redirectUri='.$_SERVER['REQUEST_URI']);
  Logger::log_access();
  exit();
}

if (isset($admin) && $admin) {
  if (!Session::is_admin()) {
    http_response_code(403);
    Logger::log_access();
    exit();  
  }
}

function print_page(&$buffer) {
  Logger::log_access();
  return <<<EOF
  $buffer
      </div>
    </div>
  </body>
</html>
EOF;
}

header("Content-security-policy: default-src 'none'; connect-src 'self'; img-src 'self'; style-src 'self'; script-src 'self' https://cdn.jsdelivr.net/npm/chart.js");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy same-origin");

ob_start('print_page');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'SmartHome';  ?></title>
    <link rel="stylesheet" href="css/page.css">
    <?php if (isset($head)) { echo $head; } ?>
    <script defer src="media/js/app.js"></script>
  </head>
  <body <?php if (isset($init)) { echo "data-init=\"$init\""; } ?>>
    <div class="container">
      <div class="head">
        <div id="sideBar" class="sideBar closed">
          <p id="close">&times;</p>
          <div class="menu" id="menu">  
            <!-- <div>s and <p>s for the rooms will be added here -->
            <!--add options if user is admin-->
          </div>
          <?php
          if(Session::is_admin()) { 
            $admin = <<<EOA
            <div class="admin"> 
            <p> <a href="admin_settings.php">Admin settings</a></p> 
            </div>
EOA;
            echo ($admin);
          };
          ?>
          <p id="logout"><a href="logout.php">Logout</a></p>
       </div>
        <p id="open">&#9776;</p>
        <h1 id="header"><?= isset($title) ? $title : 'SmartHome';  ?></h1>
      </div>
      <div class="content">
      