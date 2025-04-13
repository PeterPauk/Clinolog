<?php
$host = 'localhost';
$db = 'clinolog';
$user = 'root';
$pass = '';

$mysqli = mysqli_connect($host, $user, $pass, $db);

if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error() ." | Seems like you haven't created the DATABASE with an exact name";
  }
?>
