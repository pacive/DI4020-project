<?php
  namespace Util;

  session_name('SmartHome');
  session_start();

  class Session {
    static function logged_in() {
      return isset($_SESSION['UserId']) && $_SESSION['UserId'] > 0;
    }

    static function user() {
      if (isset($_SESSION['UserName'])) {
        return $_SESSION['UserName'];
      } else {
        return null;
      }
    }

    static function user_id() {
      if (isset($_SESSION['UserId'])) {
        return $_SESSION['UserId'];
      } else {
        return -1;
      }
    }
    static function is_admin() {
      return isset($_SESSION['IsAdmin']) && (bool) $_SESSION['IsAdmin'];
    }
  }
?>