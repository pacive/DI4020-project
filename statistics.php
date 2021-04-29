<?php
$admin = true;
$init = 'statistics';
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