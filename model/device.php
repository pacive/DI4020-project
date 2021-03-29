<?php
  namespace Model;
  
  class Device extends DBObject implements \JsonSerializable {

const SQL_GET_ALL = <<<SQL
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
LEFT JOIN Room R ON (R.RoomId = D.RoomId)
LEFT JOIN DeviceHistory DH ON (DH.DeviceId = D.DeviceId)
GROUP BY D.DeviceId;
SQL;

const SQL_GET = <<<SQL
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
JOIN DeviceHistory DH ON (DH.DeviceId = D.DeviceId)
WHERE D.DeviceId = ?;
SQL;

const SQL_ADD = <<<SQL
INSERT INTO Devices (DeviceName, TypeId, RoomId)
VALUES (?, ?, ?);
SQL;

const SQL_UPDATE = <<<SQL
UPDATE Devices
SET DeviceName = ?, TypeId = ?, RoomId = ?
WHERE DeviceIdId = ?;
SQL;

const SQL_GET_STATUS = <<<SQL
SELECT Status as status, MAX(Time) FROM DeviceHistory
WHERE DeviceId = ?;
SQL;

const SQL_UPDATE_STATUS = <<<SQL
INSERT INTO DeviceHistory (DeviceId, Status, UserId)
VALUES (?, ?, ?);
SQL;

const SQL_DELETE = <<<SQL
DELETE FROM Devices
WHERE DeviceId = ?;
SQL;

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

    function jsonSerialize() {
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