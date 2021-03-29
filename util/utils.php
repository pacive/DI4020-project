<?php
  namespace Util;

  class Utils {

    static function validate_input(&$arr, $required_fields = array()) {
      foreach ($required_fields as $field) {
        if (!isset($arr[$field])) {
          return false;
        }
      }
      return true;
    }

    static function get_or_default(&$arr, $key, $default = null) {
      return isset($arr[$key]) ? $arr[$key] : $default;
    }
  }
?>