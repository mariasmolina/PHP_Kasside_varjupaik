<?php
require("conf.php");
session_start();

// Kui kasutaja on admin, siis määrame admin õigused
if (isset($_SESSION['kasutaja']) && $_SESSION['kasutaja'] === 'admin') {
    $_SESSION['admin'] = true;
} else {
    $_SESSION['admin'] = false;
}
require("abifunktsioonid.php");

// vaikimisi sorteerime kuupäeva järgi
$sorttulp="kuupaev";
$otsiloom = "";
$otsikuupaev = "";

// kui vajutati nuppu "Lisa toitmine" ja kõik väljad on täidetud
if(isSet($_REQUEST["toitmiselisamine"]) &&
    !empty($_REQUEST["loom_id"]) &&
    !empty(trim($_REQUEST["kuupaev"])) &&
    !empty($_REQUEST["toit_id"]) &&
    !empty(trim($_REQUEST["kogus"])) &&
    isset($_SESSION["kasutaja_id"])
) {
    // Kontroll: kui sisestatud kuupaev on tulevikus
    $kuupaev=$_REQUEST["kuupaev"];
    $praegune=date("Y-m-d\TH:i");
    if ($kuupaev>$praegune) {
        header("Location: toitmisAjalugu.php?teade=vale_kuupaev");
        exit();
    }
    // lisame uue toitmise andmebaasi
    lisaToitmine($_REQUEST["kuupaev"], $_REQUEST["kogus"], $_REQUEST["toit_id"], $_REQUEST["loom_id"], $_SESSION["kasutaja_id"]);
    header("Location: toitmisAjalugu.php?teade=lisatud");
    exit();
}

// sortimine
if(isSet($_REQUEST["sort"])){$sorttulp=$_REQUEST["sort"];
}

//looma otsing
if (isset($_REQUEST["otsiloom"])) {
    $otsiloom=$_REQUEST["otsiloom"];
}

// kuupäeva otsing
if (isset($_REQUEST["otsikuupaev"])) {
    $otsikuupaev=$_REQUEST["otsikuupaev"];
}
// kustutamine
if (isset($_REQUEST["kustutusid"])) {
    kustutaLoomatoitmine($_REQUEST["kustutusid"]);
    header("Location: toitmisAjalugu.php?teade=kustutatud");
}
// muutmine
if (isset($_REQUEST["muutmine"])) {
    // Kontrollime, et muudetud kuupäev ei oleks tulevikus
    $kuupaev=$_REQUEST["kuupaev"];
    $praegune=date("Y-m-d\TH:i");
    if ($kuupaev>$praegune) {
        header("Location: toitmisAjalugu.php?teade=vale_kuupaev");
        exit();
    }
    muudaLoomatoitmine($_REQUEST["muudetudid"], $_REQUEST["loom_id"], $_REQUEST["kuupaev"], $_REQUEST["toit_id"], $_REQUEST["kogus"], $_REQUEST["tootaja_id"]);
    header("Location: toitmisAjalugu.php?teade=muudetud");
}
// küsime kõik toitmise kirjed andmebaasist, vajadusel otsinguga
$toitmisajalugu=kysiAndmed($sorttulp, $otsiloom, $otsikuupaev);
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <title>Kasside varjupaik</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link rel="icon" href="img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script>
        function tyhjendaOtsing() {
            document.getElementById('otsiloom').value = '';
            document.getElementById('otsikuupaev').value = '';
            document.getElementById('otsinguvorm').submit();
        }
    </script>
