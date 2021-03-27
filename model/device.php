<?php
  namespace Model;
  
  class Device extends DBObject implements \JsonSerializable {

const SQL_GET_ALL = <<<SQL
SELECT D.DeviceId, D.DeviceName, DT.TypeName, R.RoomName, DH.Status, MAX(DH.Time) FROM Devices D
JOIN DeviceType DT ON (DT.TypeId = D.TypeId)
JOIN Room R ON (R.RoomId = D.RoomId)
LEFT JOIN DeviceHistory DH ON (DH.DeviceId = D.DeviceId)
GROUP BY D.DeviceId;
SQL;

const SQL_GET = <<<SQL
SELECT D.DeviceId, D.DeviceName, DT.TypeName, R.RoomName, DH.Status, MAX(DH.Time) FROM Devices D
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

const SQL_UPDATE_STATUS = <<<SQL
INSERT INTO DeviceHistory (DeviceId, Status, UserId)
VALUES (?, ?, ?);
SQL;

const SQL_DELETE = <<<SQL
DELETE FROM Devices
WHERE DeviceId = ?;
SQL;

    private $id;
    private $name;
    private $type;
    private $room;
    private $status;

    function __construct($id, $name, $type, $room, $status) {
      $this->id = (int) $id;
      $this->name = $name;
      $this->type = $type;
      $this->room = $room;
      $this->status = $status;
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
        'type' => $this->type,
        'room' => $this->room,
        'status' => $this->status
      );
    }
  }
?>