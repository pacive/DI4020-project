
<?php
$admin = true;
$init = 'addRoomType';
$title = 'Add new room type';
require_once('template.php');
?> 

<form id="newRoomTypeForm" action="api/roomtypes.php" method="POST">
    <input type="text" id="newRoomType" name="name" placeholder="new room type"> 
    <input type="submit" value="save"> 
    <p id="roomTypeAdded"></p>
</form>

<div>
<button onclick="document.location='index.php'">Home</button> 
<button onclick="document.location='admin_settings.php'">Back to admin settings</button>
</div>