</head>
<body>
<?php
if (isset($_SESSION['kasutaja'])) {
?>
    <header>
        <h1>Toitmisajalugu</h1>
        <?php if (isAdmin()): ?>
            <span class="admin-badge">Admin</span>
        <?php endif; ?>
        <?php if (isTootaja()): ?>
            <span class="tootaja-badge">Töötaja</span>
        <?php endif; ?>
    </header>
    <?php include 'nav.php'; ?>
    <div class="tabeli_container">
        <?php if (isAdmin() || isTootaja()): ?>
            <aside class="aside_login">
                <!-- lisamine -->
                <form action="toitmisAjalugu.php" method="post" id="lisamisvorm">
                    <h2>Toitmise lisamine</h2>
                    <br>
                    <label for="">Looma nimi:</label>
                    <?php
                    echo looRippMenyy("SELECT id, looma_nimi FROM loom",
                        "loom_id");
                    ?>
                    <br>
                    <label for="kuupaev">Kuupäev/kellaaeg:</label>
                    <input type="datetime-local" name="kuupaev" id="kuupaev" required> <!-- "required" muudab selle välja kohustuslikuks -->
                    <br>
                    <label for="">Toit:</label>
                    <?php
                    echo looRippMenyy("SELECT id, toidu_nimetus FROM toit",
                        "toit_id");
                    ?>
                    <br>
                    <label for="">Kogus:</label>
                    <input type="number" name="kogus" id="kogus" min="1" max="200" placeholder="grammid" required>
                    <br><br>
                    <input type="submit" name="toitmiselisamine" value="Lisa toitmine" />
                    <br><br>
                </form>
            </aside>
        <?php endif; ?>
        <main class="table">
            <!-- otsing -->
            <form action="toitmisAjalugu.php" id="otsinguvorm">
                <div>
                    <label for="otsiloom">Looma nimi:</label>
                    <select name="otsiloom" id="otsiloom">
                        <option value="">vali...</option>
                        <?php
                        global $yhendus;
                        $kask = $yhendus->prepare("SELECT id, looma_nimi FROM loom");
                        $kask->bind_result($id, $sisu);
                        $kask->execute();
                        while ($kask->fetch()) {
                            echo "<option value='$id'>$sisu</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label for="otsikuupaev">Kuupäev:</label>
                    <input type="date" name="otsikuupaev" id="otsikuupaev" value="<?=htmlspecialchars($otsikuupaev)?>">
                </div>

                <div class="otsingu-nupud">
                    <div>
                        <input type="submit" value="Otsi">
                        <button type="button" onclick="tyhjendaOtsing()">Tühjenda</button>
                    </div>
                </div>
                <br>
                <!-- Tabel toitmise andmetega -->
                <table>
                    <tr>
                        <th><a href="toitmisAjalugu.php?sort=looma_nimi">Loom</a></th>
                        <th><a href="toitmisAjalugu.php?sort=kuupaev">Kuupäev/kellaaeg</a></th>
                        <th><a href="toitmisAjalugu.php?sort=toidu_nimetus">Toit</a></th>
                        <th><a href="toitmisAjalugu.php?sort=kogus">Kogus</a></th>
                        <th><a href="toitmisAjalugu.php?sort=kasutaja_nimi">Töötaja</a></th>
                        <?php if (isAdmin() || isTootaja()) : ?>
                            <th>Haldus</th>
                        <?php endif; ?>
                    </tr>
                    <!-- Andmete kuvamine tabelina -->
                    <?php foreach($toitmisajalugu as $loom): ?>
                        <?php if(isSet($_REQUEST["muutmisid"]) && intval($_REQUEST["muutmisid"])==$loom->id): ?>
                            <!-- Kui kasutaja soovib kirjet muuta -->
                            <tr>
                                <td>
                                    <?php
                                    echo looRippMenyy("SELECT id, looma_nimi FROM loom", "loom_id", $loom->loom_id);
                                    ?>
                                </td>
                                <td><input type="datetime-local" name="kuupaev" value="<?=date('Y-m-d\TH:i', strtotime($loom->kuupaev)) ?>" /></td>
                                <td>
                                    <?php
                                    echo looRippMenyy("SELECT id, toidu_nimetus FROM toit", "toit_id", $loom->toit_id);
                                    ?>
                                </td>
                                <td><input type="number" name="kogus" min="1" max="200" value="<?=$loom->kogus ?>" /></td>
                                <td>
                                    <?php
                                    echo looRippMenyy("SELECT id, kasutaja_nimi FROM ab_kasutajad", "tootaja_id", $loom->tootaja_id);
                                    ?>
                                </td>
                                <td>
                                    <input type="submit" name="muutmine" class="muuda_nupp" value="Muuda" />
                                    <input type="submit" name="katkestus" class="kustuta_nupp" value="Katkesta" />
                                    <!-- Peidame vajalikud väärtused -->
                                    <input type="hidden" name="muudetudid" value="<?=$loom->id ?>" />
                                    <input type="hidden" name="sort" value="<?=htmlspecialchars($sorttulp) ?>">
                                    <input type="hidden" name="otsiloom" value="<?=htmlspecialchars($otsiloom) ?>">
                                    <input type="hidden" name="otsikuupaev" value="<?=htmlspecialchars($otsikuupaev) ?>">
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td><?=$loom->looma_nimi ?></td>
                                <td><?=$loom->kuupaev ?></td>
                                <td><?=$loom->toidu_nimetus ?></td>
                                <td><?=$loom->kogus ?></td>
                                <td><?=$loom->kasutaja_nimi ?></td>
                                <?php if (isAdmin()): ?>
                                    <td>
                                        <!-- Admin saab kustutada ja muuta -->
                                        <a href="toitmisAjalugu.php?kustutusid=<?= $loom->id ?>" class="kustuta_nupp"
                                           onclick="return confirm('Kas ikka soovid kustutada?')">Kustuta</a>
                                        <a href="toitmisAjalugu.php?muutmisid=<?= $loom->id ?>" class="muuda_nupp">Muuda</a>
                                    </td>
                                <?php elseif (isTootaja() && $_SESSION['kasutaja_id'] == $loom->tootaja_id): ?>
                                    <!-- Töötaja saab muuta/kustutada ainult oma kirjeid -->
                                    <td>
                                        <a href="toitmisAjalugu.php?kustutusid=<?= $loom->id ?>" class="kustuta_nupp"
                                           onclick="return confirm('Kas ikka soovid kustutada?')">Kustuta</a>
                                        <a href="toitmisAjalugu.php?muutmisid=<?= $loom->id ?>" class="muuda_nupp">Muuda</a>
                                    </td>
                                <?php else: ?>
                                    <td></td> <!-- Kui pole õigusi, siis tühi lahter -->
                                <?php endif; ?>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </table>
            </form>
        </main>
    </div>
    <!-- Lisame lehe lõpu jaluse -->
    <?php include 'footer.php'; ?>
<?php
}
?>
<?php if (isset($_GET["teade"])): ?>
    <!-- Kui toiming tehtud, näitame teadet -->
    <script>
        window.onload = function () {
            <?php if ($_GET["teade"] === "muudetud"): ?>
            alert("Andmed on edukalt muudetud!");
            <?php elseif ($_GET["teade"] === "lisatud"): ?>
            alert("Uus kirje lisatud!");
            <?php elseif ($_GET["teade"] === "kustutatud"): ?>
            alert("Kirje on kustutatud!");
            <?php elseif ($_GET["teade"] === "vale_kuupaev"): ?>
            alert("Viga! Kuupäev ei tohi olla tulevikus!");
            <?php endif; ?>
        };
    </script>
<?php endif; ?>
</body>
</html>
