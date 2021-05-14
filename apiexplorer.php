<?php
$admin = true;
$init = 'apiExplorer';
$title = 'API Explorer';
require_once('template.php');
?>

<div class="main-content apiexplorer">
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
      <input type="text" name="id" id="id" placeholder="&lt;id&gt;" />
        <label id="inclDevices" for="includeDevices">Incl devices:
          <input type="checkbox" name="includeDevices" id="includeDevices" />
        </label>
      <input type="submit" value="Send" />
      <input type="reset" id="reset" value="Clear" />
    </div>
    <div class="apiexplorer-body">
      <textarea name="body" id="body"></textarea>
    </div>
    <div class="apiexplorer-result">
      <pre id="result"></pre>
    </div>
  </form>
</div>
<div>
    <ul class="nav_white">
        <li><a href="index.php">Home</a></li>
        <li><a href="admin_settings.php">Admin settings</a></li>
        <li>API explorer</li>
    </ul>
</div>