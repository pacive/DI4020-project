<?php
  namespace Util;
  class Logger {
    static function log_access() {
      echo '<br />'.$_SERVER['REQUEST_METHOD'];
      echo '<br />'.$_SERVER['REQUEST_URI'];
      echo '<br />'.http_response_code();
      if (isset($_SESSION['UserId'])) {
        echo '<br />'.$_SESSION['UserName'];
      } else {
        echo '<br />'.-1;
      }
    }
  }