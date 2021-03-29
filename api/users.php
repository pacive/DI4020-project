<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Model\User;

  class Users extends AbstractEndpoint {

    const ENTITY = '\Model\User';

    static function do_put(&$body) {
      $arr = json_decode($body, true);
      if (Utils::validate_input($arr, User::$required_fields_update)) {
        $new = User::update($arr);
        if ($new instanceof User) {
          return json_encode($new);
        } else {
          http_response_code(500);
          return $new;
        }
      } elseif (isset($arr['id']) && isset($arr['password'])) {
        if (User::set_password($arr)) {
          http_response_code(204);
          return;
        } else {
          http_response_code(500);
          return 'Error updating password';
        }
      } else {
        http_response_code(400);
        return 'Bad request';
      }
    }
  }

  Users::handle_request();
?>