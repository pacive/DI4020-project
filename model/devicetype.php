<?php
  namespace Model;
  require_once('../util/preventaccess.php');

  /*
   * Class for representing and handling device types
   */
  class DeviceType extends Type {

const SQL_GET_ALL = <<<SQL
SELECT TypeId as id, TypeName as name FROM DeviceType
SQL;
    
const SQL_GET = <<<SQL
SELECT TypeId as id, TypeName as name FROM DeviceType
WHERE TypeId = ?;
SQL;

const SQL_INSERT = <<<SQL
INSERT INTO DeviceType (TypeName)
VALUES (?);
SQL;

const SQL_UPDATE = <<<SQL
UPDATE DeviceType
SET TypeName = ?
WHERE TypeId = ?;
SQL;

const SQL_DELETE = <<<SQL
DELETE FROM DeviceType
WHERE TypeId = ?;
SQL;
  }
?>