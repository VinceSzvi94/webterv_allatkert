<!DOCTYPE html>
<html lang="hu">

<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
	include_once 'hirek/functions/hir_functions.php';
	include_once 'hirek/functions/Comment.php';
	include_once 'hirek/functions/addhir.php';
	include_once 'hirek/functions/likeol.php';
	include_once 'hirek/functions/kommentel.php';
	include_once 'user_functions.php';

	like_hir();
	unlike_hir();


	
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
			
			<?php if(isset($_SESSION["user"]) && $_SESSION["user"]["role"] == "admin"): ?>
				<h1>Hír hozzáadása</h1>
				<br>
				<form class="reg_urlap" action="addhir.php" method="POST" enctype="multipart/form-data">
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

				// újratöltés után sikerüzenet - mivel az űrlap külön van feldolgozva, űrlapfeldolgozás után mindig újratöltés lesz
				if (isset($_SESSION["message"]) && is_array($_SESSION["message"])) {
					foreach ($_SESSION["message"] as $uzenet) {
						echo $uzenet;
					}
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
			$all_users = load_users("userdata.json");

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
							if ($comment.isDeleted()) {
								echo '<table class="comment">';
									echo '<tr>';
										echo '<td class="c_main">';
											echo '<p> A komment törölve lett! </p>';
										echo '</td>';
									echo '</tr>';
								echo '</table>';
							}
							else {
								$liked_by_user = in_array($logged_in_user, $comment.getLikedBy());
								$action = $liked_by_user ? 'c_unlike' : 'c_like';
								$no_of_likes = $comment.countLikes();

								// profil kép
								$author = $comment.getAuthor();
								$author_key = array_search($author, $all_users);
								if ($author_key !== false) {
									$author_data = $all_users[$author_key];
								}
								else { $author_data = array("profilepic" => "img/profilepic.png"); }

								// komment megjelenítése
								echo '<table class="comment">';
									echo '<tr>';
										echo '<td class="c_aux">';
											echo '<p>' . $comment["author"] . ' - ' . $comment["date"] . '</p>';
										echo '</td>';
									echo '</tr>';
									echo '<tr>';
										echo '<td class="c_main">';
											echo '<p>' . $comment["content"] . '</p>';
										echo '</td>';
									echo '</tr>';
									echo '<tr>';
										echo '<td class="c_aux">';
											echo '<p>' .  . '</p>';
										echo '</td>';
									echo '</tr>';
								echo '</table>';

								// válaszok megjelenítése (minden válasz egy szintnek számít)
								$answers = $comment_obj->listAnswers();
							}
						}
					}

					echo '/div>';

				echo '/div>';
			}
			?>

			<?php endif; ?>

			<a href="https://www.flaticon.com/free-icons/user" title="user icons">User icons created by Phoenix Group - Flaticon</a>

		</div>
	</main>

	<?php include_once 'footer.html'; ?>
	
</body>

</html>