<?php
	if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

	if (isset($_POST["kosarba"])) {
		$quantity_felnott = $_POST["quantity_felnott"];
		$quantity_diak = $_POST["quantity_diak"];
		$quantity_gyerek = $_POST["quantity_gyerek"];
		$quantity_nyugdijas = $_POST["quantity_nyugdijas"];
		$quantity_csaladi = $_POST["quantity_csaladi"];

		$osszesen = $quantity_felnott * 2000 + $quantity_diak * 1500 + $quantity_gyerek * 1000 + $quantity_nyugdijas * 1000 + $quantity_csaladi * 5000;

		if ($osszesen > 0) {
			$siker = TRUE;
			$_SESSION['cart'] = array(
				'felnott' => $quantity_felnott,
				'diak' => $quantity_diak,
				'gyerek' => $quantity_gyerek,
				'nyugdijas' => $quantity_nyugdijas,
				'csaladi' => $quantity_csaladi,
				'osszesen' => $osszesen
			);
		}
		else {
			$siker = FALSE;
			$uzenet = "Nem választott ki jegyet!";
		}
	}	
?>

<!DOCTYPE html>
<html lang="hu">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Kormos Sándor, Szigetvári Vince">
	<title>Jegyek | Gotham állatkert</title>
	<link rel="stylesheet" href="css/style.css">
	<link rel="icon" href="img/zoo3.png">
	<meta name="keywords" content="Gotham, allatkert, zoo, Hungary">
	<meta name="description" content="Gotham állatkert honlapja.">
</head>

<body class="egyeboldal">

	<?php include_once 'header.php'; ?>

	<main>
		<div class="main_multicolumn-content-wrapper">
			<div class="left-column">

				<?php
					if (isset($_SESSION["message"]) && isset($_SESSION["message_type"])) {
						if ($_SESSION["message_type"] == "neutral"){
							echo '<p class="neutral-message">' . $_SESSION["message"] . '</p><br>';
							unset($_SESSION["message"]);
							unset($_SESSION["message_type"]);
						}
						else {
							echo '<p class="success-message">' . $_SESSION["message"] . '</p><br>';
							unset($_SESSION["message"]);
							unset($_SESSION["message_type"]);
						}
					}
				?>

				<h1>Jegyek</h1>
				<br>
				
				<form class="jegy_urlap" id="jegyurlap" action="jegyek.php" method="POST">

					<table>
						<tr>
							<th>Jegytípus</th>
							<th>Leírás</th>
							<th>Ár</th>
							<th>Kosárhoz</th>
						</tr>
						<tr>
							<td>Felnőtt</td>
							<td>18-60 éves kor között</td>
							<td>2000&nbsp;Ft</td>
							<td>
								<div class="jegyhozzaad">
									<input type="number" name="quantity_felnott" placeholder="0" value="0" min="0">
								</div>
							</td>
						</tr>
						<tr>
							<td>Diák</td>
							<td>12-18 éves kor között vagy érvényes nappali diákigazolvány bemutatásával 25 éves korig</td>
							<td>1500&nbsp;Ft</td>
							<td>
								<div class="jegyhozzaad">
									<input type="number" name="quantity_diak" placeholder="0" value="0" min="0">
								</div>
							</td>
						</tr>
						<tr>
							<td>Gyerek</td>
							<td>4-12 éves kor között</td>
							<td>1000&nbsp;Ft</td>
							<td>
								<div class="jegyhozzaad">
									<input type="number" name="quantity_gyerek" placeholder="0" value="0" min="0">
								</div>
							</td>
						</tr>
						<tr>
							<td>Nyugdíjas</td>
							<td>60 éves kor felett</td>
							<td>1000&nbsp;Ft</td>
							<td>
								<div class="jegyhozzaad">
									<input type="number" name="quantity_nyugdijas" placeholder="0" value="0" min="0">
								</div>
							</td>
						</tr>
						<tr>
							<td>Családi</td>
							<td>2 felnőtt és 2 gyerek részére</td>
							<td>5000&nbsp;Ft</td>
							<td>
								<div class="jegyhozzaad">
									<input type="number" name="quantity_csaladi" placeholder="0" value="0">
								</div>
							</td>
						</tr>
					</table>
					<div class="formgomb">
						<input type="reset" value="Töröl">
						<input type="submit" class="submitclass" name="kosarba" value="Kosárba">
					</div>

				</form>
				
				<?php
				if (isset($siker) && $siker === FALSE) {  // ha 0 jegyet akar valaki kosárba tenni
					echo '<p class="error-message">' . $uzenet . '</p>';
					}
				?>

				<div class="transparent_bg_img">
					<img class="balrol" src="img/Lion%20Animal%20-%20640x462.png" alt="oroszlanOszi" title="oroszlán Oszi">
				</div>
			</div>

			<div class="right-column">
				<div class="transparent_bg_img">
					<img class="jobbrol" src="img/Arctic%20Snow%20Fox%20-%20640x394.png" alt="sarkiRudi" title="sarki Rudi">
				</div>

				<div class="kosar">
					<h1>Kosár</h1>
					<br>
					<?php
					if (isset($siker) && $siker === TRUE) {  // ha jegy van adva a kosárhoz
						if ($quantity_felnott > 0) {
							echo "<p>Felnőtt:\t" . $quantity_felnott . " db:\t" . $quantity_felnott*2000 . " Ft</p>";
						}
						if ($quantity_diak > 0) {
							echo "<p>Diák:\t" . $quantity_diak . " db:\t" . $quantity_diak*1500 . " Ft</p>";
						}
						if ($quantity_gyerek > 0) {
							echo "<p>Gyerek:\t" . $quantity_gyerek . " db:\t" . $quantity_gyerek*1000 . " Ft</p>";
						}
						if ($quantity_nyugdijas > 0) {
							echo "<p>Nyugdíjas:\t" . $quantity_nyugdijas . " db:\t" . $quantity_nyugdijas*1000 . " Ft</p>";
						}
						if ($quantity_csaladi > 0) {
							echo "<p>Családi:\t" . $quantity_csaladi . " db:\t" . $quantity_csaladi*5000 . " Ft</p>";
						}
						echo "<p>-------------------------------------------</p>";
						echo "<p>Összesen:\t" . $osszesen . " Ft</p>";
					}
					else {
						echo "<p>Kérem adjon jegyet a kosárhoz.</p>";
					}
					?>

					<div class="formgomb">
						<form action="kosartorol.php" method="POST">
							<input type="submit" class="kosarurit" name="kosarurit" value="Kosár ürítése">
						</form>
						<form action="fizetes.php" method="POST">
							<input type="submit" class="fizetes" name="fizet" value="Fizetés">
						</form>
					</div>
					
				</div>
			</div>

		</div>
	</main>

	<?php include_once 'footer.html'; ?>
	
</body>

</html>