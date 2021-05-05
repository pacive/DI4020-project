<?php
$admin = true;
$init = 'updateDevice';
$title = 'Edit devices';
require_once('template.php');
?>

    <form id="updateDevice" action="api/devices.php">
        <label for="selectDevice">Select device:</label>
        <select name="id" id="selectDevice">
        <option value="0">Select device</option>
        </select> <br>
        <label for="name">Edit device name:</label>
        <input type="text" id="name" name="name"><br>
        <label for="getTypeIds">Device type:</label>
        <select name="typeId" id="getTypeIds">
        </select> <br>
        <label for="getRooms">Change room the device belongs to:</label>
        <select name="roomId" id="selectRoom">
        <option value="0">Select room</option>
        </select> <br>
        <input type="submit" value="save">
        <input type="button" value="Delete device" onclick="deleteDevice()">
    </form>

    <p id="deviceUpdated"></p>
