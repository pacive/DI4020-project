
<?php
$admin = true;
$init = 'addRoom'; // fixa denhär funktionen
$title = 'Add new room';
require_once('template.php');
?> 

<form id="newRoomTypeForm" action="api/roomtypes.php" method="POST" style="display: none">
    <span> add new room type here:</span>
    <input type="text" id="newRoomType" name="name" placeholder="new room type"> 
    <input type="submit" value="add">
    <p id="roomTypeAdded"></p>
</form>

<form id="addRoom" action="api/rooms.php" method="POST">
    <label for="roomname">Roomname:</label>
    <input type="text" id="roomname" name="name"><br>

    <label for="roomTypes">Select room type:</label>
    <select name="typeId" id="roomTypes">
    <option value="0">Add new room type</option>
    </select>
    
    <br>
    <input type="submit" value="Save">
</form>

<p id="roomAdded"></p>

