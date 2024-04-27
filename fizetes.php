<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
unset($_SESSION['cart']);
$_SESSION['message'] = "Sikeres fizetés! Köszönjük a vásárlást!";
$_SESSION['message_type'] = "success";
header('Location: jegyek.php');
exit;
?>