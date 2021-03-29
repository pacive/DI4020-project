<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Util\Logger;

  abstract class AbstractEndpoint {

    static $entity_class;

    static function verify_user($admin = true) {

    }

    static function handle_request() {
      self::$entity_class = static::ENTITY;
      switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
          echo static::do_get();
          break;
        case 'POST':
          $body = self::get_body();
          echo static::do_post($body);
          break;
        case 'PUT':
          $body = self::get_body();
          echo static::do_put($body);
          break;
        case 'DELETE':
          echo static::do_delete();
          break;
        default:
          http_response_code(405);
          echo 'Method not supported';
          break;
      }
      Logger::log_access();
    }

    static function get_body() {
      return file_get_contents('php://input');
    }

    static function do_get() {
      if (isset($_GET['id'])) {
        if ($entity = call_user_func(static::ENTITY.'::get_by_id', $_GET['id'])) {
          return json_encode($entity);
        } else {
          http_response_code(404);
          return static::ENTITY.' not found';
        }
      } else {
        return json_encode(call_user_func(static::ENTITY.'::get_all'));
      }
    }
    abstract static function do_post(&$body);
    abstract static function do_put(&$body);
    abstract static function do_delete();
  }
?>