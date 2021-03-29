<?php
  namespace Api;
  require_once('../util/autoload.php');

  class Rooms extends AbstractEndpoint {

    const ENTITY = '\Model\Room';

  }

  Rooms::handle_request();
?>