<?php
$admin = true;
$init = 'drawRoom';
require_once('template.php');
?>
  <div>
  <div class="canvas-background">
    <canvas id="draw-room"></canvas>
  </div>
    <button id="save">Save</button>
    <button id="undo">Undo</button>
    <button id="reset">Reset</button>
    <p id="roomUpdated"></p>
  </div>
