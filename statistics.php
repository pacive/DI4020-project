<?php
$admin = true;
$init = 'statistics';
$title = 'Statistics';
$head = <<<HTML
<script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
HTML;
require_once('template.php');
?>
<div class="main-content">
<div class="selectPeriod">
  <select id="selectPeriod">
    <option value="all">All</option>
    <option value="1d">Last day</option>
    <option value="1w">Last week</option>
    <option value="2w">Last 2 weeks</option>
    <option value="1m">Last month</option>
    <option value="3m">Last 3 months</option>
    <option value="6m">Last 6 months</option>
    <option value="1y">Last year</option>
  </select>
</div>
<div class="statistics">
  <div>
    <canvas id="browser-chart"></canvas>
  </div>
  <table id="ip-addresses">
    <thead>
    <tr>
      <th>Ip Address</th>
      <th>Number of visits</th>
      <th>Last visit</th>
    </tr>
    </thead>
    <tbody>

    </tbody>
  </table>
</div>
</div>
<div>
    <ul class="nav_white">
        <li><a href="index.php">Home</a></li>
        <li><a href="admin_settings.php">Admin settings</a></li>
        <li>Statistics</li>
    </ul>
</div>