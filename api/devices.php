<?php
  namespace Api;
  require_once('../util/autoload.php');
//  use \Util\Utils;

  class Devices extends AbstractEndpoint {

    const ENTITY = '\Model\Device';

    // static function do_post(&$body) {
    //   $arr = json_decode($body, true);
    //   if ($arr && Utils::validate_input($arr, \Model\Device::$required_fields_insert)) {
    //     $new = \Model\Device::insert($arr);
    //     if ($new instanceof \Model\Device) {
    //       echo json_encode($new);
    //     } else {
    //       http_response_code(500);
    //       echo $new;
    //     }
    //   } else {
    //     http_response_code(400);
    //     return 'Bad request';
    //   }
    // }

    static function do_put(&$body) {
      http_response_code(501);
    }

    static function do_delete() {
      http_response_code(501);
    }
  }

  Devices::handle_request();
?>