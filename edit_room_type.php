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
        </select> <br>
        <label for="roomTypeName">Edit room type name:</label>
        <input type="text" id="roomTypeName" name="name"><br>
        <input type="submit" value="save">
        <input type="button" value="Delete room type" id="delete"> 
        <p id="roomTypeUpdated"></p> 

    </form>

</div>
    <div class="nav">
    <button onclick="document.location='index.php'">Home</button> 
    <button onclick="document.location='admin_settings.php'">Back to admin settings</button>
    </div>