<?php
$admin = true;
$title = 'Settings';
require_once('template.php');
?>

<!-- maybe we could have these in three columns? -->

<h3>Users</h3>
<button> <a href="edit_user.php"> Edit a user </a></button> 
<button> <a href="add_user.php"> Add new user </a> </button>

<h3>Devices</h3>
<button> <a href="edit_device.php"> Edit a device </a></button> 
<button> <a href="add_device.php"> Add new device </a> </button>

<h3>Rooms</h3>
<button> <a href="edit_room.php"> Edit a room </a> </button>
<button> <a href="add_room.php"> Add new room </a> </button>

<button> <a href="index.php"> Home </a> </button>

