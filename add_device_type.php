
<?php
$admin = true;
$init = 'addDeviceType'; 
$title = 'Add new device type';
require_once('template.php');
?> 

<div class="main-content">
<form id="newDeviceTypeForm" action="api/devicetypes.php" method="POST">
    <input type="text" id="newDeviceType" name="name" placeholder="new device type"> 
    <input type="submit" value="save"> 
    <p id="deviceTypeAdded"></p>
</form>
</div>

<div class="nav">
<button onclick="document.location='index.php'">Home</button> 
<button onclick="document.location='admin_settings.php'">Back to admin settings</button>
</div>
