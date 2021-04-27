<?php
$admin = true;
$init = 'updateDevice';
require_once('template.php');
?>

    <form id="updateDevice" action="api/devices.php">
        <label>Select device:</label>
        <select name="id" id="selectDevices">
        </select> <br>
        <label for="text">Edit device name:</label>
        <input type="text" id="username" name="name"><br>
        <label for="text">Device type:</label>
        <select name="typeId" id="getTypeIds">
        </select> <br>
        <label for="text">Change room the device belongs to:</label>
        <select name="roomId" id="getRooms">
        </select> <br>
        <input type="submit" value="save">
        <input type="button" value="Delete device" onclick="deleteDevice()">
    </form>

    <p id="deviceUpdated"></p>
</div>
</body>
</html>