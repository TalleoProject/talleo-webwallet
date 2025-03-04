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
  $spendKey = $_COOKIE['spendKey'];
  if (!validate_spendkey($spendKey)) {
    echo "<span class='error'>Invalid spend key!</span>";
    exit();
  }
  $address = get_address($spendKey);
  $params = Array();
  $params['address'] = $address;
  $getBalance = walletrpc_post("getBalance", $params);
  $availableBalance = array_key_exists("availableBalance", $getBalance) ? $getBalance["availableBalance"] : 0;
  $lockedBalance = array_key_exists("lockedAmount", $getBalance) ? $getBalance["lockedAmount"] : 0;
  echo "<b>Available balance:</b><br>";
  echo number_format($availableBalance / 100, 2), " TLO<br>";
  echo "<b>Locked balance:</b><br>";
  echo number_format($lockedBalance / 100, 2), " TLO<br>";
} else {
  echo "<span class='error'>Not logged in!</span>";
}
?>
