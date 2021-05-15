<?php
$admin = true;
$init = 'drawRoom';
$title = 'Draw room';
require_once('template.php');
?>
<div class="main-content draw-room">
    <div class="canvas-background">
      <img id="blueprint" src="media/images/blueprint.png" alt="blueprint" />
      <canvas id="draw-room"></canvas>
    </div>
    <div class="action-buttons">
        <button id="save">Save</button>
        <button id="undo">Undo</button>
        <button id="reset">Reset</button>
    </div>
    <p id="roomUpdated"></p> 
</div>
<div>
    <ul class="nav_white">
        <li><a href="index.php">Home</a></li>
        <li><a href="admin_settings.php">Admin settings</a></li>
        <li><a href="edit_room.php">Edit a room</a></li>
        <li>Draw room</li>
    </ul>
</div>
