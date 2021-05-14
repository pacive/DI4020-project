
<?php
$admin = true;
$init = 'addDeviceType'; 
$title = 'Add new device type';
require_once('template.php');
?> 

<div class="main-content">
<form id="newDeviceTypeForm" action="api/devicetypes.php" method="POST">
    <label for="newDeviceType">Device type name:</label>
    <input type="text" id="newDeviceType" name="name">
    <div class="action-buttons">
        <input type="submit" value="Save">
    </div>
    <p id="deviceTypeAdded"></p>
</form>
</div>

<div>
<ul class="nav_white">
        <li><a href="index.php">Home</a></li>
        <li><a href="admin_settings.php">Admin settings</a></li>
        <li><a href="add_device.php">Add a device</a></li>
        <li>Add new device type</li>
    </ul>
</div>
