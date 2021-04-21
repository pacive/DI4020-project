<?php
require_once('template.php');
?>

<form id="new_user" onsubmit="/* add user funciton */">
    <label for="text">Username:</label>
    <input type="text" id="username" name="username"><br>
    <label for="text">Password:</label>
    <input type="text" id="password" name="password"><br>
    <label for="isAdmin">Is admin:</label>
    <input type="checkbox" id="isAdmin" name="isAdmin" value="isAdmin"><br>
    <input type="submit" value="save">
</form>

</div>
</body>
</html>