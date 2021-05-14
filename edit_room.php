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
        </select>
        <label for="roomName">Edit room name:</label>
        <input type="text" id="roomName" name="name">
        <label for="roomTypes">Change room type:</label>
        <select name="typeId" id="roomTypes">
        <option value="0">Select room type</option>
        </select>
        <a href="edit_room_type.php">Edit an existing room type</a>
        <a id="drawRoomLink" href="#">Edit room coordinates</a><br />
        <div class="action-buttons">
            <input type="submit" value="Save">
            <input type="button" value="Delete room" id="delete">
        </div>
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
