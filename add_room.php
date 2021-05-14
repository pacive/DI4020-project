
<?php
$admin = true;
$init = 'addRoom';
$title = 'Add new room';
require_once('template.php');
?> 

<div class="main-content">
<form id="addRoom" action="api/rooms.php" method="POST">
    <label for="roomname">Roomname:</label>
    <input type="text" id="roomname" name="name" ><br>

    <label for="roomTypes">Select room type:</label>
    <select name="typeId" id="roomTypes" >
    <option >Select room type</option>
    </select>
    
    <br>
    <input type="submit" value="Save"> <br>
    <label> Add a new room type: </label>
    <input type="button" value="Add a room type" onclick="document.location='add_room_type.php'">
    <p id="roomAdded"></p>

</form>
</div>

<div class="nav">
<button onclick="document.location='index.php'">Home</button> 
<button onclick="document.location='admin_settings.php'">Back to admin settings</button>
</div>
