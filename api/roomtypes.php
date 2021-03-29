<?php
  namespace Api;
  require_once('../util/autoload.php');

  class RoomTypes extends AbstractEndpoint {

    const ENTITY = '\Model\RoomType';

  }

  RoomTypes::handle_request();
?>