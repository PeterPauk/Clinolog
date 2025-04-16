<?php

function set_alert($type, $message) {
    if ($type === 'success') {
        $_SESSION["success_message"] = $message;
    } elseif ($type === 'error') {
        $_SESSION["error_message"] = $message;
    }
}

if (isset($_SESSION["success_message"])) {
    echo "<div class='alert success-alert'>" . htmlspecialchars($_SESSION["success_message"]) . "</div>";
    unset($_SESSION["success_message"]);
} elseif (isset($_SESSION["error_message"])) {
    echo "<div class='alert error-alert'>" . htmlspecialchars($_SESSION["error_message"]) . "</div>";
    unset($_SESSION["error_message"]);
}
?>
