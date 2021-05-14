<?php
$admin = true;
$init = 'updateDevice';
$title = 'Edit devices';
require_once('template.php');
?>

<div class="main-content">
    <form id="updateDevice" action="api/devices.php">
        <label for="selectDevice">Select device:</label>
        <select name="id" id="selectDevice">
        <option >Select device</option>
        </select>
        <label for="name">Edit device name:</label>
        <input type="text" id="name" name="name">
        <label for="deviceTypes">Device type:</label>
        <select name="typeId" id="deviceTypes">
        <option value="0">Select device type</option>
        </select>
        <a href="edit_device_type.php">Edit an existing device type</a>
        <label for="selectRoom">Change room the device belongs to:</label>
        <select name="roomId" id="selectRoom">
        <option>Select room</option>
        </select>
        <div class="action-buttons">
            <input type="submit" value="Save">
            <input type="button" value="Delete device" id="delete">
        </div>
    </form>

</div>
<div>
    <ul class="nav_white">
        <li><a href="index.php">Home</a></li>
        <li><a href="admin_settings.php">Admin settings</a></li>
        <li>Edit a device</li>
    </ul>
</div>
