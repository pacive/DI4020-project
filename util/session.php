<?php
  namespace Util;

  /*
   * Handles the session and user authentication
   */
  class Session {

    const TIMEOUT = 1800;
    const REFRESH_INTERVAL = 900;

const SQL_VERIFY_USER = <<<SQL
SELECT UserName as user, Password as pwd, UserId as id, IsAdmin as admin 
FROM Users WHERE UserName = ?;
SQL;

    private static $validated;

    /*
     * Check if the session is valid, i.e. all required session varables are set
     * and the session hasn't timed out. Caches the result for subsequent checks
     */
    private static function validate() {
      if (!isset($validated)) {
        if (self::expired()) {
          self::logout();
        }
        self::$validated = isset($_SESSION['UserId'], $_SESSION['UserName'], $_SESSION['IsAdmin'], $_SESSION['Timestamp']);
      }
      return self::$validated;
    }

    /*
     * Checks if the session is valid, and refreshes it if necessary
     */
    static function logged_in() {
      if (self::validate()) {
        self::refresh_session();
      };
      return self::validate();
    }

    /*
     * Gets the username of the logged in user, or null if none is logged in
     */
    static function user() {
      return self::validate() ? $_SESSION['UserName'] : null;
    }

    /*
     * Gets the userId of the logged in user, or null if none is logged in
     */
    static function user_id() {
      return self::validate() ? $_SESSION['UserId'] : null;
    }

    /*
     * Gets whether the logged in user is an admin
     */
    static function is_admin() {
      return self::validate() && (bool) $_SESSION['IsAdmin'];
    }

    /*
     * Gets the timestamp of when the user was logged in, or the last time
     * the session was refreshed
     */
    static function timestamp() {
      if (isset($_SESSION['Timestamp'])) {
        return (int) $_SESSION['Timestamp'];
      } else {
        return 0;
      }
    }

    /*
     * Checks if the session has expired
     */
    private static function expired() {
      return self::timestamp() < time() - self::TIMEOUT;
    }

    /*
     * Refreshes the session by regenerating the session id and updates the timestamp
     */
    private static function refresh_session() {
      if (self::timestamp() < time() - self::REFRESH_INTERVAL) {
        $_SESSION['Timestamp'] = time();
        session_regenerate_id();
      }
    }

    /*
     * Checks it the logged in user has the specified user id
     */
    static function is_user($id) {
      return self::user_id() == $id;
    }

    /*
     * Checks if the provided username and password is valid, and logs in the user
     */
    static function login($username, $password) {
      $query = DB::get_connection()->prepare(self::SQL_VERIFY_USER);
      $query->bind_param('s', $username);
      $query->execute();
      $result = $query->get_result();
      if ($row = $result->fetch_object()) {
        if (password_verify($password, $row->pwd)) {
          session_regenerate_id();
          $_SESSION['UserName'] = $row->user;
          $_SESSION['UserId'] = (int) $row->id;
          $_SESSION['IsAdmin'] = (bool) $row->admin;
          $_SESSION['Timestamp'] = time();
          return true;
        }
      }
      return false;
    }

    /*
     * Logs out the user by clearing the session variables
     */
    static function logout() {
      $_SESSION = array();
    }
  }

  
  /*
   * Initialize the session
   */
  session_name('SmartHome');
  session_start();
?>