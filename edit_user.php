<?php
require_once('template.php');
?>

<!--list of users with edit button on each

    button for add user-->

    <form>
        <label>Select user:</label>
        <select name="selectUsername" id="selectUsernames">
        </select>
        <label for="text">Edit username:</label>
        <input type="text" id="username" name="username"><br>
        <label for="text">Edit password:</label>
        <input type="text" id="password" name="password"><br>
        <label for="isAdmin">Is admin:</label>
        <input type="checkbox" id="isAdmin" name="isAdmin" value="isAdmin"><br>
        <input type="submit" value="save">
        <input type="button" value="Delete user" onclick="deleteUser()">
    </form>

</div>
</body>
</html>