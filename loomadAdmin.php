<?php
require("conf.php");
global $yhendus;
session_start();

// Kui kasutaja on admin, siis määrame admin õigused
if (isset($_SESSION['kasutaja']) && $_SESSION['kasutaja'] === 'admin') {
    $_SESSION['admin'] = true;
} else {
    $_SESSION['admin'] = false;
}
require("abifunktsioonid.php");

// Sorteerimise ja otsingu muutujad
$sorttulp="synniaeg";
$otsitoug = "";
$otsisugu = "";

// kuvamine
if(isSet($_REQUEST["kuva_id"])) {
    $paring = $yhendus->prepare("UPDATE loom SET avalik=1 WHERE id=?");
    $paring->bind_param("i", $_REQUEST["kuva_id"]);
    $paring->execute();
    header("Location:$_SERVER[PHP_SELF]");
}

// peitmine
if(isSet($_REQUEST["peida_id"])) {
    $paring = $yhendus->prepare("UPDATE loom SET avalik=0 WHERE id=?");
    $paring->bind_param("i", $_REQUEST["peida_id"]);
    $paring->execute();
    header("Location:$_SERVER[PHP_SELF]");
}
// Kui vajutati "Lisa tõug", kontrollime kas nimi ei ole tühi ja allergiasobralik olemas
if(isSet($_REQUEST["tougulisamine"]) && trim($_REQUEST["uusTouguNimetus"]) !== ""
    && isset($_REQUEST["uusAllergiaStaatus"])
) {
    $toug_nimetus=trim($_REQUEST["uusTouguNimetus"]);
    $allergiasobralik=intval($_REQUEST["uusAllergiaStaatus"]);
    if(touguKontroll($toug_nimetus)==0){
        lisaToug($toug_nimetus, $allergiasobralik);
        header("Location: loomadAdmin.php?teade=toug_lisatud");
        exit();
    }
    else {
        header("Location: loomadAdmin.php?teade=toug_olemas");
        exit();
    }
}
// Kui kõik vajalikud väljad on täidetud, lisame uue looma
if(isSet($_REQUEST["loomalisamine"]) &&
    !empty(trim($_REQUEST["looma_nimi"])) &&
    !empty(trim($_REQUEST["kaal"])) &&
    !empty(trim($_REQUEST["synniaeg"])) &&
    !empty(trim($_REQUEST["sugu"])) &&
    !empty($_REQUEST["toug_id"])&&
    !empty(trim($_REQUEST["pilt"]))) {

    // Kontroll: kui sisestatud sünniaeg on tulevikus
    $synd=$_REQUEST["synniaeg"];
    if (strtotime($synd)>time()) {
        header("Location: loomadAdmin.php?teade=vale_kuupaev");
        exit();
    }

    lisaLoom($_REQUEST["looma_nimi"], $_REQUEST["kaal"], $_REQUEST["synniaeg"], $_REQUEST["sugu"], $_REQUEST["toug_id"], $_REQUEST["pilt"]);
    header("Location: loomadAdmin.php?teade=lisatud");
    exit();
}
// sortimine
if(isSet($_REQUEST["sort"])){$sorttulp=$_REQUEST["sort"];
}
//otsing
if (isset($_REQUEST["otsitoug"])) {
    $otsitoug=$_REQUEST["otsitoug"];
}
// otsing
if (isset($_REQUEST["otsisugu"])) {
    $otsisugu=$_REQUEST["otsisugu"];
}
// Kui on admin ja vajutatakse "Kustuta"
if (isset($_REQUEST["kustutusid"]) && isAdmin()) {
    kustutaLoom($_REQUEST["kustutusid"]);
    header("Location: loomadAdmin.php?teade=kustutatud");
}
// Kui admin vajutab "Muuda"
if (isset($_REQUEST["muutmine"]) && isAdmin()) {
    // Kontrollime, et muudetud sünniaeg ei oleks tulevikus
    $synd = $_REQUEST["synniaeg"];
    if (strtotime($synd) > time()) {
        header("Location: loomadAdmin.php?teade=vale_kuupaev");
        exit();
    }
    muudaLoom($_REQUEST["muudetudid"], $_REQUEST["looma_nimi"], $_REQUEST["kaal"], $_REQUEST["synniaeg"], $_REQUEST["sugu"], $_REQUEST["toug_id"]);
    header("Location: loomadAdmin.php?teade=muudetud");
}
// Pärime loomade nimekirja (sorteeritud ja otsinguga)
$loomad=kysiLoomaAndmed($sorttulp, $otsitoug, $otsisugu);
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
        <!-- JavaScript funktsioon otsingu tühjendamiseks -->
        function tyhjendaOtsing() {
            document.getElementById('otsitoug').value = '';
            document.getElementById('otsisugu').value = '';
            document.getElementById('otsinguvorm').submit();
        }
    </script>
