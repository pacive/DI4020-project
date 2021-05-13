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

        <option value="0">Select room</option>
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
    </form>

    <p id="roomUpdated"></p>
</div>

    <div class="nav">
    <button onclick="document.location='index.php'">Home</button> 
    <button onclick="document.location='admin_settings.php'">Back to admin settings</button>
    </div>
