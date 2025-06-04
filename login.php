<?php include('conf.php'); ?>
<?php
session_start();
global $yhendus;
//kontrollime kas väljad on täidetud
if (!empty($_POST['login']) && !empty($_POST['pass'])) {
    //eemaldame kasutaja sisestusest kahtlase pahna
    $login = htmlspecialchars(trim($_POST['login']));
    $pass = htmlspecialchars(trim($_POST['pass']));
    //SIIA UUS KONTROLL
    $sool = 'cool';
    $krypt = crypt($pass, $sool);
    //kontrollime kas andmebaasis on selline kasutaja ja parool
    $paring = $yhendus->prepare("SELECT id, login, parool, kasutaja_nimi, onadmin, istootaja FROM ab_kasutajad 
                                  WHERE login=? AND parool=?");
    $paring->bind_param('ss', $login, $krypt);
    $paring->bind_result($id, $login, $parool, $kasutaja_nimi, $onadmin, $istootaja);
    $paring->execute();

    if($paring->fetch() && $parool == $krypt) {
        // Salvestame sessiooni kõik vajaliku kasutaja kohta
        $_SESSION['kasutaja'] = $login;
        $_SESSION['kasutaja_nimi'] = $kasutaja_nimi;
        $_SESSION['kasutaja_id'] = $id;

        // Kontrollime, kas ta on admin või töötaja
        if($onadmin==1) {
            $_SESSION['admin'] = true;
        }
        if($istootaja==1) {
            $_SESSION['tootaja'] = true;
        }
        // Suuname peale sisselogimist avalehele
        header('location:index.php');
        $yhendus->close();
    }   else {
        // Vale parool või login – anname veateate
        $teade = "Kasutaja või parool on vale";
        $teadeTyyp = "sonum viga";
    }
}
// Kui eelnevalt ei olnud teateid (näiteks vale parool),
// ja URL-is on ?signup=success, siis kuvame registreerimise õnnestumise sõnumi
if (empty($teade) && isset($_GET["signup"]) && $_GET["signup"] == "success") {
    $teade = "Konto loodud edukalt!";
    $teadeTyyp = "sonum edu";
}
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Autoriseerimine</title>
    <link rel="icon" href="img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="css/loginStyle.css">
</head>
<body>
<div class="login-container">
    <div class="login-content">
        <h1>Tere tulemast
            <br>
            <span id="teine_rida">Kasside Varjupaika!</span>
        </h1>
        <!-- Kui on sõnum (viga või edu), siis näitame -->
        <?php if (!empty($teade)): ?>
            <div class="<?php echo $teadeTyyp; ?>"><?php echo $teade; ?></div>
        <?php else: ?>
            <p>Sisselogimine annab sulle ligipääsu meie kasside maailmale</p>
        <?php endif; ?>
        <form action="" method="post">
            <!-- SVG ikoonid -->
            <label for="login" class="login_label"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="#24594e" style="vertical-align: middle; margin-right: 5px;  margin-bottom: 3px;">
                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                </svg>
                Login:</label>
            <input type="text" name="login" placeholder="Kasutajanimi">
            <label for="login" class="login_label">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"  width="16" height="16" fill="#24594e" style="vertical-align: middle; margin-right: 5px;  margin-bottom: 3px;">
                    <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                </svg>
                Parool:</label>
            <input type="password" name="pass" placeholder="Parool">
            <br><br>
            <div class="btn-group">
                <input type="submit" value="Logi sisse" class="login-btn">
                <input type="button" value="Registreeru" class="signup-btn" onclick="location.href='signup.php'">
            </div>
            <br><br>
            <a href="index.php" class="tagasi-link">← Tagasi avalehele</a>
        </form>
    </div>
</div>
</body>
</html>
