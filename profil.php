<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

?>

<!DOCTYPE html>
<html lang="hu">

<?php

	if (!isset($_SESSION["user"])) {
		header("Location: bejelentkezes.php");
	}
	
	include_once 'user_functions.php';
	$users = load_users("userdata.json");

	$uzenet = "";
	$siker = NULL;
	$bansiker = NULL;
	$unbansiker = NULL;
	$usertochange = $_SESSION["user"]; // a módosítandó felhasználó, alapértelmezetten a bejelentkezett felhasználó (admin választhat mást is)

	// Admin felhasználó választása
	if (isset($_POST["selectuser"])) {   // ha az űrlapot elküldték...
		if (!isset($_POST["usermodify"]) || trim($_POST["usermodify"]) === "") {
			$uzenet = "<strong>Hiba:</strong> Adjon meg egy felhasználónevet!";
			$siker = FALSE;
		}
		else {
			$felhasznalonev = $_POST["usermodify"];
			$uzenet = "<strong>Hiba:</strong> A felhasználó nem létezik.";  // alapból azt feltételezzük, hogy sikertelen
			$siker = FALSE;
			
			foreach ($users["users"] as $user) {
				if ($user["username"] === $felhasznalonev) {
					$usertochange = $user;
					$siker = TRUE;
					break;
				}
			}
		}
	}


	//Hiba: Adminnál mindig az admin értékeit módosítja, hiába választottál más user-t
	if (isset($_POST["user_data_submit"])) {
		$usertochange["életkor"] = $_POST["user_age"];
		$usertochange["nem"] = $_POST["user_nem"];
		$usertochange["vércsoport"] = $_POST["user_ver"];
		
		foreach ($users["users"] as &$user) {
			if ($user["username"] === $usertochange["username"]) {
				$user["életkor"] = $usertochange["életkor"];
				$user["nem"] = $usertochange["nem"];
				$user["vércsoport"] = $usertochange["vércsoport"];
				$_SESSION["user"] = $user; // session user-t is update-elni kell, különben visszatérve erre az oldalra, az előző értékeket mutatja
			}
		}
		
		update_users("userdata.json", $users);
		
	}

	// Tiltás
	if (isset($_POST["selectuserban"])) {   // ha az űrlapot elküldték...
		if (!isset($_POST["userban"]) || trim($_POST["userban"]) === "") {
			$uzenet = "<strong>Hiba:</strong> Adjon meg egy felhasználónevet!";
			$bansiker = FALSE;
		}
		else {
			$felhasznalonev = $_POST["userban"];
			$uzenet = "<strong>Hiba:</strong> A felhasználó nem létezik.";  // alapból azt feltételezzük, hogy sikertelen
			$bansiker = FALSE;
			
			foreach ($users["users"] as &$user) {
				if ($user["username"] === $felhasznalonev) {
					if ($user["role"] == "admin") {
						$uzenet = "<strong>Hiba:</strong> Admin nem tiltható.";
						$bansiker = FALSE;
						break;
					}
					
					$uzenet = "A felhasználó tiltásra került!";
					$user["banned"] = TRUE;
					update_users("userdata.json", $users);
					$bansiker = TRUE;
					break;
				}
			}
		}
	}
	
	// Feloldás
	if (isset($_POST["selectuserunban"])) {   // ha az űrlapot elküldték...
		if (!isset($_POST["userunban"]) || trim($_POST["userunban"]) === "") {
			$uzenet = "<strong>Hiba:</strong> Adjon meg egy felhasználónevet!";
			$unbansiker = FALSE;
		}
		else {
			$felhasznalonev = $_POST["userunban"];
			$uzenet = "<strong>Hiba:</strong> A felhasználó nem létezik.";  // alapból azt feltételezzük, hogy sikertelen
			$unbansiker = FALSE;
			
			foreach ($users["users"] as &$user) {
				if ($user["username"] === $felhasznalonev) {
					if ($user["role"] == "admin") {
						$uzenet = "<strong>Hiba:</strong> Admin nem tiltható.";
						$unbansiker = FALSE;
						break;
					}
					
					$uzenet = "A felhasználó feloldva!";
					$user["banned"] = FALSE;
					update_users("userdata.json", $users);
					$unbansiker = TRUE;
					break;
				}
			}
		}
	}
	
/*
"email" => "admin@gothamzoo.hu",
                
                "password" => password_hash("Admin123", PASSWORD_DEFAULT),
                "profpic" => "img/zoo3.png",
                "role" => "admin",
				"banned" => false,
				"newuser" => false,
*/


//	if ($_SESSION["user"]["role"] == "admin") {
		
		
//	} else {
		
//	}
	
