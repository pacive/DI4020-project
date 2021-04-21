<?php
$admin = true;
$init = 'editUsers';
require_once('template.php');
?>

<!--list of users with edit button on each

    button for add user-->

    <form id="edit_user", action="api/users.php" method="PUT">
        <label>Select user:</label>
        <select name="id" id="selectUsernames">
        </select>
        <label for="text">Edit username:</label>
        <input type="text" id="username" name="name"><br>
        <label for="text">Edit password:</label>
        <input type="text" id="password" name="password"><br>
        <label for="isAdmin">Is admin:</label>
        <input type="checkbox" id="isAdmin" name="admin"><br>
        <input type="submit" value="save">
        <input type="button" value="Delete user" onclick="deleteUser()">
    </form>

</div>
</body>
</html>
