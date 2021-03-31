<?php
  namespace Model;
  use \Util\DB;

  abstract class DBObject implements \JsonSerializable {

    const ID_NOT_SET = -1;

    protected static function get_connection() {
      return DB::get_connection();
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

    protected static function db_get_by_id($id) {
      $query = self::get_connection()->prepare(static::SQL_GET);
      $query->bind_param('i', $id);
      $query->execute();
      return $query->get_result();
    }

    static function get_by_id($id) {
      $class = get_called_class();
      $result = self::db_get_by_id($id);
      if ($result->num_rows > 0) {
        $obj = $result->fetch_object(static::class);
        return $obj->get_id() == -1 ? null : $obj;
      } else {
        return null;
      }
    }

    static function insert($arr) {
      $db = self::get_connection();
      $query = $db->prepare(static::SQL_INSERT);
      static::bind_params_insert($query, $arr);
      $query->execute();
      if ($query->errno) {
        return $query->error;
      }
      $query->close();
      return static::get_by_id($query->insert_id);
    }

    static function update($arr) {
      $db = self::get_connection();
      $query = $db->prepare(static::SQL_UPDATE);
      static::bind_params_update($query, $arr);
      $query->execute();
      if ($query->errno) {
        return $query->error;
      }
      $query->close();
      return static::get_by_id($arr['id']);
    }

    static function delete($id) {
      $query = self::get_connection()->prepare(static::SQL_DELETE);
      $query->bind_param('i', $id);
      $query->execute();
      return $query->errno ? false : true;
    }

    protected $id;

    protected function __construct($id) {
      $this->id = (int) (isset($this->id) ? $this->id : ($id ?: self::ID_NOT_SET));
    }

    function get_id() {
      return $this->id;
    }

    abstract function to_array();

    function jsonSerialize() {
      return $this->to_array();
    }
  }
?>