<?php
include("../ini/config.php");
include("../ini/session.php");
include("../ini/alerts.php");

if (isset($_POST['but_upload'])) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $file_type = $_FILES['file']['type'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_name = basename($_FILES['file']['name']);
    $target_dir = "../uploads/";
    $target_file = $target_dir . $file_name;

    if (in_array($file_type, $allowed_types)) {
        if (move_uploaded_file($file_tmp, $target_file)) {
            $sql = "UPDATE users SET profile_picture=? WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $target_file, $userid);
            $stmt->execute();

            $userprofile = $target_file;
            set_alert("success", "Obrázok bol úspešne aktualizovaný!");
        } else {
            set_alert("error", "Chyba pri nahrávaní súboru.");
        }
    } else {
        set_alert("error", "Nepodporovaný formát súboru. Povolené sú: JPG, JPEG, PNG, GIF.");
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['save'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];

    $sql = "UPDATE users SET name='$first_name', surname='$last_name' WHERE id='$userid'";
    mysqli_query($mysqli, $sql);

    $firstname = $first_name;
    $lastname = $last_name;
    set_alert("success", "Profil bol úspešne aktualizovaný.");

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $sql = "SELECT password FROM users WHERE id='$userid'";
    $result = mysqli_query($mysqli, $sql);
    $row = mysqli_fetch_assoc($result);

    if (password_verify($old_password, $row['password'])) {
        if ($new_password == $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password='$hashed_password' WHERE id='$userid'";
            mysqli_query($mysqli, $sql);
            set_alert("success", "Heslo bolo úspešne zmenené.");
        } else {
            set_alert("error", "Nové heslá sa nezhodujú.");
        }
    } else {
        set_alert("error", "Zadané staré heslo nie je správne.");
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link rel="stylesheet" href="../styles/main.css">
</head>

<body>
    <a href="../index.php" class="brand-link">Clinolog</a>
    <div class="page-content">
        <div class="container">
            <div class="card profile-card">
                <h2 class="card-title">Úprava profilu</h2>
                <hr>

                <form class="profile-picture-form" method="post" enctype="multipart/form-data">
                    <div class="profile-picture-container">
                        <img src="<?php echo $userprofile; ?>" class="profile-picture" alt="Profilový obrázok">
                    </div>
                    <div class="upload-section">
                        <label for="profilepic" class="file-label">Vybrať fotku</label>
                        <input type="file" name="file" id="profilepic" class="file-input">
                        <button type="submit" name="but_upload" class="upload-button">Nahrať obrázok</button>
                    </div>
                </form>

                <form class="profile-info-form" method="post">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="first_name">Meno</label>
                            <input type="text" name="first_name" id="first_name" class="input-text" value="<?php echo $firstname; ?>">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Priezvisko</label>
                            <input type="text" name="last_name" id="last_name" class="input-text" value="<?php echo $lastname; ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="input-text" value="<?php echo $useremail; ?>" disabled>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="save" class="save-button">Uložiť zmeny</button>
                    </div>
                </form>

                <form class="password-change-form" method="post">
                    <h3>Zmena hesla</h3>
                    <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>
                    <div class="form-grid">
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
</body>

</html>