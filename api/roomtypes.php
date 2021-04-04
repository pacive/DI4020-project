<?php
  namespace Api;
  require_once('../util/autoload.php');

  /*
   * Endpoint for handling CRUD operations for room types
   */
  class RoomTypes extends AbstractEndpoint {

    const ENTITY = '\Model\RoomType';

  }

  RoomTypes::handle_request();
?>