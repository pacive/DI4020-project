<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Model\Device;
  use \Util\Utils;

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
      $arr = json_decode($body, true);
      if (Utils::validate_input($arr, array('id', 'status'))) {
        if (Device::set_device_status($arr)) {
          http_response_code(204);
          return;
        } else {
          http_response_code(500);
          return 'Error setting status';
        }
      } else {
        http_response_code(400);
        return 'Bad request';
      }
    }

    static function do_put(&$body) {
      http_response_code(405);
    }

    static function do_delete() {
      http_response_code(405);
    }
  }

  Status::handle_request();
?>