<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Model\Room;

  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      echo json_encode(Room::get_all());
      break;
    case 'POST':
      $body = file_get_contents('php://input');
      echo add_room($body);
      break;
    case 'PUT':
      $body = file_get_contents('php://input');
      echo update_room($body);
      break;
    case 'DELETE':
      $body = file_get_contents('php://input');
      echo delete_room($body);
      break;
    default:
      http_response_code(405);
      echo 'Method not supported';
      break;
  }
  \Util\Logger::log_access();
?>