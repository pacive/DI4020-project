<?php
  namespace Util;

  session_name('SmartHome');
  session_start();

  class Session {
    static function logged_in() {
      return isset($_SESSION['UserId']) && $_SESSION['UserId'] > 0;
    }

    static function user() {
      return isset($_SESSION['UserName']) ? $_SESSION['UserName'] : null;
    }

    static function user_id() {
      return isset($_SESSION['UserId']) ? $_SESSION['UserId'] : null;
    }

    static function is_admin() {
      return isset($_SESSION['IsAdmin']) && (bool) $_SESSION['IsAdmin'];
    }
  }
?>