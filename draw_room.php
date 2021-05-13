<?php
$admin = true;
$init = 'drawRoom';
require_once('template.php');
?>
<div class="main-content">
  <div>
    <div class="canvas-background">
      <img src="media/images/blueprint.png" alt="blueprint" />
      <canvas id="draw-room"></canvas>
    </div>
    <button id="save">Save</button>
    <button id="undo">Undo</button>
    <button id="reset">Reset</button>
    <p id="roomUpdated"></p>
  </div>
</div>
<div class="nav">
<button onclick="document.location='index.php'">Home</button> 
<button onclick="document.location='admin_settings.php'">Back to admin settings</button>
<button onclick="document.location='edit_room.php'">Back to edit room</button>
</div>