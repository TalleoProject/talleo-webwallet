<?php
//echo "Entering lib/validate.php...<br>";

function validate_address($address) {
  $valid = "123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";
  if (strlen($address) != 97) {
    return false;
  }
  if (substr($address, 0, 2) != "TA") {
    return false;
  }
  for ($i = 2; $i < 97; $i++) {
    if (strpos($valid, $address{$i}) === false) {
      return false;
    }
  }
  return true;
}

function validate_hex($str, $len) {
  $valid = "0123456789abcdef";
  if (strlen($str) != $len) {
    return false;
  }
  for ($i = 0; $i < $len; $i++) {
    if (stripos($valid, $str{$i}) === false) {
      return false;
    }
  }
  return true;
}

function validate_spendkey($spendKey) {
  return validate_hex($spendKey, 64);
}

function validate_paymentid($paymentId) {
  return validate_hex($paymentId, 64);
}

function validate_txhash($txhash) {
  return validate_hex($txhash, 64);
}

function validate_email($email) {
  $valid1 = "0123456789abcdefghijklmnopqrstuvwxyz.-+_";
  $valid2 = "0123456789abcdefghijklmnopqrstuvwxyz.-";
  $numbers = "0123456789";
  $nonzero = "123456789";
  // This is placeholder address for user to enter a real e-mail address, so reject it
  if ($email == "youremail@domain.com") {
    return false;
  }
  $at = strpos($email, '@');
  if ($at === false) {
    return false;
  }
  if ($at === 0) {
    return false;
  }
  if ($at == (strlen($email) - 1)) {
    return false;
  }
  $user = substr($email, 0, $at);
  $domain = substr($email, $at + 1);
  for ($i = 0; $i < strlen($user); $i++) {
    if (strpos($valid1, $user{$i}) === false) {
      return false;
    }
  }
  $dot = strpos($domain, ".");
  if ($dot === false) {
    return false;
  }
  // last segment of domain must have atleast two characters or a non-zero number
  if ($dot == (strlen($domain) - 2) && strpos($nonzero, $domain[-1]) === false) {
    return false;
  }
  if ($dot == (strlen($domain) - 1)) {
    return false;
  }
  // Segment can't be empty
  if (strpos($domain, "..") !== false) {
    return false;
  }
  // IP address?
  if (strpos($numbers, $domain[-1]) !== false) {
    if (substr_count($domain, ".") != 3) {
      return false;
    }
    $tok = strtok($domain, ".");
    while ($tok !== false) {
      for ($i = 0; $i < strlen($tok); $i++) {
        if (strpos($numbers, $tok[$i]) === false) {
          return false;
        }
      }
      if (intval($tok) > 255) {
        return false;
      }
      $tok = strtok(".");
    }
  }
  for ($i = 0; $i < strlen($domain); $i++) {
    if (strpos($valid2, $domain{$i}) === false) {
      return false;
    }
  }
  return true;
}

function validate_int($amount) {
  $valid = "0123456789";
  for ($i = 0; $i < strlen($amount); $i++) {
    if (strpos($valid, $amount{$i}) === false) {
      return false;
    }
  }
  return true;
}

function validate_amount($amount) {
  $valid = "0123456789";
  $dot = strpos($amount, ".");
  if ($dot === false) {
    return validate_int($amount);
  }
  if ($dot === 0) {
    return false;
  }
  if ($dot != (strlen($amount) - 3)) {
    return false;
  }
  for ($i = 0; $i < $dot; $i++) {
    if (strpos($valid, $amount{$i}) === false) {
      return false;
    }
  }
  for ($i = $dot + 1; $i < strlen($amount); $i++) {
    if (strpos($valid, $amount{$i}) === false) {
      return false;
    }
  }
  return true;
}

function validate_contact_name($name) {
  if (strlen($name) < 1 || strlen($name) > 64) {
    return false;
  }
  if (strpos($name, "'") !== false) {
    return false;
  }
  if (strpos($name, '"') !== false) {
    return false;
  }
  if (strpos($name, "\\") !== false) {
    return false;
  }
  for ($i = 0; $i < strlen($name); $i++) {
    if (ord($name{$i}) < 32) {
      return false;
    }
  }
  return true;
}

function validate_url($url) {
  if (!filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
    return false;
  }
  if (substr($url, 0, 7) == 'http://') {
    return true;
  }
  if (substr($url, 0, 8) == 'https://') {
    return true;
  }
  return false;
}
//echo "Leaving lib/validate.php...<br>";
?>
