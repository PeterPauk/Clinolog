<!-- ===========================
 
in progress

=============================-->








<?php
include "config.php";
session_start();

if (!isset($_SESSION["email"]) && basename($_SERVER['PHP_SELF']) != 'register.php') {
    header("Location: login.php");
    exit();
}


$sess_email = $_SESSION["email"];
$sql = "SELECT id, name, surname, email, profile_picture FROM users WHERE email = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $sess_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $userid = $user["id"];
    $firstname = $user["name"];
    $lastname = $user["surname"];
    $username = $user["name"] . " " . $user["surname"];
    $useremail = $user["email"];
    $userprofile="../uploads/".$user["profile_picture"];
} else {
    header("Location: login.php");
    exit();
}
?>