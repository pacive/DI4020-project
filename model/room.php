<?php
  namespace Model;

  class Room extends DBObject {

const SQL_GET_ALL = <<<SQL
SELECT R.RoomId as id,
R.RoomName as name,
RT.TypeId as typeId,
RT.TypeName as typeName,
GROUP_CONCAT(C.X, ',', C.Y ORDER BY C.Idx SEPARATOR ';') as coordinates
FROM Room R
JOIN RoomType RT ON (RT.TypeId = R.TypeId)
LEFT JOIN Coordinates C ON (C.RoomId = R.RoomId)
GROUP BY R.RoomId
ORDER BY R.RoomId
SQL;
    
const SQL_GET = <<<SQL
SELECT R.RoomId as id,
R.RoomName as name,
RT.TypeId as typeId,
RT.TypeName as typeName,
GROUP_CONCAT(C.X, ',', C.Y ORDER BY C.Idx SEPARATOR ';') as coordinates
FROM Room R
JOIN RoomType RT ON (RT.TypeId = R.TypeId)
LEFT JOIN Coordinates C ON (C.RoomId = R.RoomId)
WHERE R.RoomId = ?;
SQL;

const SQL_GET_ALL_INCL_DEVICES = <<<SQL
SELECT R.RoomId as id, 
R.RoomName as name,
RT.TypeId as typeId,
RT.TypeName as typeName,
GROUP_CONCAT(C.X, ',', C.Y ORDER BY C.Idx SEPARATOR ';') as coordinates,
D.DeviceId as deviceId, 
D.DeviceName as deviceName, 
DT.TypeId as deviceTypeId, 
DT.TypeName as deviceTypeName, 
DH.Status as deviceStatus
FROM Room R
JOIN RoomType RT ON (RT.TypeId = R.TypeId)
LEFT JOIN Coordinates C ON (C.RoomId = R.RoomId)
JOIN Devices D ON (D.RoomId = R.RoomId)
JOIN DeviceType DT ON (DT.TypeId = D.TypeId)
LEFT JOIN (
  SELECT DH1.DeviceId, DH1.Status FROM DeviceHistory DH1
	LEFT JOIN DeviceHistory DH2 ON DH1.DeviceId = DH2.DeviceId AND DH1.DeviceHistoryId < DH2.DeviceHistoryId
	WHERE DH2.DeviceId IS NULL
  ) DH ON DH.DeviceId = D.DeviceId
GROUP BY D.DeviceId
ORDER BY D.DeviceId
SQL;

const SQL_GET_INCL_DEVICES = <<<SQL
SELECT R.RoomId as id, 
R.RoomName as name,
RT.TypeId as typeId,
RT.TypeName as typeName,
GROUP_CONCAT(C.X, ',', C.Y ORDER BY C.Idx SEPARATOR ';') as coordinates,
D.DeviceId as deviceId, 
D.DeviceName as deviceName, 
DT.TypeId as deviceTypeId, 
DT.TypeName as deviceTypeName, 
DH.Status as deviceStatus
FROM Room R
JOIN RoomType RT ON (RT.TypeId = R.TypeId)
LEFT JOIN Coordinates C ON (C.RoomId = R.RoomId)
JOIN Devices D ON (D.RoomId = R.RoomId)
JOIN DeviceType DT ON (DT.TypeId = D.TypeId)
LEFT JOIN (
  SELECT DH1.DeviceId, DH1.Status FROM DeviceHistory DH1
	LEFT JOIN DeviceHistory DH2 ON DH1.DeviceId = DH2.DeviceId AND DH1.DeviceHistoryId < DH2.DeviceHistoryId
	WHERE DH2.DeviceId IS NULL
  ) DH ON DH.DeviceId = D.DeviceId
WHERE R.RoomId = ?
GROUP BY D.DeviceId
ORDER BY D.DeviceId
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

    static function get_all($incl_devices = false) {
      $db = self::get_connection();
      $result = $db->query($incl_devices ? static::SQL_GET_ALL_INCL_DEVICES : static::SQL_GET_ALL);
      $rooms = array();
      $cursor = 0;
      while ($room = self::create_room($result)) {
        $offset = count($room->get_devices());
        $cursor += $offset ?: 1;
        $result->data_seek($cursor);
        $rooms[] = $room;
      }
      return $rooms;
    }

    static function get_by_id($id, $incl_devices = false) {
      $query = self::get_connection()->prepare($incl_devices ? static::SQL_GET_INCL_DEVICES :static::SQL_GET);
      $query->bind_param('i', $id);
      $query->execute();
      $result = $query->get_result();
      return self::create_room($result);
    }

    static function create_room(&$result) {
      if ($first_row = $result->fetch_object()) {
        $coordinates = isset($first_row->coordinates) ? self::parse_coordinates($first_row->coordinates) : array();
        $room = new Room($first_row->id, $first_row->name, $first_row->typeId, $first_row->typeName, $coordinates);
        if (isset($first_row->deviceId)) {
          $room->add_device(new Device($first_row->deviceId,
                                       $first_row->deviceName,
                                       $first_row->deviceTypeId,
                                       $first_row->deviceTypeName,
                                       $room->id,
                                       $room->name,
                                       $first_row->deviceStatus));

          while ($row = $result->fetch_object()) {
            if ($row->id != $room->id) {
              break;
            } else {
              $room->add_device(new Device($row->deviceId,
                                          $row->deviceName,
                                          $row->deviceTypeId,
                                          $row->deviceTypeName,
                                          $room->id,
                                          $room->name,
                                          $row->deviceStatus));
            }
          }
        }
        return $room;
      } else {
        return null;
      }
    }

    private static function parse_coordinates($str) {
      $coordinates = array();
      foreach (explode(';', $str) as $pair) {
        $coordinates[] = array_map('intval', explode(',', $pair));
      }
      return $coordinates;
    }

    static function insert($arr) {
      $db = self::get_connection();
      $query = $db->prepare(static::SQL_INSERT);
      $query->bind_param('si', $arr['name'], $arr['typeId']);
      $query->execute();
      if ($db->errno) {
        return $db->error;
      }
      $new_id = $query->insert_id;
      $query->close();

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
      return static::get_by_id($arr['id']);
    }

    private static function set_coordinates($id, &$coordinates) {
      $query = self::get_connection()->prepare(static::SQL_SET_COORDS);
      $query->bind_param('iiii', $id, $x, $y, $i);
      for ($i = 0; $i < count($coordinates); $i++) {
        $x = $coordinates[$i][0];
        $y = $coordinates[$i][1];
        $query->execute();
      }
    }

    private $name;
    private $typeId;
    private $typeName;
    private $coordinates;
    private $devices;

    function __construct($id, $name, $typeId, $typeName, $coordinates, $devices = null) {
      parent::__construct($id);
      $this->name = $name;
      $this->typeId = (int) $typeId;
      $this->typeName = $typeName;
      $this->coordinates = $coordinates;
      $this->devices = $devices;
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

    function get_devices() {
      return $this->devices;
    }

    function add_device($device) {
      $this->devices[] = $device;
    }

    function to_array() {
      $arr = array(
        'id' => $this->id,
        'name' => $this->name,
        'typeId' => $this->typeId,
        'typeName' => $this->typeName,
        'coordinates' => $this->coordinates
      );
      $this->devices && $arr['devices'] = $this->devices;
      return $arr;
    }
  }
?>