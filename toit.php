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
$sorttulp="tootja";
$otsityyp = "";
$otsitootja = "";

if(isSet($_REQUEST["toiduLisamine"]) &&
    !empty(trim($_REQUEST["toidu_nimetus"])) &&
    !empty(trim($_REQUEST["tootja"])) &&
    !empty(trim($_REQUEST["tyyp"])) &&
    !empty(trim($_REQUEST["sailivus_paevad"]))
) {
    $nimetus = trim($_REQUEST["toidu_nimetus"]);
    $tyyp = trim($_REQUEST["tyyp"]);
    if (toitOlemas($nimetus, $tyyp)) {
        header("Location: toit.php?teade=toit_olemas");
        exit();
    } else {
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
// kuupäeva otsing
if (isset($_REQUEST["otsitootja"])) {
    $otsitootja=$_REQUEST["otsitootja"];
}
if (isset($_REQUEST["kustutusid"])) {
    kustutaToit($_REQUEST["kustutusid"]);
    header("Location: toit.php?teade=kustutatud");
}
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
<?php include 'nav.php'; ?>
<div class="tabeli_container">
    <aside class="<?= isset($_SESSION['kasutaja']) ? 'aside_login' : 'aside_kylaline' ?>">
        <?php if (isAdmin() || isTootaja()) : ?>
            <!-- lisamine -->
            <form action="toit.php" id="lisamisvorm">
                <h2>Toidu lisamine</h2>
                <br>
                <label for="toidu_nimetus">Toidu nimetus:</label>
                <input type="text" name="toidu_nimetus" id="toidu_nimetus">
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
                    <input type="radio" name="tyyp" id="kuiv" value="kuiv">
                    <label for="kuiv">Kuiv</label>
                    <input type="radio" id="konserv" name="tyyp" value="konserv">
                    <label for="konserv">Konserv</label>
                </fieldset>
                <br>
                <label for="sailivus_paevad">Säilivus päevad:</label>
                <input type="number" name="sailivus_paevad" id="sailivus_paevad" min="0" max="365" placeholder="päevad">
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
        <main class="table" style="max-width: 900px; margin: 0 auto;">
            <?php endif; ?>
            <!-- otsing -->
            <form action="toit.php" id="otsinguvorm">
                <div>
                    <label for="otsityyp">Toidu tüüp:</label>
                    <select name="otsityyp" id="otsityyp">
                        <option value="">vali...</option>
                        <option value="kuiv">kuiv</option>
                        <option value="konserv">konserv</option>
                    </select>
                </div>

                <div>
                    <label for="otsitootja">Tootja:</label>
                    <select name="otsitootja" id="otsitootja">
                        <option value="">vali...</option>
                        <option value="Royal Canin">Royal Canin</option>
                        <option value="Purina">Purina</option>
                        <option value="Hill's">Hill's</option>
                        <option value="Mars">Mars</option>
                        <option value="Nestlé">Nestlé</option>
                        <option value="Applaws">Applaws</option>
                        <option value="Brit">Brit</option>
                    </select>
                </div>

                <div class="otsingu-nupud">
                    <div>
                        <input type="submit" value="Otsi">
                        <button type="button" onclick="tyhjendaOtsing()">Tühjenda</button>
                    </div>
                </div>
                <br>
                <table>
                    <tr>
                        <th><a href="toit.php?sort=toidu_nimetus">Toidu nimetus</a></th>
                        <th><a href="toit.php?sort=tootja">Tootja</a></th>
                        <th><a href="toit.php?sort=tyyp">Tüüp</a></th>
                        <th><a href="toit.php?sort=sailivus_paevad">Säilivus päevad</a></th>
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
<?php include 'footer.php'; ?>
<?php if (isset($_GET["teade"])): ?>
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
