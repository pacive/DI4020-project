<?php
$admin = true;
$init = 'addRoom';
$title = 'Add new room';
require_once('template.php');
?>

<div class="main-content">
<form id="addRoom" action="api/rooms.php" method="POST">
    <label for="roomname">Roomname:</label>
    <input type="text" id="roomname" name="name">

    <label for="roomTypes">Select room type:</label>
    <select name="typeId" id="roomTypes" >
    <option >Select room type</option>
    </select>
    <a href="add_room_type.php">Add a new room type</a>
    <div class="action-buttons">
        <input type="submit" value="Save">
    </div>
    <p id="roomAdded"></p>
</form>
</div>

<div>
    <ul class="nav_white">
        <li><a href="index.php">Home</a></li>
        <li><a href="admin_settings.php">Admin settings</a></li>
        <li>Add new room</li>
    </ul>
</div>
