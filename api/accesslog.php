<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Util\DB;

  /*
   * Endpoint for subscribing to SSE
   */
  class AccessLog extends AbstractEndpoint {

const TIMEOUT = 600;
const INTERVAL = 2;

const SQL_GET_LATEST = <<<SQL
(SELECT AL.AccessId AS id,
AL.Time AS time,
AL.RequestType AS requestType,
AL.Page AS page,
AL.ResponseCode AS responseCode,
IFNULL(U.userName, '<unknown>') as user
FROM AccessLog AL
LEFT JOIN Users U ON U.userId = AL.UserId
ORDER BY AL.AccessId DESC
LIMIT ?)
ORDER BY id
SQL;

const SQL_GET_UPDATES = <<<SQL
SELECT AL.AccessId AS id,
AL.Time AS time,
AL.RequestType AS requestType,
AL.Page AS page,
AL.ResponseCode AS responseCode,
IFNULL(U.userName, '<unknown>') as user
FROM AccessLog AL
LEFT JOIN Users U ON U.userId = AL.UserId
WHERE AL.AccessId > ?
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
      self::verify_user(true);
      session_write_close();
      try {
        $headers = apache_request_headers();
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header("Connection: keep-alive");
        $limit = isset($_GET['initialSize']) ? $_GET['initialSize'] : 20;
        $last_id = isset($headers['Last-Event-ID']) ? (int) $headers['Last-Event-ID'] : self::push_latest($limit);
        self::loop($last_id);
      } catch (\Exception $e) {
        exit();
      }
    }

    /*
     * Sends the latest status for each device to the client
     */
    private static function push_latest($limit) {
      $last_id;
      $db = DB::get_connection();
      $query = $db->prepare(self::SQL_GET_LATEST);
      $query->bind_param('i', $limit);
      $query->execute();
      $result = $query->get_result();
      while ($row = $result->fetch_assoc()) {
        $last_id = $row['id'];
        echo "id: {$row['id']}\n";
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
          $last_id = $row['id'];
          echo "id: {$row['id']}\n";
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
  AccessLog::handle_request();
?>