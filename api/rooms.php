<?php
  namespace Api;
  require_once('../util/autoload.php');
  use \Model\Room;

  /*
   * Endpoint for handling CRUD operations for devices
   */
  class Rooms extends AbstractEndpoint {

    const ENTITY = '\Model\Room';

    /*
     * Handler for a GET request. Gets the corresponding room from the db
     * and returns it json-encoded, or all rooms if no id is provided in the query.
     * If the query contains includeDevices=true includes the devices for the room.
     */
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