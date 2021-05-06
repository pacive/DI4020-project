<?php
$admin = true;
$init = 'updateRoomType';
$title = 'Edit room type';
require_once('template.php');
?>

    <form id="updateRoomType" action="api/roomtypes.php">
        <label for="roomTypes">Select room type:</label>
        <select name="roomTypeId" id="roomTypes">
        <option value="0">Select room type</option>
        </select> <br>
        <label for="roomTypeName">Edit room type name:</label>
        <input type="text" id="roomTypeName" name="name"><br>
        <input type="submit" value="save">
        <input type="button" value="Delete room type" onclick="deleteRoomType()"> 
    </form>

    <p id="roomTypeUpdated"></p>

    <div>
    <button onclick="document.location='index.php'">Home</button> 
    <button onclick="document.location='admin_settings.php'">Back to admin settings</button>
    </div>