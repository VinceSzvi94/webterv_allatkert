<!DOCTYPE html>
<html lang="hu">

<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
	include_once 'hir_functions.php';
	include_once 'Comment.php';

	if (isset($_POST["ujhir"])) {
		$cim = $_POST["cim"];
		$hirnev = $_POST["hirnev"];
		$hirtest = $_POST["hirtest"];
		$media = $_FILES["media"]; // "hirek/media/" . $_FILES["media"];

		if (preg_match('/^[a-zA-Z0-9_]+$/', $hirnev)) {
			$siker = TRUE;
			$uzenet = "A hír sikeresen hozzáadva!";
			$hir = array(
				"cim" => $cim,
				"hirnev" => $hirnev,
				"datum" => date("Y-m-d H:i:s"),
				"hirtest" => $hirtest,
				"media" => $media,
				"likes" => array(),
			);
			$path = "hirek/" . $hirnev . ".json";
			create_hir($path, $hir);

			// ha még nincs, lista is a hírek címeivel, időrendben a legkorábban kreált hír lesz elől
			if (!file_exists("hirek/hirlista.json")) {
				$hirek = array("hirek" => [$hirnev]);
				file_put_contents("hirek/hirlista.json", json_encode($hirek));
			} else {
				$hirek = file_get_contents("hirek/hirlista.json");
				$hirek = json_decode($hirek, true);
				$hirek["hirek"] = array_merge($hirek["hirek"], [$hirnev]);
				$json_data = json_encode($hirek, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
				file_put_contents("hirek/hirlista.json", $json_data);
			}
		}
		else {
			$siker = FALSE;
			$uzenet = "Az azonosító csak betűket, számokat és alulvonást tartalmazhat!";
		}
	}

	// hírek like/unlike-olása
	if (isset($_GET['like']) && isset($_GET['user'])) {
		$hirnev = $_GET['like'];
		$user = $_GET['user'];
	
		$hirpath = "hirek/" . $hirnev . ".json";
		$hir_arr = json_decode(file_get_contents($hirpath), true);
		$hir = $hir_arr[0];
	
		if (!in_array($user, $hir['likes'])) {
			$hir['likes'][] = $user;
		}

		$hir_arr[0] = $hir;
		file_put_contents($hirpath, json_encode($hir_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
		
		header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
		exit;
	}
	if (isset($_GET['unlike']) && isset($_GET['user'])) {
		$hirnev = $_GET['unlike'];
		$user = $_GET['user'];
	
		$hirpath = "hirek/" . $hirnev . ".json";
		$hir_arr = json_decode(file_get_contents($hirpath), true);
		$hir = $hir_arr[0];
	
		if (in_array($user, $hir['likes'])) {
			$hir['likes'] = array_diff($hir['likes'], [$user]);
		}

		$hir_arr[0] = $hir;
		file_put_contents($hirpath, json_encode($hir_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
		
		header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
		exit;
	}

	// kommentek like/unlike-olása
?>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Kormos Sándor, Szigetvári Vince">
	<title>Hírek | Gotham állatkert</title>
	<link rel="stylesheet" href="css/style.css">
    <!-- Font Awesome használata a like-okhoz -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
	<link rel="icon" href="img/zoo3.png">
	<meta name="keywords" content="Gotham, allatkert, zoo, Hungary">
	<meta name="description" content="Gotham állatkert honlapja.">
</head>

<body class="egyeboldal">

	<?php include_once 'header.php'; ?>

	<main>
		<div class="main-content-wrapper">
			<!-- Az állatkert bemutatása, pár random kép + ChatGPT rizsa -->
			<?php if(isset($_SESSION["user"]) && $_SESSION["user"]["role"] == "admin"): ?>
				<h1>Hír hozzáadása</h1>
				<br>
				<form class="reg_urlap" action="hirek.php" method="POST" enctype="multipart/form-data">
					<input type="text" name="cim" placeholder="Cím..."> <br>
					<input type="text" name="hirnev" placeholder="Azonosító..."> <br>
					<textarea id="hirtest" name="hirtest" maxlength="2222"></textarea> <br>
					<input type="file" id="file-upload" name="media" accept="image/*,video/mp4"/> <br>
					<div class="formgomb">
						<input type="reset" value="Törlés">
						<input type="submit" class="submitclass" name="ujhir" value="Hozzáad">
					</div>
				</form>
			<?php endif; ?>

			<?php
			if(isset($_SESSION["user"]) && $_SESSION["user"]["role"] == "admin") {

				// hír hozzáadás után hibaüzenet, vagy oldal újratöltés
				if (isset($siker) && $siker === TRUE) {
					$_SESSION["message"] = $uzenet;
					header("Location: hirek.php");
					exit;
				}
				else if (isset($siker) && $siker === FALSE) {
					echo '<p class="error-message">' . $uzenet . '</p>';
				}

				// újratöltés után sikerüzenet
				if (isset($_SESSION["message"])) {
					echo '<p class="success-message">' . $_SESSION["message"] . '</p><br>';
					unset($_SESSION["message"]);
				}
			}
			?>

			<!-- hírek listázása ha van, legfrissebb elől -->
			<?php if (file_exists("hirek/hirlista.json")): ?>
			<h1>Legfrissebb híreink</h1>
			<br>

			<?php
			$hirlista = file_get_contents("hirek/hirlista.json");
			$hirlista = json_decode($hirlista, true);
			$hirlista = array_reverse($hirlista);

			$logged_in_user = isset($_SESSION["user"]) ? $_SESSION["user"]["username"] : "";

			foreach ($hirlista["hirek"] as $hirnev) {
				$hirpath = "hirek/" . $hirnev . ".json";
				$hir = load_hir($hirpath);
				$comments = load_comments($hirpath);

				$liked_by_user = in_array($logged_in_user, $hir['likes']);
				$action = $liked_by_user ? 'unlike' : 'like';
				$no_of_likes = count($hir['likes']);

				echo '<div class="hirbox">';

					// cím
					echo '<div class="hir_header">';
						echo '<h1>' . $hir['cim'] . ' <a href="?' . $action . '=' . urlencode($hirnev) . '&user=' . urlencode($logged_in_user) . '"><span class="heart-icon ' . ($liked_by_user ? 'filled' : '') . '"></span></a>' . $no_of_likes . '</h1>';
						echo '<p><span class="date">' . $hir["datum"] . '</span></p>';
					echo '/div>';

					// a hír maga
					echo '<div class="hir_body">';

						echo '<p>' . nl2br($hir["hirtest"]) . '</p>';

						// média
						$media = $hir["media"];
						$extension = pathinfo($media, PATHINFO_EXTENSION);

						if ($extension == 'mp4') {
							echo '<video controls>';
								echo '<source src="' . 'hirek/media/' . $media . '" type="video/mp4">';
							echo '</video>';
						}
						else {
							echo '<img src="' . 'hirek/media/' . $media . '" alt="media_' . $hirnev . '">';
						}

					echo '/div>';
					
					// kommentek - először meglévők kiiratása majd új hozzáadása
					// kommenteknek külön táblázat


				echo '/div>';
			}
			?>

			<?php endif; ?>

		</div>
	</main>

	<?php include_once 'footer.html'; ?>
	
</body>

</html>