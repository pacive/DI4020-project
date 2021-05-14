<?php
$admin = true;
$init = 'addUser';
$title = 'Add new user';
require_once('template.php');
?> 

<div class="main-content">
<form id="addUser" action="api/users.php" method="POST">
    <label for="username">Username:</label>
    <input type="text" id="username" name="name">
    <label for="password">Password:</label>
    <input type="text" id="password" name="password">
    <label for="isAdmin">Is admin:
        <input type="checkbox" id="isAdmin" name="admin" value="isAdmin">
    </label>
    <div class="action-buttons">
        <input type="submit" value="Save">
    </div>
    <p id="userAdded"></p>
</form>

</div>

<div>
    <ul class="nav_white">
        <li><a href="index.php">Home</a></li>
        <li><a href="admin_settings.php">Admin settings</a></li>
        <li>Add new user</li>
    </ul>
</div>
