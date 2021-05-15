<?php 
  $init = 'index';
  require_once('template.php');
?>
<?php
if (!isset($_COOKIE['first-login'])) {
?>
<!--MODAL -->
<div id='modal' class='modal'>
    <div class='popup'>
        <span class="close" id='close-modal'>&times;</span>

        <div>
          <h1>Welcome home!</h1>

          <p>Use the main menu located in the top left corner to interact with your home, or click directly on the rooms in floor plan below.</p>
          <p>/^_^\</p>
        </div>
    </div>
</div>
<?php
}
setcookie('first-login', 'false', (1 << 32), '', 'ideweb2.hh.se', false, true);
?>
<div class="main-content">
<div id="image" class ="image">
  <img src="media/images/blueprint.png" alt="Layout sketch" usemap="#blueprint"/>
  <map id="blueprint" name="blueprint">
  </map>
  <div id="roompopup" class="roompopup">
    <span id="roomname"></span><span id="closepopup">&times;</span>
    <div id="devicelist"></div>
  </div>
</div>
</div>