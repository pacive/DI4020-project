<?php
  namespace Api;
  require_once('../util/autoload.php');

  class Users extends AbstractEndpoint {

    const ENTITY = '\Model\User';

    static function do_put(&$body) {
      http_response_code(501);
    }

    static function do_delete() {
      http_response_code(501);
    }
  }

  Users::handle_request();
?>