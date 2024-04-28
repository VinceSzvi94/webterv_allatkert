<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["user"])) {
	$_SESSION['message'] = "Kérem, jelentkezzen be!";
	$_SESSION['message_type'] = "neutral";
	header('Location: jegyek.php');
	exit;
}
unset($_SESSION['cart']);
$_SESSION['message'] = "Sikeres fizetés! Köszönjük a vásárlást!";
$_SESSION['message_type'] = "success";
header('Location: jegyek.php');
exit;
?>