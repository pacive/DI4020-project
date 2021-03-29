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

const SQL_INSERT = <<<SQL
INSERT INTO Room (RoomName, TypeId)
VALUES (?, ?);
SQL;

const SQL_SET_COORDS = <<<SQL
INSERT INTO Coordinates (RoomId, X, Y, Idx)
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
    
    public static $required_fields_insert = array('name', 'typeId');
    public static $required_fields_update = array('id', 'name', 'typeId');

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
        if (isset($first_row->x)) {
          $coordinates = array(array('x' => (int) $first_row->x, 'y' => (int) $first_row->y));
        } else {
          $coordinates = array();
        }
        while ($row = $result->fetch_object()) {
          if ($row->id != $first_row->id) {
            break;
          } else {
            $coordinates[] = array('x' => (int) $row->x, 'y' => (int) $row->y);
          }
        }
        return new Room($first_row->id, $first_row->name, $first_row->typeId, $first_row->typeName, $coordinates);
      } else {
        return null;
      }
    }

    static function insert($arr) {
      $db = self::get_connection();
      $query = $db->prepare(static::SQL_INSERT);
      $query->bind_param('si', $arr['name'], $arr['typeId']);
      $query->execute();
      if ($db->errno) {
        return $db->error;
      }
      $query->close();

      $new_id = $query->insert_id;
      if (!empty($arr['coordinates'])) {
        static::set_coordinates($new_id, $arr['coordinates']);
      }
      return static::get_by_id($new_id);
    }

    static function update($arr) {
      $db = self::get_connection();
      $query = $db->prepare(static::SQL_UPDATE);
      $query->bind_param('sii', $arr['name'], $arr['typeId'], $arr['id']);
      $query->execute();
      if ($db->errno) {
        return $db->error;
      }
      $query->close();

      if (isset($arr['coordinates'])) {
        $query = $db->prepare(static::SQL_DELETE_COORDS);
        $query->bind_param('i', $arr['id']);
        $query->execute();
        $query->close();
        static::set_coordinates($arr['id'], $arr['coordinates']);
      }
      return static::get_by_id($new_id);
    }

    private static function set_coordinates($id, &$coordinates) {
      $query = $db->prepare(static::SQL_SET_COORDS);
      $query->bind_param('iiii', $id, $x, $y, $i);
      for ($i = 0; $i < count($arr['coordinates']); $i++) {
        $x = $coordinates[$i]['x'];
        $y = $coordinates[$i]['y'];
        $query->execute();
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