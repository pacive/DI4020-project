<?php
  namespace Model;

  abstract class Type extends DBObject {

    public static $required_fields_insert = array('category', 'name');
    public static $required_fields_update = array('id', 'category', 'name');

    protected $name;

    protected static function bind_params_insert(&$query, $arr) {
      $query->bind_param('s', $arr['name']);
    }

    protected static function bind_params_update(&$query, $arr) {
      $query->bind_param('si', $arr['name'], $arr['id']);
    }

    function __construct($id = null, $name = null) {
      // Allow for reflective instantiation
      parent::__construct($id);
      $name && $this->name = $name;
    }

    function get_name() {
      return $this->name;
    }

    function to_array() {
      return array('id' => $this->id, 'name' => $this->name);
    }
  }
?>