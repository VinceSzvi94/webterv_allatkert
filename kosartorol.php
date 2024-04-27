<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
unset($_SESSION['cart']);
$_SESSION['message'] = "Kosár tartalma törölve";
$_SESSION['message_type'] = "neutral";
header('Location: jegyek.php');
exit;
?>