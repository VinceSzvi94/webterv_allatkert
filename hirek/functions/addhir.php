<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
	include_once 'hir_functions.php';

    $hibak = [];

	// hír hozzáadása
	if (isset($_POST["ujhir"])) {
		if (!isset($_POST["cim"]) || trim($_POST["cim"]) === "")
			$hibak[] = "Adjon meg címet!";
		if (!isset($_POST["hirnev"]) || trim($_POST["hirnev"]) === "")
			$hibak[] = "Adjon meg azonosítót!";
		if (!isset($_POST["hirtest"]) || trim($_POST["hirtest"]) === "")
			$hibak[] = "Írja meg a hírt!";
		if (!preg_match('/^[a-zA-Z0-9_]+$/', $hirnev)) {
			$hibak[] = "Az azonosító csak betűket, számokat és alulvonást tartalmazhat!";
		}

		$cel = NULL;

		// kép feltöltése
		if (isset($_FILES["media"])) {
			$engedelyezett_kiterjesztesek = ["jpg", "jpeg", "png", "mp4"];
			$kiterjesztes = strtolower(pathinfo($_FILES["media"]["name"], PATHINFO_EXTENSION));

			if (in_array($kiterjesztes, $engedelyezett_kiterjesztesek)) {

				if ($_FILES["media"]["error"] === 0) {

					if ($_FILES["media"]["size"] <= 31457280) {

						$cel = "hirek/media/" . $_FILES["media"]["name"];
						
						if (file_exists($cel)) {
							echo '<p class="neutral-message"> A régebbi fájl felülírásra kerül! </p>';
						}
						if (move_uploaded_file($_FILES["media"]["tmp_name"], $cel)) {
							echo '<p class="success-message"> Sikeres fájlfeltöltés! </p>';
						}
						else { $hibak[] = "A fájl átmozgatása nem sikerült!"; }
					}
					else { $hibak[] = "A fájl mérete túl nagy!"; }
				}
				else { $hibak[] = "A fájlfeltöltés nem sikerült!"; }
			}
			else { $hibak[] = "A fájl kiterjesztése nem megfelelő!"; }
		}

		if (count($hibak) === 0) {
			$cim = $_POST["cim"];
			$hirnev = $_POST["hirnev"];
			$hirtest = $_POST["hirtest"];
			$siker = TRUE;
			$uzenet = "A hír sikeresen hozzáadva!";
			$hir = array(
				"cim" => $cim,
				"hirnev" => $hirnev,
				"datum" => date("Y-m-d H:i:s"),
				"hirtest" => $hirtest,
				"media" => $cel,
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
		else { $siker = FALSE; }
	}

    if (isset($siker) && $siker === TRUE) {
        $_SESSION["message"] = ['<p class="success-message">' . $uzenet . '</p>'];
        header("Location: hirek.php");
        exit;
    }
    else if (isset($siker) && $siker === FALSE) {
        foreach ($hibak as $hiba) {
            $hiba = '<p class="error-message">' . $hiba . '</p>';
        }
        unset($hiba);
        $hibak[] = '<p class="error-message"> Az adatok rögzítésére nem került sor! </p>';
        $_SESSION["message"] = $hibak;
        header("Location: hirek.php");
        exit;
    }
    else {
        $_SESSION["message"] = ['<p class="error-message"> Ismeretlen hiba lépett fel az adatok feldolgozása közben, adatok rögzítésére nem került sor! </p>'];
        header("Location: hirek.php");
        exit;
    }

?>