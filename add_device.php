<?php
$admin = true;
$init = 'addDevice';
require_once('template.php');
?>

<div class="main-content">
<form id="addDevice" action="api/devices.php" method="POST">
    <label for="username">Device name:</label>
    <input type="text" id="username" name="name">
    <label for="deviceTypes">Select device type:</label>
    <select name="typeId" id="deviceTypes">
    <option>Select device type</option>
    </select>
    <a href="add_device_type.php">Add a new device type</a>
    <label for="selectRoom">Select room:</label>
    <select name="roomId" id="selectRoom">
    <option value="0">Select room</option>
    </select>
    <div class="action-buttons">
        <input type="submit" value="Save">
    </div>
    <p id="deviceAdded"></p>
</form>
</div>

<div>
    <ul class="nav_white">
        <li><a href="index.php">Home</a></li>
        <li><a href="admin_settings.php">Admin settings</a></li>
        <li>Add new device</li>
    </ul>
</div>
