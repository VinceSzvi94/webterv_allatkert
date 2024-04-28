<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
	include_once 'hirek/functions/hir_functions.php';
	include_once 'hirek/functions/Comment.php';
	include_once 'hirek/functions/likeol.php';
	include_once 'hirek/functions/show_comment.php';
	include_once 'user_functions.php';

	$admin = isset($_SESSION["user"]) && $_SESSION["user"]["role"] == "admin";
	$banned = isset($_SESSION["user"]) && $_SESSION["user"]["banned"] == true;

	// like_hir();
	// unlike_hir();
	// like_comment();
	// unlike_comment();
	// delete_comment();

	$vhibak = [];
	$vsiker = NULL;

    if (isset($_POST["valasz"])) {
		$vhibak = [$_POST["answer_to"]];

		if (!isset($_POST["content"]) || trim($_POST["content"]) === "") {
            $vhibak[] = "Írja be a kommentet!";
        }

        $path = "hirek/" . $_POST["hirnev"] . ".json";

        if (count($vhibak) === 1) {
            $vsiker = TRUE;

            // teljes hír-komment tömb betöltése, mivel ebben az esetben új komment hozzáadása helyett a már meglévő kommenthez kell gyereket adni
            $hir_arr = json_decode(file_get_contents($path), true);
            $comments = load_comments($path);
            $parent_id = $_POST["answer_to"];

            foreach ($comments as &$comment) {
				$comment = unserialize($comment);
                if ($comment->getId() === $parent_id) {
                    $comment->addAnswer($_POST["user"], $_POST["content"]);
					$comment = serialize($comment);
                    break;
                }
                else if ($comment->isChild($parent_id)) {
                    $comment->applyMethodOnChild($parent_id, "addAnswer", [$_POST["user"], $_POST["content"]]);
					$comment = serialize($comment);
                    break;
                }
                else { $comment = serialize($comment); continue; }
            }
            unset($comment);
            $hir_arr = array_merge([$hir_arr[0]], $comments);
            file_put_contents($path, json_encode($hir_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
        else {
            $vsiker = FALSE;
        }
    }

    if (isset($vsiker) && $vsiker === TRUE) {
        header("Location: hirek.php");
        exit;
    }
    else if (isset($vsiker) && $vsiker === FALSE) {
        foreach ($vhibak as &$hiba) {
            $hiba = '<p class="error-message">' . $hiba . '</p>';
        }
        unset($hiba);
        $vhibak[] = '<p class="error-message"> A komment nem lett elküldve! </p>';
        // $_SESSION["message"] = $hibak;
        // $_SESSION["comment_message"] = true;
        // header("Location: hirek/functions/show_comment.php");
        // exit;
    }
    // else {
    //     $_SESSION["message"] = ['<p class="error-message"> Ismeretlen hiba lépett fel, a komment nem lett elküldve! </p>'];
    //     $_SESSION["comment_message"] = true;
    //     header("Location: hirek/functions/show_comment.php");
    //     exit;
    // }

	$hibak = [];
	$siker = NULL;

	// hír hozzáadása
	if (isset($_POST["ujhir"])) {
		if (!isset($_POST["cim"]) || trim($_POST["cim"]) === "") {
            $hibak[] = "Adjon meg címet!";
        }
		if (!isset($_POST["hirnev"]) || trim($_POST["hirnev"]) === "") {
            $hibak[] = "Adjon meg azonosítót!";
        }
		if (!isset($_POST["hirtest"]) || trim($_POST["hirtest"]) === "") {
            $hibak[] = "Írja meg a hírt!";
        }
		if (!preg_match('/^[a-zA-Z0-9_]+$/', $_POST["hirnev"])) {
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
        // $_SESSION["message"] = ['<p class="success-message">' . $uzenet . '</p>'];
        header("Location: hirek.php");
        exit;
    }
    else if (isset($siker) && $siker === FALSE) {
        foreach ($hibak as &$hiba) {
            $hiba = '<p class="error-message">' . $hiba . '</p>';
        }
        unset($hiba);
        $hibak[] = '<p class="error-message"> Az adatok rögzítésére nem került sor! </p>';
        // $_SESSION["message"] = $hibak;
        // header("Location: hirek.php");
        // exit;
    }
    // else {
    //     $_SESSION["message"] = ['<p class="error-message"> Ismeretlen hiba lépett fel az adatok feldolgozása közben, adatok rögzítésére nem került sor! </p>'];
    //     header("Location: hirek.php");
    //     exit;
    // }

	$khibak = [];
	$ksiker = NULL;

	if (isset($_POST["komment"])) {
		if (!isset($_POST["content"]) || trim($_POST["content"]) === "") {
            $khibak[] = "Írja be a kommentet!";
        }

        $path = "hirek/" . $_POST["hirnev"] . ".json";

        if (count($khibak) === 0) {
            $ksiker = TRUE;
            $uj_komment = new Comment($_POST["user"], $_POST["content"], null);
            save_comments($path, $uj_komment);
        }
        else {
            $ksiker = FALSE;
        }
    }

    if (isset($ksiker) && $ksiker === TRUE) {
        header("Location: hirek.php");
        exit;
    }
    else if (isset($ksiker) && $ksiker === FALSE) {
        foreach ($khibak as &$hiba) {
            $hiba = '<p class="error-message">' . $hiba . '</p>';
        }
        unset($hiba);
        $khibak[] = '<p class="error-message"> A komment nem lett elküldve! </p>';
        // $_SESSION["message"] = $hibak;
        // $_SESSION["comment_message"] = false;
        // header("Location: hirek.php");
        // exit;
    }
    // else {
    //     $_SESSION["message"] = ['<p class="error-message"> Ismeretlen hiba lépett fel, a komment nem lett elküldve! </p>'];
    //     $_SESSION["comment_message"] = false;
    //     header("Location: hirek.php");
    //     exit;
    // }


?>

<!DOCTYPE html>
<html lang="hu">

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
			
			<?php if($admin): ?>
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
			if ($admin) {

				if (count($hibak) > 0) {
					foreach ($hibak as $hiba) {
						echo $hiba;
					}
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
			$all_users = load_users("userdata.json");

			foreach ($hirlista["hirek"] as $hirnev) {
				$hirpath = "hirek/" . $hirnev . ".json";
				$hir = load_hir($hirpath);
				$comments = array_reverse(load_comments($hirpath));

				$liked_by_user = in_array($logged_in_user, $hir['likes']);
				$action = $liked_by_user ? 'unlike' : 'like';
				$no_of_likes = count($hir['likes']);

				echo '<div class="hirbox">';

					// cím
					echo '<div class="hir_header">';
						echo '<h1>' . $hir['cim'] . ' | <a href="?' . $action . '=' . urlencode($hirnev) . '&user=' . urlencode($logged_in_user) . '"><span class="heart-icon ' . ($liked_by_user ? 'filled' : '') . '"></span></a>' . $no_of_likes . '</h1>';
						echo '<p><span class="date">' . $hir["datum"] . '</span></p>';
					echo '/div>';

					// a hír maga
					echo '<div class="hir_body">';

						echo '<p>' . nl2br($hir["hirtest"]) . '</p>';

						// média
						$media = $hir["media"];
						if ($media !== "" && $media !== null) {
							$extension = pathinfo($media, PATHINFO_EXTENSION);

							if ($extension == 'mp4') {
								echo '<video controls>';
									echo '<source src="' . $media . '" type="video/mp4">';
								echo '</video>';
							}
							else { echo '<img src="' . $media . '" alt="media_' . $hirnev . '">'; }
						}

					echo '/div>';
					
					// kommentek - először meglévők kiiratása majd új hozzáadása
					echo '<div class="hir_comments">';
					echo '<h4>Kommentek</h4>';
					if (!isset($_SESSION["user"])) {
						echo '<p> A kommenteléshez jelentkezzen be! </p>';
						echo '<form action="bejelentkezes.php">';
						echo '<input type="submit" class="submitclass" value="Bejelentkezéshez kattinson ide">';
						echo '</form>';
					}
					else {
						foreach ($comments as $comment) {
							$comment = unserialize($comment);
							// profil kép
							$author = $comment->getAuthor();
							$author_key = array_search($author, $all_users);
							if ($author_key !== false) {
								$author_data = $all_users[$author_key];
								if ($author_data["profpic"] !== "" && $author_data["profpic"] !== null) {
									$pic_path = "img/profpics/" . $author_data["profpic"];
								}
								else { $pic_path = "img/profpics/account.png"; }
							}
							else { $pic_path = "img/profpics/account.png"; }

							// potenciális hibaüzenet:
							if (count($vhibak) > 1 && $vhibak[0] == $comment->getId()) {
								$vhibak = array_slice($vhibak, 1);
							}
							else { $vhibak = []; }

							// komment megjelenítése
							show_comment('comment', $pic_path, $comment, $logged_in_user, $admin, $banned, $hirnev, $vhibak);
							
							// válaszok megjelenítése (minden válasz egy szintnek számít)
							$answers = $comment->listAnswers();

							foreach ($answers as $answer) {
								// profil kép
								$author = $answer->getAuthor();
								$author_key = array_search($author, $all_users);
								if ($author_key !== false) {
									$author_data = $all_users[$author_key];
									if ($author_data["profpic"] !== "" && $author_data["profpic"] !== null) {
										$pic_path = "img/profpics/" . $author_data["profpic"];
									}
									else { $pic_path = "img/profpics/account.png"; }
								}
								else { $pic_path = "img/profpics/account.png"; }

								// potenciális hibaüzenet:
								if (count($vhibak) > 1 && $vhibak[0] == $answer.getId()) {
									$vhibak = array_slice($vhibak, 1);
								}
								else { $vhibak = []; }

								show_comment('answer', $pic_path, $answer, $logged_in_user, $admin, $banned, $hirnev, $vhibak);
							}
						}

						// új komment űrlap 
						echo '<table class="comment">';
						echo '<tr>';
						echo '<td class="c_main">';

						if ($banned) {
							echo '<p> Ön ki lett tiltva a kommentelés lehetőségétől! </p>';
						}
						else {
							echo '<form class="kom_urlap" action="hirek.php" method="POST">';
								
								echo '<input type="hidden" name="hirnev" value="' . $hirnev . '">';
								echo '<input type="hidden" name="user" value="' . $logged_in_user . '">';

								echo '<div style="width: 100%; display: flex; justify-content: space-between; flex-direction: row;">';

									echo '<div style="display: flex; align-items: center; flex-direction: column; width: 20%;">';
										echo '<input type="reset" value="Elvet">';
										echo '<input type="submit" class="submitclass" name="komment" value="Komment">';
									echo '</div>';

									echo '<div style="display: flex; align-items: center; flex-direction: column; width: 75%;">';
										echo '<textarea name="content" placeholder="Komment írása..."></textarea>';
									echo '</div>';

								echo '</div>';

							echo '</form>';
						}

						echo '</td>';
						echo '</tr>';
						echo '</table>';

						if (count($khibak) > 0) {
							foreach ($khibak as $hiba) {
								echo $hiba;
							}
						}

					}

					echo '/div>';

				echo '/div>';
			}
			?>

			<?php endif; ?>

			<p>Alap profilkép: <a href="https://www.flaticon.com/free-icons/user" title="user icons">User icons created by Phoenix Group - Flaticon</a></p>

		</div>
	</main>

	<?php include_once 'footer.html'; ?>
	
</body>

</html>