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
				<h1>Jegyek</h1>
				<br>
				
				<form method="POST"></form> <!-- php-vel majd nyomon követni/feldolgozni -->
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
									<input type="hidden" name="quantity_felnott" value="0">
									<button class="jegy-btn" name="minus_felnott">-</button>
									<p class="elvalaszto">/</p>
									<button class="jegy-btn" name="plus_felnott">+</button>
									<p class="mennyiseg">0</p>
								</div>
							</td>
						</tr>
						<tr>
							<td>Diák</td>
							<td>12-18 éves kor között vagy érvényes nappali diákigazolvány bemutatásával 25 éves korig</td>
							<td>1500&nbsp;Ft</td>
							<td>
								<div class="jegyhozzaad">
									<input type="hidden" name="quantity_diak" value="0">
									<button class="jegy-btn" name="minus_diak">-</button>
									<p class="elvalaszto">/</p>
									<button class="jegy-btn" name="plus_diak">+</button>
									<p class="mennyiseg">0</p>
								</div>
							</td>
						</tr>
						<tr>
							<td>Gyerek</td>
							<td>4-12 éves kor között</td>
							<td>1000&nbsp;Ft</td>
							<td>
								<div class="jegyhozzaad">
									<input type="hidden" name="quantity_gyerek" value="0">
									<button class="jegy-btn" name="minus_gyerek">-</button>
									<p class="elvalaszto">/</p>
									<button class="jegy-btn" name="plus_gyerek">+</button>
									<p class="mennyiseg">0</p>
								</div>
							</td>
						</tr>
						<tr>
							<td>Nyugdíjas</td>
							<td>60 éves kor felett</td>
							<td>1000&nbsp;Ft</td>
							<td>
								<div class="jegyhozzaad">
									<input type="hidden" name="quantity_nyugdijas" value="0">
									<button class="jegy-btn" name="minus_nyugdijas">-</button>
									<p class="elvalaszto">/</p>
									<button class="jegy-btn" name="plus_nyugdijas">+</button>
									<p class="mennyiseg">0</p>
								</div>
							</td>
						</tr>
						<tr>
							<td>Családi</td>
							<td>2 felnőtt és 2 gyerek részére</td>
							<td>5000&nbsp;Ft</td>
							<td>
								<div class="jegyhozzaad">
									<input type="hidden" name="quantity_csaladi" value="0">
									<button class="jegy-btn" name="minus_csaladi">-</button>
									<p class="elvalaszto">/</p>
									<button class="jegy-btn" name="plus_csaladi">+</button>
									<p class="mennyiseg">0</p>
								</div>
							</td>
						</tr>
					</table>

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
					<p>
						* kosár tartalma *
					</p>
					<p>
						* pontos kitöltés a kiválasztott jegyek függvényében php-val *
					</p>
				</div>
			</div>

		</div>
	</main>

	<?php include_once 'footer.html'; ?>
	
</body>

</html>