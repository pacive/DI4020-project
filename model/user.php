<?php
  namespace Model;
  require_once('../util/preventaccess.php');
  use \Util\Utils;

  /*
   * Class for representing and handling users
   */
  class User extends DBObject {

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
SET UserName = IFNULL(?, UserName), Password = IFNULL(?, Password), IsAdmin = IFNULL(?, IsAdmin)
WHERE UserId = ?;
SQL;

const SQL_DELETE = <<<SQL
DELETE FROM Users
WHERE UserId = ?;
SQL;

    public static $required_fields_insert = array('name', 'password', 'admin');
    public static $required_fields_update = array('id');

    /*
     * Binds the correct parameters to a supplied mysqli::statement prepared with
     * self::SQL_INSERT
     */
    protected static function bind_params_insert(&$query, $arr) {
      $password = password_hash($arr['password'], PASSWORD_BCRYPT);
      $query->bind_param('ssi', $arr['name'], $password, $arr['admin']);
    }

    /*
     * Binds the correct parameters to a supplied mysqli::statement prepared with
     * self::SQL_UPDATE
     */
    protected static function bind_params_update(&$query, $arr) {
      $name = Utils::get_or_default($arr, 'name');
      $password = isset($arr['password']) ? password_hash($arr['password'], PASSWORD_BCRYPT) : null;
      $admin = Utils::get_or_default($arr, 'admin');
      $query->bind_param('ssii', $name, $password, $admin, $arr['id']);
    }

    private $name;
    private $admin;

    /*
     * Constructor for object respresenting a user. Checks if properties are set
     * before the call to __construct to allow reflective instantiation
     */
    function __construct($id = null, $name = null, $admin = null) {
      parent::__construct($id);
      $name && $this->name = $name;
      $this->admin = (bool) ($this->admin ?: $admin);
    }

    /*
     * Gets the username of the user
     */
    function get_name() {
      return $this->name;
    }

    /*
     * Returns whether the user is an admin
     */
    function is_admin() {
      return $this->admin;
    }

    /*
     * Convert to an associative array.
     */
    function to_array() {
      return array('id' => $this->id, 'name' => $this->name, 'admin' => $this->admin);
    }
  }
?>