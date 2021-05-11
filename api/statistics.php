<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Util\DB;
  use \Util\Utils;

  /*
   * Endpoint for handling CRUD operations for devices
   */
  class Statistics extends AbstractEndpoint {

//SELECT AL.Page as page, IFNULL(U.userName, '<unknown>') as user, COUNT(AL.Page) as visits FROM AccessLog AL
//LEFT JOIN Users U ON U.userId = AL.UserId
//GROUP BY AL.Page, AL.UserId

const SQL_BROWSER_STATS = <<<SQL
SELECT UserAgent AS userAgent, COUNT(*) AS visits
FROM AccessLog
WHERE Time > ?
GROUP BY UserAgent;
SQL;
    
const SQL_IP_LOG = <<<SQL
SELECT IpAddress AS ipAddress, COUNT(*) AS visits,  MAX(Time) AS lastVisit
FROM AccessLog
WHERE Time > ?
GROUP BY IpAddress;
SQL;

    static function do_get() {
      self::verify_user(true);
      $date = self::get_date_str(Utils::get_or_default($_GET, 'period', 'all'));
      $output = array();
      $output['browsers'] = self::db_get(self::SQL_BROWSER_STATS, $date);
      $output['ipAddresses'] = self::db_get(self::SQL_IP_LOG, $date);
      return json_encode($output);
    }

    /*
     * Handler for POST requests. Not supported for this endpoint, so returns a 405 response
     */
    static function do_post(&$body) {
      http_response_code(405);
    }

    /*
     * Handler for PUT requests. Not supported for this endpoint, so returns a 405 response
     */
    static function do_put(&$body) {
      http_response_code(405);
    }

    /*
     * Handler for DELETE requests. Not supported for this endpoint, so returns a 405 response
     */
    static function do_delete() {
      http_response_code(405);
    }

    private static function db_get($sql, $date) {
      $db = DB::get_connection();
      $query = $db->prepare($sql);
      $query->bind_param('s', $date);
      $query->execute();
      return $query->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    private static function get_date_str($period) {
      if ($period == 'all') { return '0'; }
      try {
        $interval = new \DateInterval('P'.strtoupper($period));
        return (new \DateTime())->sub($interval)->format('Y-m-d H:i:s');  
      } catch (Exception $e) {
        http_response_code(400);
        exit('Invalid period');
      }
    }
  }

  Statistics::handle_request();
?>