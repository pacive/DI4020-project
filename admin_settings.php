<?php
$admin = true;
$title = 'Settings';
require_once('template.php');
?>

<div class="main-content admin-menu">
<div>
<h3>Users</h3>
<p><a href="edit_user.php">Edit a user</a></p>
<p><a href="add_user.php">Add new user</a></p>
</div>
<div>
<h3>Devices</h3>
<p><a href="edit_device.php">Edit a device</a></p>
<p><a href="add_device.php">Add new device</a></p>
</div>
<div>
<h3>Rooms</h3>
<p><a href="edit_room.php">Edit a room</a></p>
<p><a href="add_room.php">Add new room</a></p>
</div>
<div>
<h3>Other</h3>
<p><a href="statistics.php">Statistics</a></p>
<p><a href="log.php">Log</a></p>
<p><a href="apiexplorer.php">API Explorer</a></p>
</div>
</div>
<div  class="nav">
<button onclick="document.location='index.php'">Home</button> 
</div>
