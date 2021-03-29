<?php
  namespace Model;

  class User extends DBObject implements \JsonSerializable {

const SQL_GET_ALL = <<<SQL
SELECT UserId as id, UserName as name, IsAdmin as admin FROM Users
SQL;
    
const SQL_GET = <<<SQL
SELECT UserId as id, UserName as name, IsAdmin as admin FROM Users
WHERE UserId = ?;
SQL;

const SQL_INSERT = <<<SQL
INSERT INTO Users (UserName, Password, IsAdmin)
VALUES (?, ?, ?);
SQL;

const SQL_UPDATE = <<<SQL
UPDATE Users
SET UserName = ?, IsAdmin = ?
WHERE UserId = ?;
SQL;

const SQL_CHPWD = <<<SQL
UPDATE Users
SET Password = ?
WHERE UserId = ?;
SQL;

const SQL_DELETE = <<<SQL
DELETE FROM Users
WHERE UserId = ?;
SQL;

    public static $required_fields_insert = array('name', 'password', 'admin');
    public static $required_fields_update = array('id', 'name', 'admin');

    private $name;
    private $admin;

    protected static function bind_params_insert(&$query, $arr) {
      $password = password_hash($arr['password'], PASSWORD_BCRYPT);
      $query->bind_param('ssi', $arr['name'], $password, $arr['admin']);
    }

    protected static function bind_params_update(&$query, $arr) {
      $query->bind_param('sii', $arr['name'], $arr['admin'], $arr['id']);
    }

    static function set_password($arr) {
      $password = password_hash($arr['password'], PASSWORD_BCRYPT);
      $db = self::get_connection();
      $query = $db->prepare(static::SQL_CHPWD);
      $query->bind_param('si', $password, $arr['id']);
      $query->execute();
      return $query->errno ? false : true
    }

    function __construct($id = null, $name = null, $admin = null) {
      // Allow for reflective instantiation
      parent::__construct($id);
      $name && $this->name = $name;
      $this->admin = (bool) ($this->admin ?: $admin);
    }

    function get_name() {
      return $this->name;
    }

    function is_admin() {
      return $this->admin;
    }

    function jsonSerialize() {
      return array('id' => $this->id, 'name' => $this->name, 'admin' => $this->admin);
    }
  }
?>