<?php
  namespace Model;

  abstract class Type extends DBObject implements \JsonSerializable {

    public static $required_fields = array('category', 'name');

    protected $name;

    protected static function bind_params(&$query, $arr) {
      $query->bind_param('s', $arr['name']);
    }

    function __construct($id = null, $name = null) {
      // Allow for reflective instantiation
      parent::__construct($id);
      $name && $this->name = $name;
    }

    function get_name() {
      return $this->name;
    }

    function jsonSerialize() {
      return array('id' => $this->id, 'name' => $this->name);
    }
  }
?>