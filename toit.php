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

// Vaikimisi sortimise tulp
$sorttulp="tootja";
$otsityyp = "";
$otsitootja = "";

// Kui vormist saadeti uue toidu lisamise andmed ja väljad ei ole tühjad
if(isSet($_REQUEST["toiduLisamine"]) &&
    !empty(trim($_REQUEST["toidu_nimetus"])) &&
    !empty(trim($_REQUEST["tootja"])) &&
    !empty(trim($_REQUEST["tyyp"])) &&
    !empty(trim($_REQUEST["sailivus_paevad"]))
) {
    $nimetus = trim($_REQUEST["toidu_nimetus"]);
    $tyyp = trim($_REQUEST["tyyp"]);
    // Kontrollime, kas toit on juba olemas
    if (toitOlemas($nimetus, $tyyp)) {
        header("Location: toit.php?teade=toit_olemas");
        exit();
    } else {
        // Kui ei ole, lisame uue toidu
        lisaToit($_REQUEST["toidu_nimetus"], $_REQUEST["tootja"], $_REQUEST["tyyp"], $_REQUEST["sailivus_paevad"]);
        header("Location: toit.php?teade=lisatud");
        exit();
    }
}
// sortimine
if(isSet($_REQUEST["sort"])){$sorttulp=$_REQUEST["sort"];
}
//otsing
if (isset($_REQUEST["otsityyp"])) {
    $otsityyp=$_REQUEST["otsityyp"];
}
// otsing
if (isset($_REQUEST["otsitootja"])) {
    $otsitootja=$_REQUEST["otsitootja"];
}
// kustutamine
if (isset($_REQUEST["kustutusid"])) {
    kustutaToit($_REQUEST["kustutusid"]);
    header("Location: toit.php?teade=kustutatud");
}
// muutmine
if (isset($_REQUEST["muutmine"])) {
    muudaToit($_REQUEST["muudetudid"], $_REQUEST["toidu_nimetus"], $_REQUEST["tootja"], $_REQUEST["tyyp"], $_REQUEST["sailivus_paevad"]);
    header("Location: toit.php?teade=muudetud");
}
$toiduloend=kysiToiduAndmed($sorttulp, $otsityyp, $otsitootja);
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
        // Funktsioon tühjendab otsingu väljad
        function tyhjendaOtsing() {
            document.getElementById('otsityyp').value = '';
            document.getElementById('otsitootja').value = '';
            document.getElementById('otsinguvorm').submit();
        }
    </script>
</head>
<body>
<header>
    <h1>Toidu olemasolu</h1>
    <?php if (isAdmin()): ?>
        <span class="admin-badge">Admin</span>
    <?php endif; ?>
    <?php if (isTootaja()): ?>
        <span class="tootaja-badge">Töötaja</span>
    <?php endif; ?>
