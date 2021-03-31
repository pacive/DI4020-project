<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Util\DB;

 class Events extends AbstractEndpoint {

const TIMEOUT = 600;
const INTERVAL = 2;

const SQL_GET_LATEST = <<<SQL
SELECT DH.DeviceId as id, DH.Status as status, DH.DeviceHistoryId as eventId FROM DeviceHistory DH
LEFT JOIN DeviceHistory DH2 ON DH2.DeviceID = DH.DeviceId AND DH.DeviceHistoryID < DH2.DeviceHistoryId
WHERE DH2.DeviceHistoryID IS NULL
AND DH.DeviceHistoryId > ?
ORDER BY DH.DeviceHistoryID
SQL;

const SQL_GET_UPDATES = <<<SQL
SELECT DeviceId as id, Status as status, DeviceHistoryId as eventId FROM DeviceHistory
WHERE DeviceHistoryId > ?
ORDER BY DeviceHistoryId
SQL;

    static function handle_request() {
      self::verify_user();
      session_write_close();
      $headers = apache_request_headers();
      header('Content-Type: text/event-stream');
      header('Cache-Control: no-cache');
      header("Connection: keep-alive");
      $last_id = 0;
      if (isset($headers['Last-Event-ID'])) {
        $last_id = (int) $headers['Last-Event-ID'];
      }
      $last_id = self::push_latest($last_id);
      self::loop($last_id);
    }

    private static function push_latest($last_id) {
      $db = DB::get_connection();
      $query = $db->prepare(self::SQL_GET_LATEST);
      $query->bind_param('i', $last_id);
      $query->execute();
      $result = $query->get_result();
      while ($row = $result->fetch_assoc()) {
        $last_id = $row['eventId'];
        echo "id: {$row['eventId']}\n";
        $data = json_encode($row);
        echo "data: $data\n\n";
        ob_flush();
        flush();
      }
      $query->close();
      return $last_id;
    }

    private static function loop($last_id) {
      $db = DB::get_connection();
      $query = $db->prepare(self::SQL_GET_UPDATES);
      $query->bind_param('i', $last_id);
      $time = 0;
      while ($time <= self::TIMEOUT) {
        $query->execute();
        $result = $query->get_result();
        while ($row = $result->fetch_assoc()) {
          $last_id = $row['eventId'];
          echo "id: {$row['eventId']}\n";
          $data = json_encode($row);
          echo "data: $data\n\n";
          ob_flush();
          flush();
        }
        $time += self::INTERVAL;
        sleep(self::INTERVAL);
      }
    }
  }
  Events::handle_request();
?>