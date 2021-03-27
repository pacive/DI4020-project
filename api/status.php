<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Model\Device;

  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      echo get_device_status();
      break;
    case 'POST':
      $body = file_get_contents('php://input');
      echo set_device_status($body);
      break;
    default:
      http_response_code(405);
      echo 'Method not supported';
      break;
  }
  \Util\Logger::log_access();
?>