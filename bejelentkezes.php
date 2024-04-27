<!DOCTYPE html>
<html lang="hu">

<?php
	if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

	// felhasználók tömb betöltése
	include_once 'user_functions.php';
	user_init();
	$users = load_users("userdata.json");

	$uzenet = "";
	$siker = NULL;

	if (isset($_POST["login"])) {    // miután az űrlapot elküldték...
		if (!isset($_POST["username"]) || trim($_POST["username"]) === "" || !isset($_POST["passwd"]) || trim($_POST["passwd"]) === "") {
			$uzenet = "<strong>Hiba:</strong> Adj meg minden adatot!";
			$siker = FALSE;
		}
		else {
			$felhasznalonev = $_POST["username"];
			$jelszo = $_POST["passwd"];

			$uzenet = "Sikertelen belépés! A belépési adatok nem megfelelők!";  // alapból azt feltételezzük, hogy a bejelentkezés sikertelen
			$siker = FALSE;
	
			foreach ($users["users"] as $user) {
				if ($user["username"] === $felhasznalonev && password_verify($jelszo, $user["password"])) {
					$uzenet = "Sikeres belépés!";
					$_SESSION["user"] = $user;
					$siker = TRUE;
					header("Location: index.php");
				}
			}
		}
	}
?>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Kormos Sándor, Szigetvári Vince">
	<title>Bejelentkezés | Gotham állatkert</title>
	<link rel="stylesheet" href="css/style.css">
	<link rel="icon" href="img/zoo3.png">
	<meta name="keywords" content="Gotham, allatkert, zoo, Hungary">
	<meta name="description" content="Gotham állatkert honlapja.">
</head>

<body class="egyeboldal">
	
	<?php include_once 'header.php'; ?>

	<main>
		<div class="main_multicolumn-content-wrapper">

			<div class="left-column2">

				<!-- sikeres regisztráció üzenet, ha most regesztrált a felhasználó -->
				<?php
				if (isset($_SESSION["message"])) {
					echo '<p class="success-message">' . $_SESSION["message"] . '</p><br>';
					unset($_SESSION["message"]);
				}
				?>

				<h1>Bejelentkezés</h1>
				<br>
				<form class="reg_urlap" method="POST">
					<input type="text" name="username" placeholder="Felhasználónév..." required> <br><br>
					<input type="password" name="passwd" placeholder="Jelszó..." required> <br><br>
					<input type="submit" class="submitclass" name="login" value="Bejelentkezés">
				</form>

				<?php
				if (isset($siker) && $siker === FALSE) {  // ha 0 jegyet akar valaki kosárba tenni
					echo '<p class="error-message">' . $uzenet . '</p>';
					}
				?>

			</div>

			<div class="right-column2">
				<div class="transparent_bg_img">
					<img class="alulrol" src="img/Male%20Yellow%20Tiger%20-%20481x830.png" alt="tigrisToni" title="tigris Tóni" style="width:60%">
				</div>
			</div>
		</div>
	</main>

	<?php include_once 'footer.html'; ?>

</body>

</html>