<!DOCTYPE html>
<html lang="hu">

<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
?>

<header>
    <div class="header-content-wrapper">

        <!-- a jelenlegi oldal meghatározása -->
        <?php
            $currentPage = basename($_SERVER["SCRIPT_FILENAME"], ".php");
        ?>
        
        <div class="header-above-nav">
            <div class="logo">
                <img src="img/zoo3.png" alt="logo">
            </div>
            <div>
                <h1>Isten hozta a Gotham állatkert honlapján!</h1>
            </div>
        </div>

        <nav>
            <div>
            <ul class="menu">
                <li><a href="index.php" class="<?php echo ($currentPage == "index") ? "current-page" : "other-page"; ?>">Főoldal</a></li>
                <li><a href="jegyek.php" class="<?php echo ($currentPage == "jegyek") ? "current-page" : "other-page"; ?>">Jegyek</a></li>
                <li><a href="hirek.php" class="<?php echo ($currentPage == "hirek") ? "current-page" : "other-page"; ?>">Hírek</a></li>
                <li><a href="allataink.php" class="<?php echo ($currentPage == "allataink") ? "current-page" : "other-page"; ?>">Állataink</a></li>
                <li><a href="kapcsolat.php" class="<?php echo ($currentPage == "kapcsolat") ? "current-page" : "other-page"; ?>">Kapcsolat</a></li>
            </ul>
            </div>
            <div>
                <ul class="user">
                    <?php if(!isset($_SESSION["user"])): ?>
                        <li><a href="regisztracio.php" class="<?php echo ($currentPage == "regisztracio") ? "current-page" : "other-page"; ?>">Regisztráció</a></li>
                        <li><a href="bejelentkezes.php" class="<?php echo ($currentPage == "bejelentkezes") ? "current-page" : "other-page"; ?>">Bejelentkezés</a></li>
                    <?php else: ?>
                        <li><a href="kijelentkezes.php" class="other-page">Kijelentkezés</a></li>
                        <li><a href="profil.php" class="<?php echo ($currentPage == "profil") ? "current-page" : "other-page"; ?>"><?php echo $_SESSION["user"]["username"], " profilja" ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </div>
</header>

</html>