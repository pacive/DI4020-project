<?php
  namespace Api;
  require_once('../util/autoload.php');

  /*
   * Endpoint for handling CRUD operations for devices
   */
  class Devices extends AbstractEndpoint {

    const ENTITY = '\Model\Device';

  }

  Devices::handle_request();
?>