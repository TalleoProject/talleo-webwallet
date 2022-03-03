
<?php
require("config.php");
require("lib/daemon.php");
require("lib/database.php");
require("lib/validate.php");
require("lib/users.php");

try {
  open_database();
} catch (Exception $e) {
  exit();
}
try {
  check_database();
} catch (Exception $e) {
  exit();
}
$email = '';
if (isset($_POST['email'])) $email = $_POST['email'];
if (isset($_GET['email'])) $email = $_GET['email'];
if (!validate_email($email)) {
   echo '{"error": "Invalid e-mail address!"}';
   exit();
}
if (!email_registered($email)) {
   echo '{"error": "E-mail address is not registered!"}';
   exit();
}
$address = get_address_with_email($email);
echo '{"address": "' . $address . '"}';
?>
