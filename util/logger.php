<?php
  namespace Util;
  require_once('preventaccess.php');

  /*
   * Handles logging of requests
   */
  class Logger {

const SQL_LOG_ACCESS = <<<SQL
INSERT INTO AccessLog (Page, RequestType, ResponseCode, UserId, IpAddress, UserAgent)
VALUES (?, ?, ?, ?, ?, ?);
SQL;

    /*
     * Log the request method, uri, response code and user to the database
     */
    static function log_access() {
      $query = DB::get_connection()->prepare(self::SQL_LOG_ACCESS);
      $response_code = http_response_code();
      $user_id = Session::user_id();
      $query->bind_param('ssiiss', $_SERVER['PHP_SELF'], $_SERVER['REQUEST_METHOD'], $response_code, $user_id, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
      $query->execute();
    }
  }