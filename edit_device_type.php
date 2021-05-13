

<?php
$admin = true;
$init = 'updateDeviceType'; 
$title = 'Edit device type';
require_once('template.php');
?>

<div class="main-content">
    <form id="updateDeviceType" action="api/devicetypes.php">
        <label for="deviceTypes">Select device type:</label>
        <select name="typeId" id="deviceTypes">
        <option value="0">Select device type</option>
        </select> <br>
        <label for="deviceTypeName">Edit device type name:</label>
        <input type="text" id="deviceTypeName" name="name"><br>
        <input type="submit" value="save">
        <input type="button" value="Delete device type" id="delete"> 
        
    </form>

    <p id="deviceTypeUpdated"></p>
</div>
    <div class="nav">
    <button onclick="document.location='index.php'">Home</button> 
    <button onclick="document.location='admin_settings.php'">Back to admin settings</button>
    </div>