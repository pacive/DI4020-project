<?php
  namespace Util;
  if (!defined('ACCESSIBLE') && count(get_included_files()) > 1) {
    define('ACCESSIBLE', true);
  }
  require_once('preventaccess.php');

  /*
   * Enable autoloading of classes
   */
  set_include_path(realpath(__DIR__ . '/../'));
  spl_autoload_register();
?>