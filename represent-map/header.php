<?php
include "./include/db.php";

// connect to db
mysql_connect($db_host, $db_user, $db_pass) or die(mysql_error());
mysql_select_db($db_name) or die(mysql_error());

// useful functions
function parseInput($value) {
  $value = htmlspecialchars($value);
  $value = str_replace("\r", "", $value);
  $value = str_replace("\n", "", $value);
  return $value;
}


?>