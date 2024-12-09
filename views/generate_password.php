<?php
$password = 'admin@123';

// Hash the password using bcrypt
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Output the hashed password
echo $hashed_password;
?>
