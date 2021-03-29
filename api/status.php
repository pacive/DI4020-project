<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Model\Device;

  class Status extends AbstractEndpoint {

    static function do_get() {
      if (isset($_GET['id'])) {
        if ($status = Device::get_device_status($_GET['id'])) {
          return json_encode($status);
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