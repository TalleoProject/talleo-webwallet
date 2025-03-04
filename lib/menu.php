<?php
  echo "<div style='clear: left;'></div>";
  echo "<div class='menu'>";
  echo "<p class='balance' id='balance'>";
  echo "<b>Available balance:</b><br>";
  echo number_format($availableBalance / 100, 2), " TLO<br>";
  echo "<b>Locked balance:</b><br>";
  echo number_format($lockedBalance / 100, 2), " TLO<br>";
  echo "</p><br>";
  echo "<a href='index.php'>Transactions</a><br>";
  echo "<a href='send.php'>Send TLO</a><br>";
  echo "<a href='contacts.php'>Contacts</a><br>";
  echo "<a href='info.php'>Wallet info</a><br>";
  echo "<a href='faucet.php'>Faucet</a><br>";
  echo "<a href='logout.php'>Logout</a><br>";
  echo "<hr>";
  echo "<p class='sync'>";
  echo "<label for='sync'>Sync status:</label><br>";
  $syncPercent = number_format($blockCount / $knownBlockCount * 100, 2);
  echo "<progress id='sync' value='" . $blockCount . "' max='" . $knownBlockCount . "' title='" . $syncPercent . " %'>" . $syncPercent . " %</progress><br/>";
  echo "</p><br>";
  $dt = date("Y");
  echo "<p class='footer'>&copy; ", $dt != "2018" ? "2018&ndash;" : "", date("Y"), " Talleo Project</p>";
  echo "</div>";
  echo "<script type='text/javascript'>
  function updateBalance() {
    fetch('api/balance.php')
      .then((response) => response.text())
      .then((text) => {
        document.getElementById('balance').innerHTML = text;
      })
      .catch(function (err) {
      });
  }
  setInterval(updateBalance, 5000);

  function updateSyncStatus() {
    fetch('api/syncstatus.php')
      .then((response) => response.text())
      .then((text) => {
        var status = JSON.parse(text);
        var el = document.getElementById('sync');
        el.value = status['count'];
        el.max = status['known'];
        var percent = (status['count'] / status['known'] * 100).toFixed(2);
        el.title = percent + ' %';
      })
      .catch(function (err) {
        var el = document.getElementById('sync');
        el.value = 0;
        el.title = '0.00 %';
      });
  }
  setInterval(updateSyncStatus, 5000);
  </script>
  ";
?>
