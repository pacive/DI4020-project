<?php
  namespace Model;
  use \Util\DB;

  /*
   * Abstract base class for the objects represented in the database
   */
  abstract class DBObject implements \JsonSerializable {

    const ID_NOT_SET = -1;

    /*
     * Gets a connection to the database
     */
    protected static function get_connection() {
      return DB::get_connection();
    }

    /*
     * Gets all entries of the entity from the database
     */
    protected static function db_get_all() {
      $db = self::get_connection();
      return $db->query(static::SQL_GET_ALL);
    }

    /*
     * Gets all entries from the db and returns them as an array of the corresponding objects
     */
    static function get_all() {
      $result = self::db_get_all();
      $objects = array();
      while ($row = $result->fetch_object(static::class)) {
        $objects[] = $row;
      }
      return $objects;
    }

    /*
     * Gets a specific entry from the database
     */
    protected static function db_get_by_id($id) {
      $query = self::get_connection()->prepare(static::SQL_GET);
      $query->bind_param('i', $id);
      $query->execute();
      return $query->get_result();
    }

    /*
     * Gets a specific entry from the db and returns it as the corresponding object
     */
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

    /*
     * Insert a new entry in the database. Returns the newly inserted entity.
     */
    static function insert($arr) {
      $db = self::get_connection();
      $query = $db->prepare(static::SQL_INSERT);
      static::bind_params_insert($query, $arr);
      $query->execute();
      if ($query->errno) {
        return $query->error;
      }
      $new_id = $query->insert_id;
      $query->close();
      return static::get_by_id($new_id);
    }

    /*
     * Updates an entry in the database
     */
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

    /*
     * Deletes an entry from the database
     */
    static function delete($id) {
      $query = self::get_connection()->prepare(static::SQL_DELETE);
      $query->bind_param('i', $id);
      $query->execute();
      return $query->errno ? false : true;
    }

    protected $id;

    /*
     * Generic constructor, initializes an object with an id, or -1 if not specified.
     * Checks if the id is set before the call to __construct to allow reflective instantiation
     */
    protected function __construct($id) {
      $this->id = (int) (isset($this->id) ? $this->id : ($id ?: self::ID_NOT_SET));
    }

    /*
     * Gets the id of the object
     */
    function get_id() {
      return $this->id;
    }

    /*
     * Convert to an array. Needs to be overridden by extending classes
     */
    abstract function to_array();

    /*
     * Function to allow objects to be serialized to json using json_encode()
     */
    function jsonSerialize() {
      return $this->to_array();
    }
  }
?>