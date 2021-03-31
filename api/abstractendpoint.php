<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Util\Logger;
  use \Util\Utils;
  use \Util\Session;

  abstract class AbstractEndpoint {

    protected static function verify_user($admin = false) {
      if (($admin && Session::is_admin()) || Session::logged_in()) {
        return;
      }
      http_response_code(403);
      die('Unauthorized');
  }

    static function handle_request() {
      self::verify_user();
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
      $content_type = http_response_code() == 200 ? 'application/json' : 'text/plain';
      header("Content-Type: $content_type");
      Logger::log_access();
    }

    protected static function get_body() {
      return file_get_contents('php://input');
    }

    protected static function do_get() {
      $entity_class = static::ENTITY;
      if (isset($_GET['id'])) {
        if ($entity = $entity_class::get_by_id($_GET['id'])) {
          return json_encode($entity);
        } else {
          http_response_code(404);
          return end(explode('\\', $entity_class)).' not found';
        }
      } else {
        return json_encode($entity_class::get_all());
      }
    }

    protected static function do_post(&$body) {
      self::verify_user(true);
      $entity_class = static::ENTITY;
      $arr = json_decode($body, true);
      if (Utils::validate_input($arr, $entity_class::$required_fields_insert)) {
        $new = $entity_class::insert($arr);
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

    protected static function do_put(&$body) {
      self::verify_user(true);
      $entity_class = static::ENTITY;
      $arr = json_decode($body, true);
      if (Utils::validate_input($arr, $entity_class::$required_fields_update)) {
        $new = $entity_class::update($arr);
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

    protected static function do_delete() {
      self::verify_user(true);
      $entity_class = static::ENTITY;
      if (isset($_GET['id'])) {
        if ($entity_class::delete($_GET['id'])) {
          http_response_code(204);
          return;
        } else {
          http_response_code(500);
          return 'Error';
        }
      } else {
        http_response_code(400);
        return 'Bad request: id not specified';
      }
    }
  }
?>