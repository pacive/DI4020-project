<?php
$admin = true;
$init = 'updateUser';
$title = 'Edit users';
require_once('template.php');
?>

<div class="main-content">
    <form id="updateUser" action="api/users.php">
        <label for="selectUsername">Select user:</label>
        <select name="id" id="selectUsername">
        <option>Select user</option>
        </select><br>
        <label for="username">Edit username:</label>
        <input type="text" id="username" name="name"><br>
        <label for="password">Edit password:</label>
        <input type="password" id="password" name="password"><br>
        <label for="isAdmin">Is admin:</label>
        <input type="checkbox" id="isAdmin" name="admin" value="isAdmin"><br>
        <input type="submit" value="save">
        <input type="button" value="Delete user" id="delete">
        <p id="userUpdated"></p> 

    </form>

</div>
    
<div>
    <ul class="nav_white">
        <li><a href="index.php">Home</a></li>
        <li><a href="admin_settings.php">Admin settings</a></li>
        <li>Edit user</li>
    </ul>
</div>