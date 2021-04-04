<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Model\Device;
  use \Util\Utils;

  /*
   * Endpoint for getting and setting device status
   */
  class Status extends AbstractEndpoint {

    /*
     * Handler for GET requests. Must include id in the query string. Gets the status
     * for the corresponding device
     */
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

    /*
     * Handler for POST requests. Sets the status for the corresponding device
     */
    static function do_post(&$body) {
      $arr = json_decode($body, true);
      if (Utils::validate_input($arr, array('id', 'status'))) {
        if (Device::set_device_status($arr['id'], $arr['status'])) {
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

    /*
     * Handler for PUT requests. Not supported for this endpoint, so returns a 405 response
     */
    static function do_put(&$body) {
      http_response_code(405);
    }

    /*
     * Handler for DELETE requests. Not supported for this endpoint, so returns a 405 response
     */
    static function do_delete() {
      http_response_code(405);
    }
  }

  Status::handle_request();
?>