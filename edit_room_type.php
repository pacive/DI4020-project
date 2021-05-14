<?php
$admin = true;
$init = 'updateRoomType';
$title = 'Edit room type';
require_once('template.php');
?>

<div class="main-content">
    <form id="updateRoomType" action="api/roomtypes.php">
        <label for="roomTypes">Select room type:</label>
        <select name="typeId" id="roomTypes">
        <option >Select room type</option>
        </select>
        <label for="roomTypeName">New name:</label>
        <input type="text" id="roomTypeName" name="name">
        <input type="submit" value="save">
        <input type="button" value="Delete room type" id="delete"> 
        <p id="roomTypeUpdated"></p> 
    </form>
</div>
<div>
    <ul class="nav_white">
        <li><a href="index.php">Home</a></li>
        <li><a href="admin_settings.php">Admin settings</a></li>
        <li><a href="edit_room.php">Edit a room</a></li>
        <li>Edit room type</li>
    </ul>
</div>
