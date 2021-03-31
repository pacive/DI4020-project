<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Model\Room;

  class Rooms extends AbstractEndpoint {

    const ENTITY = '\Model\Room';

    protected static function do_get() {
      $incl_devices = isset($_GET['includeDevices']) && (bool) $_GET['includeDevices'];
      if (isset($_GET['id'])) {
        if ($entity = Room::get_by_id($_GET['id'], $incl_devices)) {
          return json_encode($entity);
        } else {
          http_response_code(404);
          return 'Room not found';
        }
      } else {
        return json_encode(Room::get_all($incl_devices));
      }
    }
  }

  Rooms::handle_request();
?>