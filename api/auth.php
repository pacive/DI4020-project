<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Util\Session;

  /*
   * Endpoint for handling authenticatin of users
   */
  class Auth extends AbstractEndpoint {

    /*
     * Overrides handle_request from AbstractEndpoint. Only supports POST
     * requests and doesn't require a user to be logged in
     */
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