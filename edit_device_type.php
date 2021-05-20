<?php
$admin = true;
$init = 'updateDeviceType';
$title = 'Edit device type';
require_once('template.php');
?>

<div class="main-content">
    <form id="updateDeviceType" action="api/devicetypes.php">
        <label for="deviceTypes">Select device type:</label>
        <select name="id" id="deviceTypes">
        <option >Select device type</option>
        </select>
        <label for="deviceTypeName">New name:</label>
        <input type="text" id="deviceTypeName" name="name">
        <div class="action-buttons">
            <input type="submit" value="Save">
            <input type="button" value="Delete device type" id="delete">
        </div>
        <p id="deviceTypeUpdated"></p>
    </form>

</div>
<div>
<ul class="nav_white">
        <li><a href="index.php">Home</a></li>
        <li><a href="admin_settings.php">Admin settings</a></li>
        <li><a href="edit_device.php">Edit a device</a></li>
        <li>Edit device type</li>
    </ul>
</div>