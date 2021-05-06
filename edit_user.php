<?php
$admin = true;
$init = 'updateUser';
$title = 'Edit users';
require_once('template.php');
?>

    <form id="updateUser" action="api/users.php">
        <label for="selectUsername">Select user:</label>
        <select name="id" id="selectUsername">
        <option value="0">Select user</option>
        </select><br>
        <label for="username">Edit username:</label>
        <input type="text" id="username" name="name"><br>
        <label for="password">Edit password:</label>
        <input type="password" id="password" name="password"><br>
        <label for="isAdmin">Is admin:</label>
        <input type="checkbox" id="isAdmin" name="admin" value="isAdmin"><br>
        <input type="submit" value="save">
        <input type="button" value="Delete user" onclick="deleteUser()">
    </form>

    <p id="userUpdated"></p> 

    
    <div>
    <button onclick="document.location='index.php'">Home</button> 
    <button onclick="document.location='admin_settings.php'">Back to admin settings</button>
    </div>