<?php
  namespace Util;
  require_once('preventaccess.php');
  
  /*
    * Various utility functions
    */
  class Utils {

    /*
     * Checks if an array contains the specified keys
     */
    static function validate_input(&$arr, $required_fields = array()) {
      foreach ($required_fields as $field) {
        if (!isset($arr[$field])) {
          return false;
        }
      }
      return true;
    }

    /*
     * Gets a value from an array, or a default one if the key doesn't exist
     */
    static function get_or_default(&$arr, $key, $default = null) {
      return isset($arr[$key]) ? $arr[$key] : $default;
    }
  }
?>