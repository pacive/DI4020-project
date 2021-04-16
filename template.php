<?php
require_once('util/autoload.php');
use \Util\Session;
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
        <h1 id="header">Smarthome/or name of the house?</h1>
      </div>
      <div class="content">
        <div id="image" class ="image">
          <img src="media/images/blueprint.png" alt="Layout sketch" usemap="#blueprint"/>
          <map id="blueprint" name="blueprint">
          </map>
          <div id="roompopup" class="roompopup">
            <h6 id="roomname">roomname</h6><span id="closepopup">X</span>
            <div id="devicelist"></div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>