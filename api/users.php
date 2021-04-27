<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Model\User;
  use \Util\Session;
  use \Util\Utils;

  /*
   * Endpoint for handling CRUD operations for room types
   */
  class Users extends AbstractEndpoint {

    const ENTITY = '\Model\User';

    /*
     * Handler for GET requests. Only allows getting information for the logged in user
     * unless the user is an admin.
     */
    static function do_get() {
      if (Session::is_admin() || (isset($_GET['id']) && Session::is_user($_GET['id']))) {
        return parent::do_get();
      } else {
        http_response_code(403);
        return 'Forbidden';
      }
    }

    /*
     * Handler for PUT requests. Only allows setting information if the user is an admin.
     * Regular users may only update their own password.
     */
    static function do_put(&$body) {
      $arr = json_decode($body, true);
      $return;
      // Updates a user (excluding password)
      if (Utils::validate_input($arr, User::$required_fields_update)) {
        // Immediately return a 403 response unless user is an admin
        // or if user tries to update fields other than own password
        if (!Session::is_admin() && 
           (!Session::is_user($arr['id']) ||
            isset($arr['name']) ||
            isset($arr['admin']))) {
          http_response_code(403);
          return 'Forbidden';
        }
        $new = User::update($arr);
        if ($new instanceof User) {
          $newArr = $new->to_array();
          $newArr['event'] = 'updated';
          return json_encode($newArr);
        } else {
          http_response_code(500);
          return $new;
        }
      }
    }
  }

  Users::handle_request();
?>