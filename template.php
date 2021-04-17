<?php
require_once('util/autoload.php');
use \Util\Session;
use \Util\Logger;

if (!Session::logged_in() && !strpos($_SERVER['PHP_SELF'], 'login.php')) {
  http_response_code(302);
  header('Location: login.php?redirectUri='.$_SERVER['REQUEST_URI']);
  Logger::log_access();
  exit();
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
    <title>SmartHome.net</title>
    <link rel="stylesheet" href="css/page.css">
    <script defer src="media/js/app.js"></script>
  </head>
  <body>
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
            <p onclick="getUsernames()">Edit users</p> 
            <p>Edit devices</p>
            </div>
EOA;
            echo ($admin);
          };
          ?>
          <p id="logout">Logout</p>
       </div>
        <p id="open">&#9776;</p>
        <h1 id="header">Smarthome/or name of the house?</h1>
      </div>
      <div class="content">