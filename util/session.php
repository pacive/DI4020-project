<?php
  namespace Util;
  use \DateTime;
  use \DateInterval;

  class Session {

    const TIMEOUT = 'PT2H';

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
        return (new DateTime)->setTimestamp((int) $_SESSION['Timestamp']);
      } else {
        return (new DateTime)->setTimestamp(0);
      }
    }

    static function expired() {
      return self::timestamp()->add(new DateInterval(self::TIMEOUT)) < new DateTime();
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
          $_SESSION['Timestamp'] = (new DateTime)->getTimestamp();
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