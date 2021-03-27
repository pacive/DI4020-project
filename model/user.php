<?php
  namespace Model;

  class User extends DBObject implements \JsonSerializable {

const SQL_GET_ALL = <<<SQL
SELECT UserId, UserName, IsAdmin FROM Users
SQL;
    
const SQL_GET = <<<SQL
SELECT UserId, UserName, IsAdmin FROM Users
WHERE UserId = ?;
SQL;

const SQL_ADD = <<<SQL
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

    private $id;
    private $name;
    private $admin;

    function __construct($id, $name, $admin) {
      $this->id = (int) $id;
      $this->name = $name;
      $this->admin = (bool) $admin;
    }

    function get_id() {
      return $this->id;
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