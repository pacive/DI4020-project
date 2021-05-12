<?php
  namespace Api;
  require_once('../util/preventaccess.php');
  use \Util\Logger;
  use \Util\Utils;
  use \Util\Session;

  define('DEBUG', true);

  /*
   * Abstract base class for the different api endpoints
   */
  abstract class AbstractEndpoint {

    /*
     * Verifies that the user is properly logged in, and optionally that the user is an admin.
     * If not, terminates the script and returns a 403 response code.
     */
    protected static function verify_user($admin = false) {
      if (($admin && Session::is_admin()) || Session::logged_in()) {
        return;
      }
      http_response_code(403);
      Logger::log_access();
      exit($_SERVER['REQUEST_METHOD'] == 'HEAD' ? 0 : 'Unauthorized');
    }

    /*
     * Dispatches the request to different functions depenting on the request method.
     * prints the result to the output stream, and logs the access.
     */
    static function handle_request() {
      self::verify_user();
      $response;
      try {
        switch ($_SERVER['REQUEST_METHOD']) {
          case 'GET':
            $response = static::do_get();
            break;
          case 'POST':
            $body = self::get_body();
            $response = static::do_post($body);
            break;
          case 'PUT':
            $body = self::get_body();
            $response = static::do_put($body);
            break;
          case 'DELETE':
            $response = static::do_delete();
            break;
          default:
            http_response_code(405);
            $response = 'Method not supported';
            break;
        }
      } catch (\Exception $e) {
        http_response_code(500);
        $response = $e->getMessage();
      } finally {
        $content_type = http_response_code() == 200 ? 'application/json' : 'text/plain';
        header("Content-Type: $content_type");
        Logger::log_access();
        if (http_response_code() == 200 || DEBUG) {
          echo $response;  
        }
      }
    }

    /*
     * Gets the request's body for PUT and POST requests
     */
    protected static function get_body() {
      return file_get_contents('php://input');
    }

    /*
     * Generic handler for a GET request. Gets the corresponding entity from the db
     * and returns it json-encoded, or all entities if no id is provided in the query
     */
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

    /*
     * Generic handler for a POST request. Inserts the provided json-object into the db
     * and returns the newly inserted entity
     */
    protected static function do_post(&$body) {
      self::verify_user(true);
      $entity_class = static::ENTITY;
      $arr = json_decode($body, true);
      if (Utils::validate_input($arr, $entity_class::$required_fields_insert)) {
        $new = $entity_class::insert($arr);
        if ($new instanceof $entity_class) {
          $newArr = $new->to_array();
          $newArr['event'] = 'created';
          return json_encode($newArr);
        } else {
          http_response_code(500);
          return $new;
        }
      } else {
        http_response_code(400);
        return 'Bad request';
      }
    }

    /*
     * Generic handler for a PUT request. Updates the entity with the id provided in the 
     * json-object into the db and returns the newly updated entity
     */
    protected static function do_put(&$body) {
      self::verify_user(true);
      $entity_class = static::ENTITY;
      $arr = json_decode($body, true);
      if (Utils::validate_input($arr, $entity_class::$required_fields_update)) {
        $new = $entity_class::update($arr);
        if ($new instanceof $entity_class) {
          $newArr = $new->to_array();
          $newArr['event'] = 'updated';
          return json_encode($newArr);
        } else {
          http_response_code(500);
          return $new;
        }
      } else {
        http_response_code(400);
        return 'Bad request';
      }
    }

    /*
     * Generic handler for a DELETE request. Deletes the corresponding entity from the db.
     * Returns a 204 response code on success
     */
    protected static function do_delete() {
      self::verify_user(true);
      $entity_class = static::ENTITY;
      if (isset($_GET['id'])) {
        if ($entity_class::delete($_GET['id'])) {
          return json_encode(array('id' => $_GET['id'], 'event' => 'deleted'));
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