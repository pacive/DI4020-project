<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Model\Device;

  class Status extends AbstractEndpoint {

    const ENTITY = '\Model\Device';

    static function do_get() {
      if (isset($_GET['id'])) {
        if ($device = Device::get_by_id($_GET['id'])) {
          return json_encode($device);
        } else {
          http_response_code(404);
          return 'Device not found';
        }
      } else {
        http_response_code(400);
        return 'Device id must be set';
      }
    }

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

  Status::handle_request();
?>