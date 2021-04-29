
<?php
$admin = true;
$init = 'addDevice'; //anger vilken funktion i INIT-objektet i app.js ska köras när sidan laddas
require_once('template.php');
?> 


<form id="addDevice" action="api/devices.php" method="POST">
    <label for="text">Device name:</label>
    <input type="text" id="username" name="name"><br>
    <label for="text">Select device type:</label>
    <select name="typeId" id="getTypeIds">
    <option value="0">Select device type</option>
    </select> <br>
    <label for="text">Select room:</label>
    <select name="roomId" id="getRooms">
    <option value="0">Select room</option>

    </select> <br>
    <input type="submit" value="Save">
</form>

<p id="deviceAdded"></p>

<button> <a href="index.php"> Home </a> </button>


</div>
</body>
</html>