<?php 
  $init = 'login';
  require_once('template.php'); 
?>
<div>
    <h2 class="head">Welcome!</h2>
    <p>Please enter credentials bellow</p>
</div>
<div class="main-content">

<form id="login" name="login" action="api/auth.php" method="POST">
  <input type="text" id="username" name="username" placeholder="Username" />
  <input type="password" id="password" name="password" placeholder="Password" />
  <input type="submit" id="submit" name="submit" value="Login" />
</form>
</div>