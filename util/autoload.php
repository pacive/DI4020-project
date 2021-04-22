<?php
  namespace Util;
  require_once('preventaccess.php');

  /*
   * Enable autoloading of classes
   */
  set_include_path(realpath(__DIR__ . '/../'));
  spl_autoload_register();
?>