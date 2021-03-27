<?php
  namespace Model;
  abstract class DBObject {

    const MYSQL_HOST = 'localhost';
    const MYSQL_USER = 'andalf20';
    const MYSQL_PWD = 'lIKQfkgyLj';
    const MYSQL_DB = 'andalf20_db';

    private static $conn = null;

    protected static function get_connection() {
      if (self::$conn == null) {
        self::$conn = new \mysqli(self::MYSQL_HOST, self::MYSQL_USER, self::MYSQL_PWD, self::MYSQL_DB);
      }
      return self::$conn;
    }

    protected static function db_get_all($class) {
      $db = self::get_connection();
      return $db->query($class::SQL_GET_ALL);
    }

    static function get_all() {
      $class = get_called_class();
      $result = self::db_get_all($class);
      $objects = array();
      while ($row = $result->fetch_row()) {
        $row = array_pad($row, 5, null);
        $objects[] = new $class($row[0], $row[1], $row[2], $row[3], $row[4]);
      }
      return $objects;
    }

    protected static function db_get_by_id($class, $id) {
      $query = self::get_connection()->prepare($class::SQL_GET);
      $query->bind_param('i', $id);
      $query->execute();
      return $query->get_result();
    }

    protected static function get_by_id($id) {
      $class = get_called_class();
      $result = self::db_get_by_id($class, $id);
      if ($result->num_rows > 0) {
        $row = array_pad($result->fetch_row(), 5, null);
        return new $class($row[0], $row[1], $row[2], $row[3], $row[4]);
      } else {
        return null;
      }
    }
  }
?>