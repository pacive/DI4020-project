<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Model\Type;
  use \Model\DeviceType;
  use \Model\RoomType;
  use \Util\Utils;

  class Types extends AbstractEndpoint {

    private static $mappings = array('device' => '\Model\DeviceType', 'room' => '\Model\RoomType');

    static function do_get() {
      if (isset($_GET['category'])) {
        $entity_class = Utils::get_or_default(static::$mappings, $_GET['category']);
        if ($entity_class) {
          return $entity_class::get_all());
        } else {
          http_response_code(400);
          return 'Invalid value for category';
        }
      } else {
        return json_encode(array('deviceTypes' => DeviceType::get_all(), 'roomTypes' => RoomType::get_all()));
      }
    }

    static function do_post(&$body) {
      $arr = json_decode($body, true);
      if (Utils::validate_input($arr, Type::$required_fields)) {
        $entity_class = Utils::get_or_default(static::$mappings, $arr['category']);
        $new = null;
        if ($entity_class) {
            $new = $entity_class::insert($arr);
        } else {
          http_response_code(400);
          return 'Invalid value for category';
        }
        if ($new instanceof $entity_class) {
          return json_encode($new);
        } else {
          http_response_code(500);
          return $new;
        }
      } else {
        http_response_code(400);
        return 'Bad request';
      }
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