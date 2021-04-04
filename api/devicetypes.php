<?php
  namespace Api;
  require_once('../util/autoload.php');

  /*
   * Endpoint for handling CRUD operations for device types
   */
  class DeviceTypes extends AbstractEndpoint {

    const ENTITY = '\Model\DeviceType';

  }

  DeviceTypes::handle_request();
?>