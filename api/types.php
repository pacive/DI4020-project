<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Model\DeviceType;
  use \Model\RoomType;

  class Types extends AbstractEndpoint {

    static function do_get() {
      if (isset($_GET['category'])) {
        switch ($_GET['category']) {
          case 'device':
            return json_encode(DeviceType::get_all());
          case 'room':
            return json_encode(RoomType::get_all());
          default:
            http_response_code(400);
            return 'Invalid value for category';
        } 
      } else {
        return json_encode(array('deviceTypes' => DeviceType::get_all(), 'roomTypes' => RoomType::get_all()));
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

  Types::handle_request();
?>