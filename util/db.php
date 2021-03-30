<?php
  namespace Util;

  class DB {

    private static $mysql_host = 'localhost';
    private static $mysql_user = 'andalf20';
    private static $mysql_pwd = 'lIKQfkgyLj';
    private static $mysql_db = 'andalf20_db';
    
    private static $conn;

    static function get_connection() {
      if (!isset(self::$conn)) {
        self::$conn = new \mysqli(self::$mysql_host, self::$mysql_user, self::$mysql_pwd, self::$mysql_db);
        if (self::$conn->connect_errno) {
          http_response_code(503);
          header('Retry-After: 30');
          die(self::$conn->connect_error);
        }
      }
      return self::$conn;
    }
  }
?>