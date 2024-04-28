<!DOCTYPE html>
<html lang="hu">

<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

	if (!isset($_SESSION["user"])) {
		header("Location: bejelentkezes.php");
	}
	
	include_once 'user_functions.php';
	$users = load_users("userdata.json");

	$uzenet = "";
	$siker = NULL;
	$bansiker = NULL;
	$unbansiker = NULL;
	$pwdsiker = NULL;
	$adminpwdchsiker = NULL;
	$usertochange = $_SESSION["user"]; // a módosítandó felhasználó, alapértelmezetten a bejelentkezett felhasználó (admin választhat mást is)
	$pfp = $usertochange["profpic"];
	$pfp = "img/profpics/" . $pfp;

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


	//Hiba: Adminnál mindig az admin értékeit módosítja, hiába választottál más user-t --> módosítás mezők rejtve adminnak
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
				update_users("userdata.json", $users);
			}
		}
		
		
		
	}

//	//Profilkép váltás
//	if (isset($_POST["pfp_change"])) {
//		
//	}
//	if (isset($_POST["pfp_del"])) {
//		foreach ($users["users"] as &$user) {
//			if ($user["username"] === $usertochange["username"]) {
//				$user["profpic"] = NULL;
//				$pfp = NULL;
//				// kép törlése is
//				update_users("userdata.json", $users);
//				$_SESSION["user"] = $user;
//			}
//		}
//	}

	//Jelszóváltás
	if (isset($_POST["change_pwd"])) {
		if (!isset($_POST["passwdold"]) || trim($_POST["passwdold"]) === "" || !isset($_POST["passwd"]) || trim($_POST["passwd"]) === "" || !isset($_POST["passwd2"]) || trim($_POST["passwd2"]) === "") {
			$uzenet = "<strong>Hiba:</strong> Adj meg minden adatot!";
			$pwdsiker = FALSE;
		}
		else {
			$jelszo = $_POST["passwdold"];
			$ujjelszo = $_POST["passwd"];
			$ujjelszo2 = $_POST["passwd2"];
			$uzenet = "Sikertelen jelszóváltás!";  // alapból azt feltételezzük, hogy sikertelen
			$pwdsiker = FALSE;
	
			foreach ($users["users"] as &$user) {
				if ($user["username"] === $usertochange["username"]) {
					if (!password_verify($jelszo, $user["password"])) {
						$uzenet = "A régi jelszó nem helyes!";
						$pwdsiker = FALSE;
						break;
					}
					if (strlen($ujjelszo) < 8) {
						$uzenet = "Az új jelszó nem elég hosszú!";
						$pwdsiker = FALSE;
						break;
					}
					if ($ujjelszo !== $ujjelszo2) {
						$uzenet = "A két új jelszó nem egyezik!";
						$pwdsiker = FALSE;
						break;
					}
					if (!preg_match("/[A-Z]/", $ujjelszo) || !preg_match("/[a-z]/", $ujjelszo) || !preg_match("/[0-9]/", $ujjelszo)) {
						$uzenet = "Az új jelszónak tartalmaznia kell legalább egy nagybetűt, egy kisbetűt és egy számot!";
						$pwdsiker = FALSE;
						break;
					}
					if (password_verify($ujjelszo, $user["password"])) {
						$uzenet = "Az új jelszó nem egyezhet a régivel!";
						$pwdsiker = FALSE;
						break;
					}
					
					$user["password"] = password_hash($ujjelszo, PASSWORD_DEFAULT);
					update_users("userdata.json", $users);
					
					$uzenet = "A jelszavát sikeresen megváltoztatta.";
					$pwdsiker = TRUE;
					break;
				}
			}
		}
	}

	// Admin jelszóátírás
	if (isset($_POST["selectuserpwdchange"])) {
		if (!isset($_POST["userpwdchangename"]) || trim($_POST["userpwdchangename"]) === "" || !isset($_POST["userpwdchangepwd"]) || trim($_POST["userpwdchangepwd"]) === "") {
			$uzenet = "<strong>Hiba:</strong> Töltsön ki minden adatot.";
			$adminpwdchsiker = FALSE;
		}
		else {
			$felhasznalonev = $_POST["userpwdchangename"];
			$ujjelszo = $_POST["userpwdchangepwd"];
			$uzenet = "<strong>Hiba:</strong> A felhasználó nem létezik.";  // alapból azt feltételezzük, hogy sikertelen
			$adminpwdchsiker = FALSE;
			
			foreach ($users["users"] as &$user) {
				if ($user["username"] === $felhasznalonev) {
					$user["password"] = password_hash($ujjelszo, PASSWORD_DEFAULT);
					update_users("userdata.json", $users);
					
					$uzenet = "A felhasználó jelszava átírásra került.";
					$adminpwdchsiker = TRUE;
					break;
				}
			}
		}
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
			<!--Üzenetek-->
			<?php //admin felhasználóváltás
				if (isset($siker) && $siker === FALSE) {
					echo '<p class="error-message">' . $uzenet . '</p>';
					$siker = NULL;
					}
			?>
			<?php
				if (isset($pwdsiker) && $pwdsiker === FALSE) { 
					echo '<p class="error-message">' . $uzenet . '</p>';
					$pwdsiker = NULL;
				}
			?>
			<?php
				if (isset($pwdsiker) && $pwdsiker === TRUE) { 
					echo '<p class="success-message">' . $uzenet . '</p>';
					$pwdsiker = NULL;
					}
			?>
			<?php
				if (isset($adminpwdchsiker) && $adminpwdchsiker === FALSE) {
					echo '<p class="error-message">' . $uzenet . '</p>';
					$adminpwdchsiker = NULL;
					}
			?>
			<?php
				if (isset($adminpwdchsiker) && $adminpwdchsiker === TRUE) {
					echo '<p class="success-message">' . $uzenet . '</p>';
					$adminpwdchsiker = NULL;
					}
			?>
			<?php
				if (isset($bansiker) && $bansiker === FALSE) {
					echo '<p class="error-message">' . $uzenet . '</p>';
					$bansiker = NULL;
					}
			?>
			<?php
				if (isset($bansiker) && $bansiker === TRUE) {
					echo '<p class="success-message">' . $uzenet . '</p>';
					$bansiker = NULL;
					}
			?>
			<?php
				if (isset($unbansiker) && $unbansiker === FALSE) {
					echo '<p class="error-message">' . $uzenet . '</p>';
					$unbansiker = NULL;
					}
			?>
			<?php
				if (isset($unbansiker) && $unbansiker === TRUE) {
					echo '<p class="success-message">' . $uzenet . '</p>';
					$unbansiker = NULL;
					}
			?>
			
			<!--Üdvözlő üzenet első belépőknek-->
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

			
			<!--Admin felhasználóváltás-->
			<?php if(isset($_SESSION["user"]) && $_SESSION["user"]["role"] == "admin"): ?>
				<h1>(Admin) Felhasználó választása</h1>
				<p>Megj.: nem lehet átírni más felhasználó adatait<p>
				<form class="reg_urlap" method="POST">
					<input type="text" name="usermodify" value=<?php echo $usertochange["username"] ?>> <br><br>
					<input type="submit" class="submitclass" name="selectuser" value="Kiválaszt">
				</form>
			<?php endif; ?>
			
			<!--Misc adatok-->
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
						<option value=NULL <?php if($usertochange["vércsoport"] == NULL) echo "selected" ?>>-</option>
						<option value="A+" <?php if($usertochange["vércsoport"] == "A+") echo "selected" ?>>A+</option>
						<option value="A-" <?php if($usertochange["vércsoport"] == "A-") echo "selected" ?>>A-</option>
						<option value="B+" <?php if($usertochange["vércsoport"] == "B+") echo "selected" ?>>B+</option>
						<option value="B-" <?php if($usertochange["vércsoport"] == "B-") echo "selected" ?>>B-</option>
						<option value="O+" <?php if($usertochange["vércsoport"] == "O+") echo "selected" ?>>O+</option>
						<option value="O-" <?php if($usertochange["vércsoport"] == "O-") echo "selected" ?>>O-</option>
						<option value="AB+" <?php if($usertochange["vércsoport"] == "AB+") echo "selected" ?>>AB+</option>
						<option value="AB-" <?php if($usertochange["vércsoport"] == "AB-") echo "selected" ?>>AB-</option>
					</select>
					<?php if($_SESSION["user"]["username"] == $usertochange["username"]): ?> <!--admin nem módosíthatja másét-->
					<input type="submit" class="submitclass" name="user_data_submit" value="Mentés">
					<?php endif; ?>
				</form>
			
			<!--Profilkép-->
				<h1>Profilkép</h1>
				<form class="reg_urlap" method="POST">
					<?php if($usertochange["profpic"] == NULL): ?>
					Nincs profilképe
					<?php endif; ?>
					<?php if($usertochange["profpic"] !== NULL): ?>
						<img src=<?php echo '"' . $pfp . '"' ?> alt="profilkép" width = "200"> <br><br>
					<?php endif; ?>
					
					<?php if($_SESSION["user"]["username"] == $usertochange["username"]): ?> <!--admin nem módosíthatja másét-->
					<!--<input type="file" id="file-upload" name="pfp_upload" accept="image/*"/> <br>
					<input type="submit" class="submitclass" name="pfp_change" value="Feltöltés">
					<input type="submit" class="submitclass" name="pfp_del" value="Törlés">-->
					<?php endif; ?>
				</form>
			
			<!--Jelszóváltás-->
			<?php if($_SESSION["user"]["username"] == $usertochange["username"]): ?> <!--admin nem módosíthatja másét-->
				<h1>Jelszó módosítása</h1>
				<form class="reg_urlap" method="POST">
					Új jelszó
					<input type="password" name="passwd" placeholder="Jelszó (min 8 kar., kis- és nagybetű, szám)" required> <br><br>
					<input type="password" name="passwd2" placeholder="Jelszó újra" required> <br><br>
					
					Mostani jelszó
					<input type="password" name="passwdold" placeholder="Mostani jelszó" required> <br><br>
					<input type="submit" class="submitclass" name="change_pwd" value="Jelszóváltás">
				</form>
			<?php endif; ?>

			<!--Admin un/ban-->
			<?php if(isset($_SESSION["user"]) && $_SESSION["user"]["role"] == "admin"): ?>
				<h1>(Admin) Felhasználó jelszavának átírása</h1>
				<p>Megj.: nem ellenőrzi a szabályokat<p>
				<form class="reg_urlap" method="POST">
					<input type="text" name="userpwdchangename" placeholder="Felhasználó"> <br><br>
					<input type="text" name="userpwdchangepwd" placeholder="Új jelszó"> <br><br>
					<input type="submit" class="submitclass" name="selectuserpwdchange" value="Módosítás">
				</form>
				<h1>(Admin) Felhasználó tiltása</h1>
				<form class="reg_urlap" method="POST">
					<input type="text" name="userban" placeholder="Tiltani kívánt"> <br><br>
					<input type="submit" class="submitclass" name="selectuserban" value="Tiltás">
				</form>
				<h1>(Admin) Tiltás feloldása</h1>
				<form class="reg_urlap" method="POST">
					<input type="text" name="userunban" placeholder="Feloldani kívánt"> <br><br>
					<input type="submit" class="submitclass" name="selectuserunban" value="Feloldás">
				</form>
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