<?php
require_once('util/autoload.php');
use \Util\Session;
use \Util\Logger;

header('Location: login.php');

Logger::log_access();
Session::logout();
?>