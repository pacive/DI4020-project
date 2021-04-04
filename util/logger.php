<?php
  namespace Util;

  /*
   * Handles logging of requests
   */
  class Logger {

    /*
     * TODO
     * 
     * Log the request method, uri, response code and user to the database
     */
    static function log_access() {
      return;
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