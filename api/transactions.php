<?php
require("../config.php");
require("../lib/daemon.php");
require("../lib/database.php");
require("../lib/validate.php");
require("../lib/users.php");

try {
  open_database();
} catch (Exception $e) {
  echo '<span class="error">Caught exception while opening database: ', $e->getMessage(), "</span>";
  exit();
}
try {
  check_database();
} catch (Exception $e) {
  echo '<span class="error">Caught exception while reading database: ', $e->getMessage(), "</span>";
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
  $getStatus = walletrpc_post("getStatus");
  $blockCount = $getStatus["blockCount"];
  $knownBlockCount = $getStatus["knownBlockCount"];
  echo "<div class='hscroll'>";
  echo "<table id='transactions'>";
  echo "<tr><th>State</th><th>Hash</th><th>Time</th><th>Amount</th><th>Payment ID</th></tr>";
  $ntrans = 0;
  $skip = (array_key_exists('skip', $_GET) && is_numeric($_GET["skip"])) ? $_GET["skip"] : 0;
  if ($skip < 0) {
    $skip = 0;
  }
  unset($firstTime);
  if (array_key_exists('timestamp', $_GET)) {
    $firstTime = is_numeric($_GET['timestamp']) ? $_GET['timestamp'] : 0;
  }
  $info = daemonrpc_get("/getinfo");
  $height = $info["height"];
  //
  $WalletTransactionState = Array("Succeeded", "Failed", "Cancelled", "Created", "Deleted");
  $WalletTransferType = Array("Usual", "Donation", "Change");
  //
  $addresses = Array();
  $addresses[0] = $address;
  $txhashes_params = Array("addresses" => $addresses, "firstBlockIndex" => 0, "blockCount" => $height);
  $txhashes = walletrpc_post("getTransactionHashes", $txhashes_params);
  $blocks = $txhashes["items"];
  // List transactions in reverse order, from newest to oldest
  for ($i = count($blocks) - 1; $i >= 0; $i--) {
    $block = $blocks[$i];
    $transactionHashes = $block["transactionHashes"];
    for ($j = count($transactionHashes) - 1; $j >= 0; $j--) {
      $transactionHash = $transactionHashes[$j];
      $tx_params = Array("transactionHash" => $transactionHash);
      $tx = walletrpc_post("getTransaction", $tx_params);
      if ($tx) {
        $transaction = $tx["transaction"];
        if ($transaction["amount"] != 0) {
          if (isset($firstTime) && $firstTime < $transaction['timestamp']) {
            $skip++;
          }
          if ($ntrans >= $skip && $ntrans < $skip + 20) {
            if ($skip != 0 && !isset($firstTime)) {
              $firstTime = $transaction['timestamp'];
            }
            echo "<tr>";
            echo "<td>" . $WalletTransactionState[$transaction["state"]] . "</td>";
            echo "<td><a href='?hash=" . $transaction["transactionHash"] . "'>" . $transaction["transactionHash"] . "</a></td>";
            echo "<td>" . date("D, d M y H:i:s", $transaction["timestamp"]) . "</td>";
            echo "<td>" . number_format(get_amount($address, $transaction["transactionHash"]) / 100, 2) . "</td>";
            echo "<td>" . $transaction["paymentId"] . "</td>";
            echo "</tr>";
          }
          $ntrans++;
        }
      }
    }
  }
  echo "</table>";
  echo "</div>";
  echo "<table>";
  echo "<tr>";
  if ($skip > 0) {
    echo "<td><form action='index.php' method='post'>";
    echo "<input name='skip' type='hidden' value='0' />";
    echo "<input name='submit' type='submit' class='btn' value='First 20' />";
    echo "</form></td>";
    echo "<td><form action='index.php' method='post'>";
    echo "<input name='skip' type='hidden' value='" . ($skip - 20) . "' />";
    echo "<input name='submit' type='submit' class='btn' value='Previous 20' />";
    echo "</form></td>";
  }
  if ($ntrans > $skip + 20) {
    echo "<td><form action='index.php' method='post'>";
    echo "<input name='skip' type='hidden' value='" . ($skip + 20) . "' />";
    echo "<input name='submit' type='submit' class='btn' value='Next 20' />";
    echo "</form></td>";
    echo "<td><form action='index.php' method='post'>";
    echo "<input name='skip' type='hidden' value='" . ($ntrans - 20) . "' />";
    echo "<input name='submit' type='submit' class='btn' value='Last 20' />";
    echo "</form></td>";
  }
  echo "</tr>";
  echo "</table>";
}
?>

