<?php
$admin = true;
$init = 'updateDevice';
$title = 'Edit devices';
require_once('template.php');
?>

    <form id="updateDevice" action="api/devices.php">
        <label for="selectDevices">Select device:</label>
        <select name="id" id="selectDevices">
        <option value="0">Select device</option>
        </select> <br>
        <label for="username">Edit device name:</label>
        <input type="text" id="username" name="name"><br>
        <label for="getTypeIds">Device type:</label>
        <select name="typeId" id="getTypeIds">
        </select> <br>
        <label for="text">Change room the device belongs to:</label>
        <select name="roomId" id="getRooms">
        <option value="0">Select room</option>
        </select> <br>
        <input type="submit" value="save">
        <input type="button" value="Delete device" onclick="deleteDevice()">
    </form>

    <p id="deviceUpdated"></p>


</div>
</body>
</html>