<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Util\Session;
  use \Model\User;

  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      if (isset($_GET['id'])) {
        echo json_encode(User::get($_GET['id']));
      } else {
        echo json_encode(User::get_all());
      }
      break;
    case 'POST':
      $body = file_get_contents('php://input');
      echo add_user($body);
      break;
    case 'PUT':
      $body = file_get_contents('php://input');
      echo update_user($body);
      break;
    case 'DELETE':
      $body = file_get_contents('php://input');
      echo delete_user($body);
      break;
    default:
      http_response_code(405);
      echo 'Method not supported';
      break;
  }
  \Util\Logger::log_access();
?>