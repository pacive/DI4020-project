<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Util\Session;

  class Auth extends AbstractEndpoint {
    
    static function handle_request() {
      if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        http_response_code(405);
        die();
      }

      $body = json_decode(self::get_body());
      
      if ($body && Session::login($body->username, $body->password)) {
        http_response_code(204);
      } else {
        http_response_code(401);
      }
    }
  }

  Auth::handle_request();
?>