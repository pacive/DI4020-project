<?php
require_once('util/autoload.php');
use \Util\Session;
use \Util\Logger;

if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
  http_response_code(404);
  Logger::log_access();
  exit();
}

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
            <p> <a href="change_users.php">Edit users</a></p> 
            <p> <a href="change_devices.php">Edit devices</a></p> 

            </div>
EOA;
            echo ($admin);
          };
          ?>
          <p id="logout">Logout</p>
       </div>
        <p id="open">&#9776;</p>
        <h1 id="header"><?= isset($title) ? $title : 'SmartHome';  ?></h1>
      </div>
      <div class="content">