<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Model\User;
  use \Util\Session;

  class Users extends AbstractEndpoint {

    const ENTITY = '\Model\User';

    static function do_get() {
      if (Session::is_admin() || (isset($_GET['id']) && Session::is_user($_GET['id']))) {
        return parent::do_get();
      } else {
        http_response_code(403);
        return 'Forbidden';
      }
    }

    static function do_put(&$body) {
      $arr = json_decode($body, true);
      if (Utils::validate_input($arr, User::$required_fields_update)) {
        if (!Session::is_admin()) {
          http_response_code(403);
          return 'Forbidden';
        }
        $new = User::update($arr);
        if ($new instanceof User) {
          return json_encode($new);
        } else {
          http_response_code(500);
          return $new;
        }
      } elseif (isset($arr['id']) && isset($arr['password'])) {
        if (!(Session::is_admin() || Session::is_user($arr['id']))) {
          http_response_code(403);
          return 'Forbidden';
        }
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