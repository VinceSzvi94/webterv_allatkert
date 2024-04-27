<!-- felhasználok a userdata.json fájlban! -->
<?php
	require_once 'functions.php';
	user_init();
	$users = load_users("userdata.json");

	$hibak = [];

	if (isset($_POST["regiszt"])) {   // ha az űrlapot elküldték...
		// a kötelezően kitöltendő mezők ellenőrzése
		if (!isset($_POST["email"]) || trim($_POST["email"]) === "")
			$hibak[] = "Adjon meg email címet!";

		if (!isset($_POST["username"]) || trim($_POST["username"]) === "")
			$hibak[] = "Adjon meg felhasználónevet!";
	
		if (!isset($_POST["passwd"]) || trim($_POST["passwd"]) === "" || !isset($_POST["passwd2"]) || trim($_POST["passwd2"]) === "")
		  $hibak[] = "Adja meg a jelszót és az ellenőrző jelszót!";

		// űrlapadatok lementése változókba
		$email = $_POST["email"];
		$felhasznalonev = $_POST["username"];
		$jelszo = $_POST["passwd"];
		$jelszo2 = $_POST["passwd2"];
	
		// foglalt felhasználónév és email ellenőrzése
		foreach ($users as $user) {
			if ($user["username"] === $felhasznalonev)
				$hibak[] = "A megadott felhasználónév már foglalt!";
			if ($user["email"] === $email)
				$hibak[] = "A megadott email címmel már regisztráltak!";
		}

		// email cím ellenőrzése
		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
			$hibak[] = "Adjon meg érvényes email címet!";
	
		// túl rövid jelszó
		if (strlen($jelszo) < 8)
			$hibak[] = "A jelszónak legalább 8 karakter hosszúnak kell lennie!";
	
		// a két jelszó nem egyezik
		if ($jelszo !== $jelszo2)
		  $hibak[] = "A jelszó és az ellenőrző jelszó nem egyezik!";

		// nagy betű, kis betű szám ellenőrzése
		if (!preg_match("/[A-Z]/", $jelszo) || !preg_match("/[a-z]/", $jelszo) || !preg_match("/[0-9]/", $jelszo))
			$hibak[] = "A jelszónak tartalmaznia kell legalább egy nagybetűt, egy kisbetűt és egy számot!";
	
	
		// regisztráció sikerességének ellenőrzése, ha sikeres új felhasználó elmentése
		if (count($hibak) === 0) {
			$new_user[] = array(
				"email" => $email,
				"username" => $felhasznalonev,
				"password" => password_hash($jelszo, PASSWORD_DEFAULT),
				"profpic" => NULL,
				"role" => "user"
			);
			$siker = TRUE;
			save_users("userdata.json", $new_user);
		} else {
			$siker = FALSE;
		}

	}

?>

<!DOCTYPE html>
<html lang="hu">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Kormos Sándor, Szigetvári Vince">
	<title>Regisztráció | Gotham állatkert</title>
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
				<h1>Regisztráció</h1>
				<br>
				<form class="reg_urlap" action="regisztracio.php" method="POST">
					<input type="email" name="email" placeholder="E-mail cím..." value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" required> <br><br>
					<input type="text" name="username" placeholder="Felhasználónév..." value="<?php if (isset($_POST['username'])) echo $_POST['username']; ?>" required> <br><br>
					<input type="password" name="passwd" placeholder="Jelszó..." required> <br><br>
					<input type="password" name="passwd2" placeholder="Jelszó újra..." required> <br><br>
					<div class="formgomb">
						<input type="reset" value="Vissza">
						<input type="submit" name="regiszt" value="Regisztráció">
					</div>
				</form>

				<?php
					if (session_status() == PHP_SESSION_NONE) {
						session_start();
					}

					if (isset($siker) && $siker === TRUE) {  // ha nem volt hiba, akkor a regisztráció sikeres, átirányítás bejelentkezéshez
						$_SESSION["message"] = "Sikeres regisztráció!"; // üzenet a bejelentkezési oldalon
						header("Location: bejelentkezes.php");
						exit;
					} else {                                // az esetleges hibákat kiírjuk egy-egy bekezdésben
						foreach ($hibak as $hiba) {
						echo "<p class='error-message'>" . $hiba . "</p>";
						}
					}
				?>
					
			</div>

			<div class="right-column2">
				<div class="transparent_bg_img">
					<img class="felulrol" src="img/zabizebra.png" alt="zabizebra" title="zabi zebra" style="width:90%">
				</div>
			</div>
		</div>
	</main>

	<?php include_once 'footer.html'; ?>

</body>

</html>