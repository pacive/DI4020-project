
<?php
$admin = true;
$init = 'addDevice'; //anger vilken funktion i INIT-objektet i app.js ska köras när sidan laddas
$title = 'Add new device';
require_once('template.php');
?> 

<div class="main-content">
<form id="addDevice" action="api/devices.php" method="POST">
    <label for="username">Device name:</label>
    <input type="text" id="username" name="name"><br>
    <label for="deviceTypes">Select device type:</label>
    <select name="typeId" id="deviceTypes">
    <option value="0">Select device type</option>
    </select> <br>
    <label for="selectRoom">Select room:</label>
    <select name="roomId" id="selectRoom">
    <option value="0">Select room</option>

    </select> <br>
    <input type="submit" value="Save">
    <br>
    <label> Add a new device type: </label>
    <input type="button" value="Add a device type" onclick="document.location='add_device_type.php'">
</form>

<p id="deviceAdded"></p>
</div>

<div class="nav">
<button onclick="document.location='index.php'">Home</button> 
<button onclick="document.location='admin_settings.php'">Back to admin settings</button>
</div>
