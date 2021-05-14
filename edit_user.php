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
        </select>
        <label for="username">Edit username:</label>
        <input type="text" id="username" name="name">
        <label for="password">Edit password:</label>
        <input type="password" id="password" name="password">
        <label for="isAdmin">Is admin:
            <input type="checkbox" id="isAdmin" name="admin" value="isAdmin">
        </label>
        <div class="action-buttons">
            <input type="submit" value="Save">
            <input type="button" value="Delete user" id="delete">
        </div>
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