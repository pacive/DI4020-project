
<?php
$admin = true;
$init = 'addRoomType';
$title = 'Add new room type';
require_once('template.php');
?> 

<div class="main-content">
<form id="newRoomTypeForm" action="api/roomtypes.php" method="POST">
    <input type="text" id="newRoomType" name="name" placeholder="new room type"> 
    <input type="submit" value="save"> 
    <p id="roomTypeAdded"></p>
</form>
</div>

<div>
    <ul class="nav_white">
        <li><a href="index.php">Home</a></li>
        <li><a href="admin_settings.php">Admin settings</a></li>
        <li><a href="edit_room.php">Edit a room</a></li>
        <li>Add new room type</li>
    </ul>
</div>
