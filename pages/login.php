<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/main.css">
    <title>Prihlásenie</title>
</head>

<body class="body-pre-lukasa-lol">
    <div class="box">
        <span class="borderLine"></span>
        <form action="login.php" method="POST">
            <h2>Prihlásenie</h2>
            <div class="inputBox">
                <input type="email" name="email" id="" required="required">
                <span>Email</span>
                <i></i>
            </div>
            <div class="inputBox">
                <input type="password" name="password" id="" required="required">
                <span>Heslo</span>
                <i></i>
            </div>
            <div class="links">
                <!-- <a href="#">Forgot Password?</a> -->
                <a href="register.php">Registrácia</a>
            </div>
            <input type="submit" value="Prihlásiť">
        </form>
    </div>
</body>

</html>

<?php

include '../ini/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            session_unset();
            $_SESSION["email"] = $user["email"];
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["success_message"] = "Login successful!";

            header("Location: healthcard.php");
            exit();
        } else {
            echo "<script>alert('Nesprávne heslo.'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Účet neexistuje. Prosím zaregistrujte sa.'); window.location.href='register.php';</script>";
    }
}
?>