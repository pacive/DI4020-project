<?php
  namespace Model;

  class Room extends DBObject implements \JsonSerializable {

const SQL_GET_ALL = <<<SQL
SELECT R.RoomId as id,
R.RoomName as name,
RT.TypeId as typeId,
RT.TypeName as typeName,
C.X as x,
C.Y as y,
C.Idx as idx
FROM Room R
JOIN RoomType RT ON (RT.TypeId = R.TypeId)
LEFT JOIN Coordinates C ON (C.RoomId = R.RoomId)
ORDER BY R.RoomId, C.Idx
SQL;
    
const SQL_GET = <<<SQL
SELECT R.RoomId as id,
R.RoomName as name,
RT.TypeId as typeId,
RT.TypeName as typeName,
C.X as x,
C.Y as y,
C.Idx as idx
FROM Room R
JOIN RoomType RT ON (RT.TypeId = R.TypeId)
LEFT JOIN Coordinates C ON (C.RoomId = R.RoomId)
WHERE R.RoomId = ?;
SQL;

const SQL_GET_DEVICES = <<<SQL
SELECT D.DeviceId as id, 
D.DeviceName as name, 
DT.TypeId as typeId, 
DT.TypeName as typeName, 
R.RoomId as roomId, 
R.RoomName as roomName, 
DH.Status as status, 
MAX(DH.Time)
FROM Devices D
JOIN DeviceType DT ON (DT.TypeId = D.TypeId)
JOIN Room R ON (R.RoomId = D.RoomId)
LEFT JOIN DeviceHistory DH ON (DH.DeviceId = D.DeviceId)
WHERE D.RoomId = ?
GROUP BY D.DeviceId;
SQL;

const SQL_ADD = <<<SQL
INSERT INTO Room (RoomName, TypeId)
VALUES (?, ?);
SQL;

const SQL_SET_COORDS = <<<SQL
INSERT INTO Coordinates (RoomId, X, Y, Order)
VALUES (?, ?, ?, ?);
SQL;

const SQL_UPDATE = <<<SQL
UPDATE Room
SET RoomName = ?, TypeId = ?
WHERE RoomId = ?;
SQL;

const SQL_DELETE = <<<SQL
DELETE FROM Room
WHERE RoomId = ?;
SQL;

const SQL_DELETE_COORDS = <<<SQL
DELETE FROM Coordinates
WHERE RoomId = ?;
SQL;
    
    private $name;
    private $typeId;
    private $typeName;
    private $coordinates;

    static function get_all() {
      $result = self::db_get_all(self::class);
      $rooms = array();
      $cursor = 0;
      while ($room = self::create_room($result)) {
        $offset = count($room->get_coordinates());
        $cursor += $offset ?: 1;
        $result->data_seek($cursor);
        $rooms[] = $room;
      }
      return $rooms;
    }

    static function get_by_id($id) {
      $result = self::db_get_by_id(self::class, $id);
      return self::create_room($result);
    }

    static function create_room(&$result) {
      if ($first_row = $result->fetch_object()) {
        if (!$first_row->x) {
          $coordinates = array();
        } else {
          $coordinates = array(array('x' => $first_row->x, 'y' => $first_row->y));
        }
        while ($row = $result->fetch_object()) {
          if ($row->id != $first_row->id) {
            break;
          } else {
            $coordinates[] = array('x' => $row->x, 'y' => $row->y);
          }
        }
        return new Room($first_row->id, $first_row->name, $first_row->typeId, $first_row->typeName, $coordinates);
      } else {
        return null;
      }
    }

    function __construct($id, $name, $typeId, $typeName, $coordinates) {
      parent::__construct($id);
      $this->name = $name;
      $this->typeId = (int) $typeId;
      $this->typeName = $typeName;
      $this->coordinates = $coordinates;
    }

    function get_name() {
      return $this->name;
    }

    function get_type() {
      return $this->type;
    }

    function get_coordinates() {
      return $this->coordinates;
    }

    function jsonSerialize() {
      return array(
        'id' => $this->id,
        'name' => $this->name,
        'typeId' => $this->typeId,
        'typeName' => $this->typeName,
        'coordinates' => $this->coordinates
      );
    }
  }
?>