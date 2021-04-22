<?php
if (count(preg_grep('~^/home/stud/.*/public_html/project/util/autoload.php~', get_included_files())) == 0) {
  http_response_code(404);
  exit();
}
?>