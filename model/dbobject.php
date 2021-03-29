<?php
  namespace Model;
  abstract class DBObject {

    const MYSQL_HOST = 'localhost';
    const MYSQL_USER = 'andalf20';
    const MYSQL_PWD = 'lIKQfkgyLj';
    const MYSQL_DB = 'andalf20_db';
    
    const ID_NOT_SET = -1;

    private static $conn;

    protected $id;

    protected static function get_connection() {
      if (!isset(self::$conn)) {
        self::$conn = new \mysqli(self::MYSQL_HOST, self::MYSQL_USER, self::MYSQL_PWD, self::MYSQL_DB);
      }
      return self::$conn;
    }

    protected static function db_get_all() {
      $db = self::get_connection();
      return $db->query(static::SQL_GET_ALL);
    }

    static function get_all() {
      $result = self::db_get_all();
      $objects = array();
      while ($row = $result->fetch_object(static::class)) {
        $objects[] = $row;
      }
      return $objects;
    }

    protected static function db_get_by_id($class, $id) {
      $query = self::get_connection()->prepare($class::SQL_GET);
      $query->bind_param('i', $id);
      $query->execute();
      return $query->get_result();
    }

    static function get_by_id($id) {
      $class = get_called_class();
      $result = self::db_get_by_id($class, $id);
      if ($result->num_rows > 0) {
        $obj = $result->fetch_object($class);
        return $obj->get_id() == -1 ? null : $obj;
      } else {
        return null;
      }
    }

    protected function __construct($id) {
      $this->id = (int) (isset($this->id) ? $this->id : ($id ?: self::ID_NOT_SET));
    }

    function get_id() {
      return $this->id;
    }
  }
?>