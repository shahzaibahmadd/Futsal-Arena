<?php
$password = 'admin1122'; // The password you want to hash
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
echo $hashed_password;
?>