</header>
<!-- Lisame navigeerimismenüü -->
<?php include 'nav.php'; ?>
<div class="tabeli_container">
    <?php
    // Kui kasutaja on sisseloginud, siis näitame spetsiaalse klassiga sisu
    $className='aside_kylaline';
    if (isset($_SESSION['kasutaja'])) {
        $className='aside_login';
    }
    ?>
    <aside class="<?= $className ?>">
        <?php if (isAdmin() || isTootaja()) : ?>
            <!-- lisamine -->
            <form action="toit.php" id="lisamisvorm">
                <h2>Toidu lisamine</h2>
                <br>
                <label for="toidu_nimetus">Toidu nimetus:</label>
                <input type="text" name="toidu_nimetus" id="toidu_nimetus" required> <!-- "required" muudab selle välja kohustuslikuks -->
                <br>
                <label for="tootja">Tootja:</label>
                <select name="tootja" id="tootja">
                    <option value="Royal Canin">Royal Canin</option>
                    <option value="Purina">Purina</option>
                    <option value="Hill's">Hill's</option>
                    <option value="Mars">Mars</option>
                    <option value="Nestlé">Nestlé</option>
                    <option value="Applaws">Applaws</option>
                    <option value="Brit">Brit</option>
                </select>
                <br>
                <fieldset class="toidutyyp-valik">
                    <legend><strong>Tüüp:</strong></legend>
                    <input type="radio" name="tyyp" id="kuiv" value="kuiv" required>
                    <label for="kuiv">Kuiv</label>
                    <input type="radio" id="konserv" name="tyyp" value="konserv" required>
                    <label for="konserv">Konserv</label>
                </fieldset>
                <br>
                <label for="sailivus_paevad">Säilivuspäevad:</label>
                <input type="number" name="sailivus_paevad" id="sailivus_paevad" min="0" max="365" placeholder="päevad" required>
                <br><br>
                <input type="submit" name="toiduLisamine" value="Lisa toit" />
                <br><br>
            </form>
        <?php else: ?>
            <!-- kui pole admin, näita pilti -->
            <div class="cat_img_toit">
                <img src="img/cat_food.png" alt="Toituv kass">
            </div>
        <?php endif; ?>
    </aside>
    <?php if (isAdmin() || isTootaja()): ?>
        <main class="table">
    <?php else: ?>
        <!-- külaline näeb ainult kitsamat versiooni -->
        <main class="table" style="max-width: 900px; margin: 0 auto;">
    <?php endif; ?>
            <!-- vorm toidu otsimiseks -->
            <form action="toit.php" id="otsinguvorm">
                <div>
                    <label for="otsityyp">Toidu tüüp:</label>
                    <select name="otsityyp" id="otsityyp">
                        <option value="">vali...</option>
                        <!-- Kuvab valiku valituks, kui see vastab kasutaja sisestatud andmetele -->
                        <option value="kuiv" <?php if (isset($_REQUEST['otsityyp']) && $_REQUEST['otsityyp']=='kuiv')
                            echo 'selected'; ?>>kuiv</option>
                        <option value="konserv" <?php if (isset($_REQUEST['otsityyp']) && $_REQUEST['otsityyp']=='konserv')
                            echo 'selected'; ?>>konserv</option>
                    </select>
                </div>

                <div>
                    <label for="otsitootja">Tootja:</label>
                    <select name="otsitootja" id="otsitootja">
                        <option value="">vali...</option>
                        <!-- Kuvab valiku valituks, kui see vastab kasutaja sisestatud andmetele -->
                        <option value="Royal Canin" <?php if (isset($_REQUEST['otsitootja']) && $_REQUEST['otsitootja']=='Royal Canin')
                            echo 'selected'; ?>>Royal Canin</option>
                        <option value="Purina" <?php if (isset($_REQUEST['otsitootja']) && $_REQUEST['otsitootja']=='Purina')
                            echo 'selected'; ?>>Purina</option>
                        <option value="Hill's" <?php if (isset($_REQUEST['otsitootja']) && $_REQUEST['otsitootja']== "Hill's")
                            echo 'selected'; ?>>Hill's</option>
                        <option value="Mars" <?php if (isset($_REQUEST['otsitootja']) && $_REQUEST['otsitootja']=='Mars')
                            echo 'selected'; ?>>Mars</option>
                        <option value="Nestlé" <?php if (isset($_REQUEST['otsitootja']) && $_REQUEST['otsitootja']=='Nestlé')
                            echo 'selected'; ?>>Nestlé</option>
                        <option value="Applaws" <?php if (isset($_REQUEST['otsitootja']) && $_REQUEST['otsitootja']=='Applaws')
                            echo 'selected'; ?>>Applaws</option>
                        <option value="Brit" <?php if (isset($_REQUEST['otsitootja']) && $_REQUEST['otsitootja']=='Brit')
                            echo 'selected'; ?>>Brit</option>
                    </select>
                </div>

                <div class="otsingu-nupud">
                    <div>
                        <input type="submit" value="Otsi">
                        <button type="button" onclick="tyhjendaOtsing()">Tühjenda</button>
                    </div>
                </div>
                <br>
                <!-- tabel, kus kuvatakse toidud -->
                <table>
                    <tr>
                        <th><a href="toit.php?sort=toidu_nimetus">Toidu nimetus</a></th>
                        <th><a href="toit.php?sort=tootja">Tootja</a></th>
                        <th><a href="toit.php?sort=tyyp">Tüüp</a></th>
                        <th><a href="toit.php?sort=sailivus_paevad">Säilivuspäevad</a></th>
                        <?php if (isAdmin()): ?>
                            <th>Haldus</th>
                        <?php endif; ?>
                    </tr>
                    <?php foreach($toiduloend as $toit): ?>
                        <?php if(isSet($_REQUEST["muutmisid"]) && intval($_REQUEST["muutmisid"])==$toit->id): ?>
                            <tr>
                                <td><input type="text" name="toidu_nimetus" value="<?=$toit->toidu_nimetus ?>" /></td>
                                <td>
                                    <select name="tootja">
                                        <option value="<?=$toit->tootja ?>"><?=$toit->tootja ?></option>
                                        <?php
                                        $tootjad=["Royal Canin","Purina","Hill's","Mars","Nestlé","Applaws","Brit"];
                                        foreach ($tootjad as $tootja) {
                                            if ($tootja!==$toit->tootja) {
                                                echo "<option value=\"$tootja\">$tootja</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="tyyp" id="tyyp">
                                        <option value="<?=$toit->tyyp ?>"><?=$toit->tyyp ?></option>
                                        <?php
                                        $tyybid=["kuiv","konserv"];
                                        foreach ($tyybid as $tyyp) {
                                            if ($tyyp!==$toit->tyyp) {
                                                echo "<option value=\"$tyyp\">$tyyp</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td><input type="number" name="sailivus_paevad" value="<?=$toit->sailivus_paevad ?>" /></td>
                                <td>
                                    <input type="submit" name="muutmine" class="muuda_nupp" value="Muuda" />
                                    <input type="submit" name="katkestus" class="kustuta_nupp" value="Katkesta" />
                                    <input type="hidden" name="muudetudid" value="<?=$toit->id ?>" />
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td><?=$toit->toidu_nimetus ?></td>
                                <td><?=$toit->tootja ?></td>
                                <td><?=$toit->tyyp ?></td>
                                <td><?=$toit->sailivus_paevad ?></td>
                                <?php if (isAdmin()): ?>
                                    <td>
                                        <!-- admin saab muuta ja kustutada -->
                                        <a href="toit.php?kustutusid=<?=$toit->id ?>" class="kustuta_nupp"
                                           onclick="return confirm('Kas ikka soovid kustutada?')">Kustuta</a>
                                        <a href="toit.php?muutmisid=<?=$toit->id ?>" class="muuda_nupp">Muuda</a>
                                    </td>
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
<?php if (isset($_GET["teade"])): ?>
    <!-- Teadete kuvamine -->
    <script>
        window.onload = function () {
            <?php if ($_GET["teade"] === "muudetud"): ?>
            alert("Andmed on edukalt muudetud!");
            <?php elseif ($_GET["teade"] === "lisatud"): ?>
            alert("Uus kirje lisatud!");
            <?php elseif ($_GET["teade"] === "kustutatud"): ?>
            alert("Kirje on kustutatud!");
            <?php elseif ($_GET["teade"] === "toit_olemas"): ?>
            alert("Lisa erinev toit!");
            <?php endif; ?>
        };
    </script>
<?php endif; ?>
</body>
</html>
