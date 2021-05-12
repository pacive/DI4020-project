<?php
  namespace Model;
  require_once('../util/preventaccess.php');

  /*
   * Abstract base class for types
   */
  abstract class Type extends DBObject {

    public static $required_fields_insert = array('name');
    public static $required_fields_update = array('id', 'name');

    protected $name;

    /*
     * Binds the correct parameters to a supplied mysqli::statement prepared with
     * static::SQL_INSERT
     */
    protected static function bind_params_insert(&$query, $arr) {
      $query->bind_param('s', $arr['name']);
    }

    /*
     * Binds the correct parameters to a supplied mysqli::statement prepared with
     * static::SQL_UPDATE
     */
    protected static function bind_params_update(&$query, $arr) {
      $query->bind_param('si', $arr['name'], $arr['id']);
    }

    /*
     * Generic constructor for object respresenting a type. Checks if properties are set
     * before the call to __construct to allow reflective instantiation
     */
    function __construct($id = null, $name = null) {
      // Allow for reflective instantiation
      parent::__construct($id);
      $name && $this->name = $name;
    }

    /*
     * Gets the type name
     */
    function get_name() {
      return $this->name;
    }

    /*
     * Convert to an associative array.
     */
    function to_array() {
      return array('id' => $this->id, 'name' => htmlspecialchars($this->name));
    }
  }
?>