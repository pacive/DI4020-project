<?php
  namespace Api;
  require_once('../util/autoload.php');

  class Devices extends AbstractEndpoint {

    const ENTITY = '\Model\Device';

  }

  Devices::handle_request();
?>