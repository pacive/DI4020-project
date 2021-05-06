<?php
$admin = true;
$init = 'statistics';
$title = 'Statistics';
$head = <<<HTML
<script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
HTML;
require_once('template.php');
?>
<div class="statistics">
  <div>
    <canvas id="browser-chart"></canvas>
  </div>
  <table id="ip-addresses">
    <tr>
      <th>Ip Address</th>
      <th>Number of visits</th>
      <th>Last visit</th>
    </tr>
  </table>
</div>

<div>
<button onclick="document.location='index.php'">Home</button> 
<button onclick="document.location='admin_settings.php'">Back to admin settings</button>
</div>