/*
	foreach ($users["users"] as $user) {
		if ($user["username"] === $usertochange) {
			$currentuser = &$user;
		}
	}
*/
?>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Kormos Sándor, Szigetvári Vince">
	<title>Profil | Gotham állatkert</title>
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
			<?php if(isset($_SESSION["user"]) && $_SESSION["user"]["newuser"] == TRUE): ?>
				<h2>Üdvözöljük! Kérjük, adja meg további adatait.</h2>
				<?php
					foreach ($users["users"] as &$user) {
						if ($user["username"] === $_SESSION["user"]["username"]) {
							$user["newuser"] = FALSE;
							$_SESSION["user"] = $user;
							update_users("userdata.json", $users);
							break;
						}
					}
				?>
			<?php endif; ?>


			<?php if(isset($_SESSION["user"]) && $_SESSION["user"]["role"] == "admin"): ?>
				<h1>Felhasználó választása</h1>
				<form class="reg_urlap" method="POST">
					<input type="text" name="usermodify" value=<?php echo $usertochange["username"] ?>> <br><br>
					<input type="submit" class="submitclass" name="selectuser" value="Kiválaszt">
				</form>
				<?php
				if (isset($siker) && $siker === FALSE) {  // ha 0 jegyet akar valaki kosárba tenni
					echo '<p class="error-message">' . $uzenet . '</p>';
					}
				?>
			<?php endif; ?>
			
			<h1>Adatok módosítása</h1>
			<form class="reg_urlap" method="POST">
				Életkor
				<input type="number" name="user_age" value=<?php echo $usertochange["életkor"] ?>>
				Nem
				<select name="user_nem">
					<option value="férfi" <?php if($usertochange["nem"] == "férfi") echo "selected" ?>>férfi</option>
					<option value="nő" <?php if($usertochange["nem"] == "nő") echo "selected" ?>>nő</option>
					<option value="(egyéb)" <?php if($usertochange["nem"] == "(egyéb)") echo "selected" ?>>(egyéb)</option>
				</select>
				Vércsoport
				<select name="user_ver">
					<option value=NULL <?php if($usertochange["vércsoport"] == NULL) echo "selected" ?>> </option>
					<option value="A+" <?php if($usertochange["vércsoport"] == "A+") echo "selected" ?>>A+</option>
					<option value="A-" <?php if($usertochange["vércsoport"] == "A-") echo "selected" ?>>A-</option>
					<option value="B+" <?php if($usertochange["vércsoport"] == "B+") echo "selected" ?>>B+</option>
					<option value="B-" <?php if($usertochange["vércsoport"] == "B-") echo "selected" ?>>B-</option>
					<option value="O+" <?php if($usertochange["vércsoport"] == "O+") echo "selected" ?>>O+</option>
					<option value="O-" <?php if($usertochange["vércsoport"] == "O-") echo "selected" ?>>O-</option>
					<option value="AB+" <?php if($usertochange["vércsoport"] == "AB+") echo "selected" ?>>AB+</option>
					<option value="AB-" <?php if($usertochange["vércsoport"] == "AB-") echo "selected" ?>>AB-</option>
				</select>
				<input type="submit" class="submitclass" name="user_data_submit" value="Mentés">
			</form>
			
			<?php if(isset($_SESSION["user"]) && $_SESSION["user"]["role"] == "admin"): ?>
				<h1>Felhasználó tiltása</h1>
				<form class="reg_urlap" method="POST">
					<input type="text" name="userban" placeholder="Tiltani kívánt"> <br><br>
					<input type="submit" class="submitclass" name="selectuserban" value="Tiltás">
				</form>
				<?php
				if (isset($bansiker) && $bansiker === FALSE) {  // ha 0 jegyet akar valaki kosárba tenni
					echo '<p class="error-message">' . $uzenet . '</p>';
					}
				?>
				<?php
				if (isset($bansiker) && $bansiker === TRUE) {  // ha 0 jegyet akar valaki kosárba tenni
					echo '<p class="error-message">' . $uzenet . '</p>';
					$bansiker = NULL;
					}
				?>
				
				<h1>Tiltás feloldása</h1>
				<form class="reg_urlap" method="POST">
					<input type="text" name="userunban" placeholder="Feloldani kívánt"> <br><br>
					<input type="submit" class="submitclass" name="selectuserunban" value="Feloldás">
				</form>
				<?php
				if (isset($unbansiker) && $unbansiker === FALSE) {  // ha 0 jegyet akar valaki kosárba tenni
					echo '<p class="error-message">' . $uzenet . '</p>';
					}
				?>
				<?php
				if (isset($unbansiker) && $unbansiker === TRUE) {  // ha 0 jegyet akar valaki kosárba tenni
					echo '<p class="error-message">' . $uzenet . '</p>';
					$unbansiker = NULL;
					}
				?>
			<?php endif; ?>
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