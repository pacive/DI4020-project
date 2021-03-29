<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Model\Device;

  class Devices extends AbstractEndpoint {

    const ENTITY = '\Model\Device';

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

  Devices::handle_request();
?>