</head>
<body>
<?php
// Kontrollime, kas kasutaja on sisse logitud
if (isset($_SESSION['kasutaja'])) {
    ?>
    <header>
        <h1>Kasside loetelu</h1>
        <!-- Kui on admin, siis näitame admin-märki -->
        <?php if (isAdmin()): ?>
            <span class="admin-badge">Admin</span>
        <?php endif; ?>
        <!-- Kui on töötaja, siis töötaja-märk -->
        <?php if (isTootaja()): ?>
            <span class="tootaja-badge">Töötaja</span>
        <?php endif; ?>
    </header>
    <!-- Lisame navigeerimismenüü -->
    <?php include 'nav.php'; ?>
    <div class="tabeli_container">
        <?php if (isAdmin()): ?>
            <aside class="aside_login">
                <!-- Vorm uue looma lisamiseks -->
                <form action="loomadAdmin.php" id="lisamisvorm" method="get">
                    <h2>Looma lisamine</h2>
                    <br>
                    <label for="looma_nimi">Looma nimi:</label>
                    <input type="text" name="looma_nimi" id="looma_nimi">
                    <br>
                    <label for="kaal">Kaal:</label>
                    <input type="number" name="kaal" id="kaal" min="0" max="30" step="0.01" placeholder="kilogrammid">
                    <br>
                    <label for="">Sünniaeg:</label>
                    <input type="date" name="synniaeg" id="synniaeg">
                    <br>
                    <fieldset class="sugu-valik">
                        <legend><strong>Sugu:</strong></legend>
                        <input type="radio" name="sugu" id="emane" value="Emane">
                        <label for="emane">Emane</label>
                        <input type="radio" id="isane" name="sugu" value="Isane">
                        <label for="isane">Isane</label>
                    </fieldset>
                    <br>
                    <label for="">Tõug:</label>
                    <?php
                    echo looRippMenyy("SELECT id, toug_nimetus FROM toug",
                        "toug_id");
                    ?>
                    <br>
                    <label for="pilt">Pildi link:</label>
                    <!-- Kui kasutaja sisestab pildi lingi, uuendatakse eelvaade all JS abil -->
                    <input type="url" name="pilt" id="pilt" placeholder="https://naidis.ee/pilt.jpg"
                           oninput="document.getElementById('preview').src = this.value;">
                    <br>
                    <img id="preview" src="">
                    <br><br>
                    <input type="submit" name="loomalisamine" value="Lisa loom" />
                    <br><br>
                    <hr>
                    <h2>Tõugu lisamine</h2>
                    <br>
                    <label for="uusTouguNimetus">Tõugu nimetus:</label>
                    <input type="text" name="uusTouguNimetus" id="uusTouguNimetus">
                    <br>
                    <label for="uusAllergiaStaatus">Allergiasõbralik:</label>
                    <select name="uusAllergiaStaatus" id="uusAllergiaStaatus">
                        <option value="1">Jah</option>
                        <option value="0">Ei</option>
                    </select>
                    <br><br>
                    <input type="submit" name="tougulisamine" value="Lisa tõug">
                </form>
            </aside>
        <?php endif; ?>
        <?php if (isAdmin()): ?>
        <main class="table">
            <?php else: ?>
            <!-- Kitsam vaade tavakasutajale -->
            <main class="table" style="max-width: 900px; margin: 0 auto;">
                <?php endif; ?>
                <!-- otsing -->
                <form action="loomadAdmin.php" id="otsinguvorm">
                    <div>
                        <!-- Tõu valik -->
                        <label for="otsitoug">Tõug:</label>
                        <select name="otsitoug" id="otsitoug">
                            <option value="">vali...</option>
                            <?php
                            $kask = $yhendus->prepare("SELECT id, toug_nimetus FROM toug");
                            $kask->bind_result($id, $sisu);
                            $kask->execute();
                            while ($kask->fetch()) {
                                // Kuvab valiku valituks, kui see vastab kasutaja sisestatud andmetele
                                if ($_REQUEST['otsitoug']==$id) {
                                    echo "<option value='$id' selected>$sisu</option>";
                                } else {
                                    echo "<option value='$id'>$sisu</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Sugu valik -->
                    <div>
                        <label for="otsisugu">Sugu:</label>
                        <select name="otsisugu" id="otsisugu">
                            <option value="">vali...</option>
                            <!-- Kuvab valiku valituks, kui see vastab kasutaja sisestatud andmetele -->
                            <option value="Isane" <?php if (isset($_REQUEST['otsisugu']) && $_REQUEST['otsisugu']=='Isane')
                                echo 'selected'; ?>>Isane</option>
                            <option value="Emane" <?php if (isset($_REQUEST['otsisugu']) && $_REQUEST['otsisugu']=='Emane')
                                echo 'selected'; ?>>Emane</option>
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
                            <!-- Sortimislingid tulpadel -->
                            <th><a href="loomadAdmin.php?sort=looma_nimi">Looma nimi</a></th>
                            <th><a href="loomadAdmin.php?sort=sugu">Sugu</a></th>
                            <th><a href="loomadAdmin.php?sort=synniaeg">Sünniaeg</a></th>
                            <th><a href="loomadAdmin.php?sort=kaal">Kaal</a></th>
                            <th><a href="loomadAdmin.php?sort=toug_nimetus">Tõug</a></th>
                            <?php if (isAdmin()): ?>
                                <th>Haldus</th>
                                <th colspan="2">Staatus</th>
                            <?php endif; ?>
                        </tr>
                        <?php foreach($loomad as $loom): ?>
                            <?php if(isSet($_REQUEST["muutmisid"]) && intval($_REQUEST["muutmisid"])==$loom->id): ?>
                                <tr>
                                    <!-- Vorm muutmiseks -->
                                    <td><input type="text" name="looma_nimi" value="<?=$loom->looma_nimi ?>" /></td>
                                    <td>
                                        <select name="sugu" id="sugu">
                                            <option value="<?=$loom->sugu  ?>"><?=$loom->sugu  ?></option>
                                            <?php
                                            $sood=["Emane","Isane"];
                                            foreach ($sood as $sugu) {
                                                if ($sugu!==$loom->sugu) {
                                                    echo "<option value=\"$sugu\">$sugu</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td><input type="date" name="synniaeg" value="<?=$loom->synniaeg ?>" /></td>
                                    <td><input type="number" name="kaal" min="0" max="30" step="0.01" placeholder="kilogrammid" value="<?=$loom->kaal ?>" /></td>
                                    <td>
                                        <?php
                                        echo looRippMenyy("SELECT id, toug_nimetus FROM toug ", "toug_id", $loom->toug_id);
                                        ?>
                                    </td>
                                    <td>
                                        <input type="submit" name="muutmine" class="muuda_nupp" value="Muuda" />
                                        <input type="submit" name="katkestus" class="kustuta_nupp" value="Katkesta" />
                                        <input type="hidden" name="muudetudid" value="<?=$loom->id ?>" />
                                        <input type="hidden" name="sort" value="<?=htmlspecialchars($sorttulp)?>">
                                        <input type="hidden" name="otsitoug" value="<?=htmlspecialchars($otsitoug)?>">
                                        <input type="hidden" name="otsisugu" value="<?=htmlspecialchars($otsisugu)?>">
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td><?=$loom->looma_nimi ?></td>
                                    <td><?=$loom->sugu ?></td>
                                    <td><?=$loom->synniaeg ?></td>
                                    <td><?=$loom->kaal ?></td>
                                    <td><?=$loom->toug_nimetus ?></td>
                                    <?php if (isAdmin()): ?>
                                        <td>
                                            <a href="loomadAdmin.php?kustutusid=<?=$loom->id ?>" class="kustuta_nupp"
                                               onclick="return confirm('Kas ikka soovid kustutada?')">Kustuta</a>
                                            <a href="loomadAdmin.php?muutmisid=<?=$loom->id ?>" class="muuda_nupp">Muuda</a>
                                        </td>
                                        <!-- Näita / Peida nupp ja ikoon -->
                                        <?php
                                            $tekst="Näita";
                                            $avaparametr="kuva_id";
                                            $seis="<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='#24594e' width='24' height='24'>
            <path d='M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z' />
            <path d='M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.244 4.243Z' />
            <path d='M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z' />
        </svg>";
                                            if($loom->avalik==1){
                                            $tekst="Peida";
                                            $avaparametr="peida_id";
                                            $seis="<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='#24594e' width='24' height='24'>
                <path d='M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z' />
                <path fill-rule='evenodd' d='M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z' clip-rule='evenodd' />
            </svg>";
                                            }
                                            echo "<td><a class='staatus' href='?$avaparametr={$loom->id}'>$tekst</a></td>";
                                            echo "<td>$seis</td>";
                                            echo "</tr>";
                                        ?>
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
    <!-- Teadete kuvamine -->
    <script>
        window.onload = function () {
            <?php if ($_GET["teade"] === "muudetud"): ?>
            alert("Andmed on edukalt muudetud!");
            <?php elseif ($_GET["teade"] === "lisatud"): ?>
            alert("Uus kirje lisatud!");
            <?php elseif ($_GET["teade"] === "kustutatud"): ?>
            alert("Kirje on kustutatud!");
            <?php elseif ($_GET["teade"] === "toug_olemas"): ?>
            alert("Tõug on juba olemas, lisa teistsugune!");
            <?php elseif ($_GET["teade"] === "toug_lisatud"): ?>
            alert("Uus tõug on lisatud edukalt!");
            <?php elseif ($_GET["teade"] === "vale_kuupaev"): ?>
            alert("Viga! sünniaeg ei saa olla tulevikus!");
            <?php endif; ?>
        };
    </script>
<?php endif; ?>
</body>
</html>
