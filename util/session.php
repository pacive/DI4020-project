<?php
  namespace Util;

  class Session {

    const TIMEOUT = 1800;
    const REFRESH_INTERVAL = 900;

const SQL_VERIFY_USER = <<<SQL
SELECT UserName as user, Password as pwd, UserId as id, IsAdmin as admin 
FROM Users WHERE UserName = ?;
SQL;

    private static $validated;

    private static function validate() {
      if (!isset($validated)) {
        if (self::expired()) {
          self::logout();
        }
        self::$validated = isset($_SESSION['UserId'], $_SESSION['UserName'], $_SESSION['IsAdmin'], $_SESSION['Timestamp']);
      }
      return self::$validated;
    }

    static function logged_in() {
      if (self::validate()) {
        self::refresh_session();
      };
      return self::validate();
    }

    static function user() {
      return self::validate() ? $_SESSION['UserName'] : null;
    }

    static function user_id() {
      return self::validate() ? $_SESSION['UserId'] : null;
    }

    static function is_admin() {
      return self::validate() && (bool) $_SESSION['IsAdmin'];
    }

    static function timestamp() {
      if (isset($_SESSION['Timestamp'])) {
        return (int) $_SESSION['Timestamp'];
      } else {
        return 0;
      }
    }

    private static function expired() {
      return self::timestamp() < time() - self::TIMEOUT;
    }

    private static function refresh_session() {
      if (self::timestamp() < time() - self::REFRESH_INTERVAL) {
        $_SESSION['Timestamp'] = time();
        session_regenerate_id();
      }
    }

    static function is_user($id) {
      return self::user_id() == $id;
    }

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

    static function logout() {
      $_SESSION = array();
    }
  }

  session_name('SmartHome');
  session_start();
?>