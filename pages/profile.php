<!-- ===========================
 
in progress

=============================-->








<?php
include "../ini/session.php";

$exp_fetched = mysqli_query($mysqli, "SELECT * FROM expenses WHERE user_id = '$userid'");

if (isset($_POST['save'])) {
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];

    $sql = "UPDATE users SET name = '$fname', surname='$lname' WHERE id='$userid'";
    if (mysqli_query($mysqli, $sql)) {
        $_SESSION["success_message"] = "Profil bol úspešne aktualizovaný.";
    } else {
        $_SESSION["error_message"] = "Nepodarilo sa aktualizovať profil.";
    }
    header('location: profile.php');
    exit;
}

if (isset($_POST['but_upload'])) {
    $name = $_FILES['file']['name'];
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);

    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $extensions_arr = array("jpg", "jpeg", "png", "gif");

    if (in_array($imageFileType, $extensions_arr)) {
        $query = "UPDATE users SET profile_picture = '$name' WHERE id='$userid'";
        mysqli_query($mysqli, $query);
        move_uploaded_file($_FILES['file']['tmp_name'], $target_dir . $name);
        header("Refresh: 2");
        $_SESSION["success_message"] = "Profilová fotka bola úspešne zmenená.";
    } else {
        $_SESSION["error_message"] = "Nepodporovaný typ súboru. Iba JPG, JPEG, PNG, a GIF sú povolené.";
    }
}

if (isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $result = mysqli_query($mysqli, "SELECT password FROM users WHERE id = '$userid'");
    $user = mysqli_fetch_assoc($result);

    if (!password_verify($old_password, $user['password'])) {
        $_SESSION["error_message"] = "Staré heslo je nesprávne.";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION["error_message"] = "Nové heslá sa nezhodujú.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET password = '$hashed_password' WHERE id = '$userid'";
        if (mysqli_query($mysqli, $update_query)) {
            $_SESSION["success_message"] = "Heslo bolo úspešne zmenené.";
        } else {
            $_SESSION["error_message"] = "Nepodarilo sa zmeniť heslo.";
        }
    }
    header('location: profile.php');
    exit;
}
?>

<?php 
if (isset($_SESSION["success_message"])) {
    echo "<div class='success-alert'>" . htmlspecialchars($_SESSION["success_message"]) . "</div>";
    unset($_SESSION["success_message"]);
}
else if (isset($_SESSION["error_message"])) {
    echo "<div class='error-alert'>" . htmlspecialchars($_SESSION["error_message"]) . "</div>";
    unset($_SESSION["error_message"]);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Používateľský profil</title>
    <link rel="stylesheet" href="../styles/main.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sen:wght@400..800&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/0a4f5afbb3.js" crossorigin="anonymous"></script>
</head>



<body>
    <div class="profile-page">
        <?php // include "../inc/sidebar.php"; ?>

        <div class="page-content">
            <div class="container">
                <div class="row center">
                    <div class="profile-form-container">
                        <h2 class="title">Úprava profilu</h2>
                        <hr>
                        <?php if (isset($_SESSION["success_message"]) || isset($_SESSION["error_message"])) : ?>
                            <div class="alert <?php echo isset($_SESSION["success_message"]) ? 'alert-success' : 'alert-error'; ?>">
                                <?php
                                echo $_SESSION["success_message"] ?? $_SESSION["error_message"];
                                unset($_SESSION["success_message"], $_SESSION["error_message"]);
                                ?>
                            </div>
                        <?php endif; ?>

                        <form class="profile-picture-form" method="post" action="" enctype="multipart/form-data">
                            <div class="profile-picture-container">
                                <img
                                    src="<?php echo $userprofile; ?>"
                                    class="profile-picture"
                                    width="120"
                                    alt="Profile Picture">
                            </div>
                            <div class="upload-section">
                                <input type="file" name="file" id="profilepic" class="file-input">
                                <label for="profilepic" class="file-label">Zmeniť fotku</label>
                                <button type="submit" name="but_upload" class="upload-button">Nahrať obrázok</button>
                            </div>
                        </form>

                        <form class="profile-info-form" action="" method="post" autocomplete="off">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name">Meno</label>
                                    <input
                                        type="text"
                                        name="first_name"
                                        id="first_name"
                                        class="input-text"
                                        placeholder="First Name"
                                        value="<?php echo $firstname; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Priezvisko</label>
                                    <input
                                        type="text"
                                        name="last_name"
                                        id="last_name"
                                        class="input-text"
                                        placeholder="Last Name"
                                        value="<?php echo $lastname; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input
                                        type="email"
                                        name="email"
                                        id="email"
                                        class="input-text"
                                        value="<?php echo $useremail; ?>"
                                        disabled>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" name="save" class="save-button">Uložiť zmeny</button>
                            </div>
                            
                        </form>

                        <form class="password-change-form" action="" method="post">
                            <h3>Zmena hesla</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="old_password">Staré heslo</label>
                                    <input type="password" name="old_password" id="old_password" class="input-text" required>
                                </div>
                                <div class="form-group">
                                    <label for="new_password">Nové heslo</label>
                                    <input type="password" name="new_password" id="new_password" class="input-text" required>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Potvrďte nové heslo</label>
                                    <input type="password" name="confirm_password" id="confirm_password" class="input-text" required>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" name="change_password" class="save-button">Zmeniť heslo</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>