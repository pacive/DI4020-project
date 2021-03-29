<?php
  namespace Api;
  require_once('../util/autoload.php');

  class DeviceTypes extends AbstractEndpoint {

    const ENTITY = '\Model\DeviceType';

  }

  DeviceTypes::handle_request();
?>