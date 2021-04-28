<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Util\DB;

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
GROUP BY UserAgent;
SQL;
    
const SQL_IP_LOG = <<<SQL
SELECT IpAddress AS ipAddress, COUNT(*) AS visits,  MAX(Time) AS lastVisit
FROM AccessLog
GROUP BY IpAddress;
SQL;

    static function do_get() {
      self::verify_user(true);
      $db = DB::get_connection();
      $db->multi_query(self::SQL_BROWSER_STATS . self::SQL_IP_LOG);
      $output = array();
      $result = $db->use_result();
      $output['browsers'] = $result->fetch_all(MYSQLI_ASSOC);
      $db->next_result();
      $result = $db->use_result();
      $output['ipAddresses'] = $result->fetch_all(MYSQLI_ASSOC);
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
  }

  Statistics::handle_request();
?>