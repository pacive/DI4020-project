
<?php
$admin = true;
$init = 'addUser'; //anger vilken funktion i INIT-objektet i app.js ska köras när sidan laddas
$title = 'Add new user';
require_once('template.php');
?> 


<form id="addUser" action="api/users.php" method="POST">
    <label for="username">Username:</label>
    <input type="text" id="username" name="name"><br>
    <label for="password">Password:</label>
    <input type="text" id="password" name="password"><br>
    <label for="isAdmin">Is admin:</label>
    <input type="checkbox" id="isAdmin" name="admin" value="isAdmin"><br>
    <input type="submit" value="Save">
</form>

<p id="userAdded"></p>


