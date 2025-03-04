<?php
require("../config.php");
require("../lib/daemon.php");
require("../lib/database.php");
require("../lib/validate.php");
require("../lib/users.php");

try {
  open_database();
} catch (Exception $e) {
  echo '<span class="error">Caught exception while opening database: ', $e->getMessage(), "</span></div></body></html>";
  exit();
}
try {
  check_database();
} catch (Exception $e) {
  echo '<span class="error">Caught exception while reading database: ', $e->getMessage(), "</span></div></body></html>";
  exit();
}
// Check if user has logged in or not?
require("../lib/login.php");
//
$address = "";
if (logged_in()) {
  $getStatus = walletrpc_post("getStatus");
  $blockCount = $getStatus["blockCount"];
  $knownBlockCount = $getStatus["knownBlockCount"];
  $result = Array();
  $result["count"] = $blockCount;
  $result["known"] = $knownBlockCount;
  header("Content-Type: application/json");
  echo json_encode($result);
} else {
  echo "<span class='error'>Not logged in!</span>";
}
?>
