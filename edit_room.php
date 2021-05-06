<?php
$admin = true;
$init = 'updateRoom';
$title = 'Edit room';
require_once('template.php');
?>

    <form id="updateRoom" action="api/rooms.php">
        <label for="selectRoom">Select Room:</label>
        <select name="id" id="selectRoom">
        <option value="0">Select room</option>
        </select> <br>
        <label for="roomName">Edit room name:</label>
        <input type="text" id="roomName" name="name"><br>
        <label for="roomTypes">Edit room type:</label>
        <select name="typeId" id="roomTypes">
        </select> <br>
        <a id="drawRoomLink" href="#">Edit room coordinates</a><br />
        <input type="submit" value="save">
        <input type="button" value="Delete room" onclick="deleteRoom()"> 
    </form>

    <p id="roomUpdated"></p>
