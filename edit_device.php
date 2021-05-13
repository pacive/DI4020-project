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
        <option value="0">Select device</option>
        </select> <br>
        <label for="name">Edit device name:</label>
        <input type="text" id="name" name="name"><br>
        <label for="deviceTypes">Device type:</label>
        <select name="typeId" id="deviceTypes">
        </select> <br>
        <label for="selectRoom">Change room the device belongs to:</label>
        <select name="roomId" id="selectRoom">
        <option value="0">Select room</option>
        </select> <br>
        <input type="submit" value="save">
        <input type="button" value="Delete device" id="delete">
        <br>
        <label> Edit an existing device type here: </label>
        <input type="button" value="Edit device type" onclick="document.location='edit_device_type.php'">
    </form>

    <p id="deviceUpdated"></p>
</div>
    <div class="nav">
    <button onclick="document.location='index.php'">Home</button> 
    <button onclick="document.location='admin_settings.php'">Back to admin settings</button>
    </div>