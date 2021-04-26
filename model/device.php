<?php
  namespace Model;
  require_once('../util/preventaccess.php');
  use \Util\Utils;
  
  /*
   * Class for representing and handling devices
   */
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
WHERE D.DeviceId = ?
ORDER BY DH.DeviceHistoryId DESC
LIMIT 1
SQL;

const SQL_INSERT = <<<SQL
INSERT INTO Devices (DeviceName, TypeId, RoomId)
VALUES (?, ?, ?);
SQL;

const SQL_UPDATE = <<<SQL
UPDATE Devices
SET DeviceName = IFNULL(?, DeviceName), TypeId = IFNULL(?, TypeId), RoomId = IFNULL(?, RoomId)
WHERE DeviceId = ?;
SQL;

const SQL_GET_STATUS = <<<SQL
SELECT DH.Status as status, DH.Time FROM DeviceHistory DH
WHERE DH.DeviceId = ?
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
    public static $required_fields_update = array('id');

    /*
     * Gets the status for the specified device
     */
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

    /*
     * Sets the status for the specified device
     */
    static function set_device_status($id, $status) {
      $db = self::get_connection();
      $query = $db->prepare(static::SQL_UPDATE_STATUS);
      $query->bind_param('isi', $id, $status, \Util\Session::user_id());
      $query->execute();
      echo $query->errno.': '.$query->error."\r\n";
      return $query->errno ? false : true;
    }

    /*
     * Binds the correct parameters to a supplied mysqli::statement prepared with
     * self::SQL_INSERT
     */
    protected static function bind_params_insert(&$query, $arr) {
      $query->bind_param('sii', $arr['name'], $arr['typeId'], $arr['roomId']);
    }

    /*
     * Binds the correct parameters to a supplied mysqli::statement prepared with
     * self::SQL_UPDATE
     */
    protected static function bind_params_update(&$query, $arr) {
      $name = Utils::get_or_default($arr, 'name');
      $typeId = Utils::get_or_default($arr, 'typeId');
      $roomId = Utils::get_or_default($arr, 'roomId');
      $query->bind_param('siii', $name, $typeId, $roomId, $arr['id']);
    }

    private $name;
    private $typeId;
    private $typeName;
    private $roomId;
    private $roomName;
    private $status;

    /*
     * Constructor for object respresenting a device. Checks if properties are set
     * before the call to __construct to allow reflective instantiation
     */
    function __construct($id = null, $name = null, $typeId = null, $typeName = null, $roomId = null, $roomName = null, $status = null) {
      parent::__construct($id);
      $name && $this->name = $name;
      $this->typeId = (int) (isset($this->typeId) ? $this->typeId : ($typeId ?: self::ID_NOT_SET));
      $typeName && $this->typeName = $typeName;
      $this->roomId = (int) (isset($this->roomId) ? $this->roomId : ($roomId ?: self::ID_NOT_SET));
      $roomName && $this->roomName = $roomName;
      $status && $this->status = $status;
    }

    /*
     * Gets the name of the device
     */
    function get_name() {
      return $this->name;
    }

    /*
     * Gets the type of the device
     */
    function get_type() {
      return $this->type;
    }

    /*
     * Gets the room the device belongs to
     */
    function get_room() {
      return $this->room;
    }

    /*
     * Gets the status of the device
     */
    function get_status() {
      return $this->status;
    }

    /*
     * Convert to an associative array.
     */
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