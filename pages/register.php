<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrácia</title>
    <link rel="stylesheet" href="../styles/main.css">
</head>

<body class="body-pre-lukasa-lol">
    <a href="../index.php" class="brand-link">Clinolog</a>
    <div class="box">
        <span class="borderLine"></span>
        <form action="register.php" method="POST">
            <h2>Registrácia</h2>
            <div class="inputBox">
                <input type="text" name="name" id="" required="required">
                <span>Meno</span>
                <i></i>
            </div>
            <div class="inputBox">
                <input type="text" name="surname" id="" required="required">
                <span>Priezvisko</span>
                <i></i>
            </div>
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
            <div class="inputBox">
                <input type="password" name="confirm_password" id="" required="required">
                <span>Opakovať heslo</span>
                <i></i>
            </div>
            <div class="links">
                <a href="login.php">Už máte účet?</a>
            </div>
            <input type="submit" value="Registrovať">
        </form>
    </div>
</body>

</html>

<?php

include '../ini/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $password_regex = '/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*?])[A-Za-z\d!@#$%^&*?]{8,}$/';

    if ($password !== $confirm_password) {
        echo "<script>alert('Heslá sa nezhodujú.'); window.location.href='register.php';</script>";
        exit;
    }

    if (!preg_match($password_regex, $password)) {
        echo "<script>alert('Heslo musí mať aspoň 8 znakov, obsahovať 1 veľké písmeno, 1 číslo, a 1 špeciálny znak.'); window.location.href='register.php';</script>";
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email už existuje. Prosím prihláste sa.'); window.location.href='login.php';</script>";
    } else {
        $sql = "INSERT INTO users (name, surname, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssss", $name, $surname, $email, $hashedPassword);


        if ($stmt->execute()) {
            $_SESSION["email"] = $email;
            $_SESSION["user_id"] = $mysqli->insert_id;
            $_SESSION["name"] = $name;
            $_SESSION["success_message"] = "Registrácia bola úspešná! Momentálne ste prihlásení.";
            header("Location: healthcard.php");
            exit;
        } else {
            echo "<script>alert('Registrácia zlyhala.'); window.location.href='register.php';</script>";
        }
    }
}
?>