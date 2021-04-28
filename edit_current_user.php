<?php
$admin = true;
$init = 'updateUser';
require_once('template.php');
?>

    <form id="updateUser" action="api/users.php">
        <label>Select user:</label>
        <select name="id" id="selectUsernames">
        <option value="0">Select user</option>
        </select><br>
        <label for="text">Edit username:</label>
        <input type="text" id="username" name="name"><br>
        <label for="text">Edit password:</label>
        <input type="text" id="password" name="password"><br>
        <label for="isAdmin">Is admin:</label>
        <input type="checkbox" id="isAdmin" name="admin" value="isAdmin"><br>
        <input type="submit" value="save">
        <input type="button" value="Delete user" onclick="deleteUser()">
    </form>

    <p id="userUpdated"></p>

</div>
</body>
</html>