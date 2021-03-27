<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Model\Device;

  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      echo json_encode(Device::get_all());
      break;
    case 'POST':
      $body = file_get_contents('php://input');
      echo add_device($body);
      break;
    case 'PUT':
      $body = file_get_contents('php://input');
      echo update_device($body);
      break;
    case 'DELETE':
      $body = file_get_contents('php://input');
      echo delete_device($body);
      break;
    default:
      http_response_code(405);
      echo 'Method not supported';
      break;
  }
  \Util\Logger::log_access();
?>