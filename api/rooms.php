<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Model\Room;

  class Rooms extends AbstractEndpoint {

    const ENTITY = '\Model\Room';

    static function do_post(&$body) {
      http_response_code(501);
    }

    static function do_put(&$body) {
      http_response_code(501);
    }

    static function do_delete() {
      http_response_code(501);
    }
  }

  Rooms::handle_request();
?>