<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Util\DB;
  use \Util\Utils;

  /*
   * Endpoint for subscribing to SSE
   */
  class Events extends AbstractEndpoint {

const TIMEOUT = 600;
const INTERVAL = 2;

const SQL_GET_LATEST = <<<SQL
SELECT DH.DeviceId as id, DH.Status as status, DH.Time as time, DH.DeviceHistoryId as eventId FROM DeviceHistory DH
LEFT JOIN DeviceHistory DH2 ON DH2.DeviceID = DH.DeviceId AND DH.DeviceHistoryID < DH2.DeviceHistoryId
WHERE DH2.DeviceHistoryID IS NULL
AND DH.DeviceHistoryId > ?
ORDER BY DH.DeviceHistoryID
SQL;

const SQL_GET_UPDATES = <<<SQL
SELECT DeviceId as id, Status as status, Time as time, DeviceHistoryId as eventId FROM DeviceHistory
WHERE DeviceHistoryId > ?
ORDER BY DeviceHistoryId
SQL;

    /*
     * Overrides function from AbstractEndpoint. Sets the correct headers then
     * pushes the latest status for each device (unless the client supplies the
     * Last-Event-ID request header). 
     * 
     * Finally starts a loop to poll the database for updates and sends them to the
     * client.
     */
    static function handle_request() {
      self::verify_user();
      session_write_close();
      try {
        $headers = apache_request_headers();
        $keep_alive = isset($headers['Connection']) && $headers['Connection'] != 'close';
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: ' . ($keep_alive ? 'keep-alive' : 'close'));
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
          exit();
        }

        $last_id = Utils::get_or_default($headers, 'Last-Event-ID', 0);
        $last_id = self::push_latest($last_id);
        if ($keep_alive) {
          self::loop($last_id);
        }
      } catch (\Exception $e) {
        exit();
      }
    }

    /*
     * Sends the latest status for each device to the client
     */
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

    /*
     * Polls the database every self::INTERVAL seconds and sends any new entries
     * to the client. Times out after self::TIMEOUT seconds
     */
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