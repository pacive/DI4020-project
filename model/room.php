<?php
  namespace Model;

  class Room extends DBObject implements \JsonSerializable {

const SQL_GET_ALL = <<<SQL
SELECT R.RoomId, R.RoomName, RT.TypeName, C.X, C.Y, C.Idx FROM Room R
JOIN RoomType RT ON (RT.TypeId = R.TypeId)
LEFT JOIN Coordinates C ON (C.RoomId = R.RoomId)
ORDER BY R.RoomId, C.Idx
SQL;
    
const SQL_GET = <<<SQL
SELECT R.RoomId, R.RoomName, RT.TypeName, C.X, C.Y, C.Idx FROM Room R
JOIN RoomType RT ON (RT.TypeId = R.TypeId)
JOIN Coordinates C ON (C.RoomId = R.RoomId)
WHERE R.RoomId = ?;
SQL;

const SQL_GET_DEVICES = <<<SQL
SELECT MAX(DH.Time), D.DeviceId, D.DeviceName, R.RoomName, DT.TypeName, DH.Status FROM Devices D
JOIN DeviceType DT ON (DT.TypeId = D.TypeId)
JOIN Room R ON (R.RoomId = D.RoomId)
LEFT JOIN DeviceHistory DH ON (DH.DeviceId = D.DeviceId)
WHERE R.RoomId = ?
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
    
    private $id;
    private $name;
    private $type;
    private $coordinates;

    static function get_all() {
      $result = self::db_get_all(self::class);
      $rooms = array();
      $cursor = 0;
      while ($room = self::create_room($result)) {
        $offset = count($room->get_coordinates());
        $cursor += ($offset) ? $offset : 1;
        $result->data_seek($cursor);
        $rooms[] = $room;
      }
      return $rooms;
    }

    static function create_room(&$result) {
      if ($first_row = $result->fetch_object()) {
        if (!$first_row->X) {
          $coordinates = array();
        } else {
          $coordinates = array(array('x' => $first_row->X, 'y' => $first_row->Y));
        }
        while ($row = $result->fetch_object()) {
          if ($row->RoomId != $first_row->RoomId) {
            break;
          } else {
            $coordinates[] = array('x' => $row->X, 'y' => $row->Y);
          }
        }
        return new Room($first_row->RoomId, $first_row->RoomName, $first_row->TypeName, $coordinates);
      } else {
        return false;
      }
    }

    function __construct($id, $name, $type, $coordinates) {
      $this->id = (int) $id;
      $this->name = $name;
      $this->type = $type;
      $this->coordinates = $coordinates;
    }

    function get_id() {
      return $this->id;
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
        'type' => $this->type,
        'coordinates' => $this->coordinates
      );
    }
  }
?>