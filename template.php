<?php
require_once('util/autoload.php');
use \Util\Session;
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>SmartHome.net</title>
    <link rel="stylesheet" href="css/page.css">
    <script defer src="media/js/app.js"></script>
  </head>
  <body>
      <div class="content">
        <h1 id="header">Smarthome/or name of the house?</h1>
          <div id="sideBar" class="sideBar">
              <p id="close">&times;</p>
              <div class="menu" id="menu">  
                <!-- <div>s and <p>s for the rooms will be added here -->
              <!--add options if user is admin-->
              <?php
              if(Session::is_admin()) { 
                $admin = <<<EOA
                <div class="admin"> 
                <p>Edit users</p> 
                <p>Edit devices</p>
                </div>
EOA;
                echo ($admin);
              };
              ?>
              </div>
              <p id="logout">Logout</p>
          </div>
          <p id="open">&#9776;</p>

          <div id="image" class ="image">
            <img src="https://media.discordapp.net/attachments/801081934410678302/826815048143208468/notstolen.png?width=604&height=415" alt="Layout sketch" usemap="#blueprint"/>
            <map id="blueprint" name="blueprint">
            </map>
          </div>
      </div>
  </body>
</html>