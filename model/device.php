<?php
  namespace Model;
  
  class Device extends DBObject {

const SQL_GET_ALL = <<<SQL
SELECT D.DeviceId as id, 
D.DeviceName as name, 
DT.TypeId as typeId, 
DT.TypeName as typeName, 
R.RoomId as roomId, 
R.RoomName as roomName, 
DH.Status as status
FROM Devices D
JOIN DeviceType DT ON (DT.TypeId = D.TypeId)
LEFT JOIN Room R ON (R.RoomId = D.RoomId)
LEFT JOIN (
  SELECT DH1.DeviceId, DH1.Status FROM DeviceHistory DH1
	LEFT JOIN DeviceHistory DH2 ON DH1.DeviceId = DH2.DeviceId AND DH1.DeviceHistoryId < DH2.DeviceHistoryId
	WHERE DH2.DeviceId IS NULL
  ) DH ON DH.DeviceId = D.DeviceId
SQL;

const SQL_GET = <<<SQL
SELECT D.DeviceId as id, 
D.DeviceName as name, 
DT.TypeId as typeId, 
DT.TypeName as typeName, 
R.RoomId as roomId, 
R.RoomName as roomName, 
DH.Status as status
FROM Devices D
JOIN DeviceType DT ON (DT.TypeId = D.TypeId)
LEFT JOIN Room R ON (R.RoomId = D.RoomId)
LEFT JOIN DeviceHistory DH ON DH.DeviceId = D.DeviceId
WHERE D.DeviceId = 6
ORDER BY DH.DeviceHistoryId DESC
LIMIT 1
SQL;

const SQL_INSERT = <<<SQL
INSERT INTO Devices (DeviceName, TypeId, RoomId)
VALUES (?, ?, ?);
SQL;

const SQL_UPDATE = <<<SQL
UPDATE Devices
SET DeviceName = ?, TypeId = ?, RoomId = ?
WHERE DeviceIdId = ?;
SQL;

const SQL_GET_STATUS = <<<SQL
SELECT DH.Status as status, DH.Time FROM DeviceHistory DH
WHERE DH.DeviceId = 6
ORDER BY DeviceHistoryId DESC
LIMIT 1
SQL;

const SQL_UPDATE_STATUS = <<<SQL
INSERT INTO DeviceHistory (DeviceId, Status, UserId)
VALUES (?, ?, ?);
SQL;

const SQL_DELETE = <<<SQL
DELETE FROM Devices
WHERE DeviceId = ?;
SQL;

    public static $required_fields_insert = array('name', 'typeId', 'roomId');
    public static $required_fields_update = array('id', 'name', 'typeId', 'roomId');

    static function get_device_status($device_id) {
      $db = self::get_connection();
      $query = $db->prepare(static::SQL_GET_STATUS);
      $query->bind_param('i', $device_id);
      $query->execute();
      $result = $query->get_result();
      if ($row = $result->fetch_object()) {
        return array('id' => $device_id, 'status' => $row->status);
      } else {
        return null;
      }
    }

    static function set_device_status($arr) {
      $db = self::get_connection();
      $query = $db->prepare(static::SQL_UPDATE_STATUS);
      $query->bind_param('isi', $arr['id'], $arr['status'], \Util\Session::user_id());
      $query->execute();
      echo $query->errno.': '.$query->error."\r\n";
      return $query->errno ? false : true;
    }

    protected static function bind_params_insert(&$query, $arr) {
      $query->bind_param('sii', $arr['name'], $arr['typeId'], $arr['roomId']);
    }

    protected static function bind_params_update(&$query, $arr) {
      $query->bind_param('siii', $arr['name'], $arr['typeId'], $arr['roomId'], $arr['id']);
    }

    private $name;
    private $typeId;
    private $typeName;
    private $roomId;
    private $roomName;
    private $status;

    function __construct($id = null, $name = null, $typeId = null, $typeName = null, $roomId = null, $roomName = null, $status = null) {
      // Allow for reflective instantiation
      parent::__construct($id);
      $name && $this->name = $name;
      $this->typeId = (int) (isset($this->typeId) ? $this->typeId : ($typeId ?: self::ID_NOT_SET));
      $typeName && $this->typeName = $typeName;
      $this->roomId = (int) (isset($this->roomId) ? $this->roomId : ($roomId ?: self::ID_NOT_SET));
      $roomName && $this->roomName = $roomName;
      $status && $this->status = $status;
    }

    function get_name() {
      return $this->name;
    }

    function get_type() {
      return $this->type;
    }

    function get_room() {
      return $this->room;
    }

    function get_status() {
      return $this->status;
    }

    function to_array() {
      return array(
        'id' => $this->id,
        'name' => $this->name,
        'typeId' => $this->typeId,
        'typeName' => $this->typeName,
        'roomId' => $this->roomId,
        'roomName' => $this->roomName,
        'status' => $this->status
      );
    }
  }
?>