
<?php
$admin = true;
$init = 'addRoomType';
$title = 'Add new room type';
require_once('template.php');
?>

<div class="main-content">
<form id="newRoomTypeForm" action="api/roomtypes.php" method="POST">
    <label for="newRoomType">Room type name:</label>
    <input type="text" id="newRoomType" name="name">
    <div class="action-buttons">
        <input type="submit" value="Save">
    </div>
    <p id="roomTypeAdded"></p>
</form>
</div>

<div>
    <ul class="nav_white">
        <li><a href="index.php">Home</a></li>
        <li><a href="admin_settings.php">Admin settings</a></li>
        <li><a href="add_room.php">Add a room</a></li>
        <li>Add new room type</li>
    </ul>
</div>
