<?php
$admin = true;
$init = 'updateRoom';
$title = 'Edit room';
require_once('template.php');
?>

<div class="main-content">
    <form id="updateRoom" action="api/rooms.php">
        <label for="selectRoom">Select Room:</label>
        <select name="id" id="selectRoom">

        <option value="">Select room</option>
        </select> <br>
        <label for="roomName">Edit room name:</label>
        <input type="text" id="roomName" name="name"><br>
        <label for="roomTypes">Change room type:</label>
        <select name="typeId" id="roomTypes">
        </select> <br>
        <a id="drawRoomLink" href="#">Edit room coordinates</a><br />
        <input type="submit" value="save">
        <input type="button" value="Delete room" id="delete"> <br>
        <br>
        <label> Edit an existing room type here: </label>
        <input type="button" value="Edit room type" onclick="document.location='edit_room_type.php'">
        <p id="roomUpdated"></p>

    </form>

</div>

<div>
    <ul class="nav_white">
        <li><a href="index.php">Home</a></li>
        <li><a href="admin_settings.php">Admin settings</a></li>
        <li>Edit a room</li>
    </ul>
</div>
