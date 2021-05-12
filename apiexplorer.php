<?php
$admin = true;
$init = 'apiExplorer';
$title = 'API Explorer';
require_once('template.php');
?>

<div class="main-content">
<div class="apiexplorer">
  <form id="apiexplorer">
    <div class="apiexplorer-controls">
      <select name="method" id="method">
        <option value="GET">GET</option>
        <option value="PUT">PUT</option>
        <option value="POST">POST</option>
        <option value="DELETE">DELETE</option>
      </select>
      <select name="endpoint" id="endpoint">
        <option value="devices">devices.php</option>
        <option value="rooms">rooms.php</option>
        <option value="status">status.php</option>
        <option value="users">users.php</option>
        <option value="devicetypes">devicetypes.php</option>
        <option value="roomtypes">roomtypes.php</option>
      </select>
      <input type="text" name="query" id="query" placeholder="?param=value" />
      <input type="submit" value="Send" />
      <input type="reset" value="Clear" />
    </div>
    <div class="apiexplorer-body">
      <textarea name="body" id="body"></textarea>
    </div>
  </form>
  <div class="apiexplorer-result">
    <textarea name="result" id="result"></textarea>
  </div>
  </div>
</div>
<div class="nav">
<button onclick="document.location='index.php'">Home</button> 
<button onclick="document.location='admin_settings.php'">Back to admin settings</button>
</div>
