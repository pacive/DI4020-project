<?php
  namespace Model;

  class RoomType extends Type {

const SQL_GET_ALL = <<<SQL
SELECT TypeId as id, TypeName as name FROM RoomType
SQL;
    
const SQL_GET = <<<SQL
SELECT TypeId as id, TypeName as name FROM RoomType
WHERE TypeId = ?;
SQL;

const SQL_INSERT = <<<SQL
INSERT INTO RoomType (TypeName)
VALUES (?);
SQL;

const SQL_UPDATE = <<<SQL
UPDATE RoomType
SET TypeName = ?
WHERE TypeId = ?;
SQL;

const SQL_DELETE = <<<SQL
DELETE FROM RoomType
WHERE TypeId = ?;
SQL;
  }
?>