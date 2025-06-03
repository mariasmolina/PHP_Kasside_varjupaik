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

if (isset($_REQUEST["kustutusid"]) && isAdmin()) {
    kustutaKasutaja($_REQUEST["kustutusid"]);
    header("Location: kasutajad.php?teade=kustutatud");
}
if (isset($_REQUEST["muutmine"]) && isAdmin()) {
    muudaKasutaja($_REQUEST["muudetudid"], $_REQUEST["kasutaja_nimi"], $_REQUEST["login"], $_REQUEST["parool"], 0, $_REQUEST["istootaja"]);
    header("Location: kasutajad.php?teade=muudetud");
}
$kasutajad=kasutajaAndmed();
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <title>Kasside varjupaik</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link rel="icon" href="img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
<?php
if (isset($_SESSION['kasutaja'])) {
    ?>
    <header>
        <h1>Süsteemi kasutajad</h1>
        <?php if (isAdmin()): ?>
            <span class="admin-badge">Admin</span>
        <?php endif; ?>
    </header>
    <?php include 'nav.php'; ?>
    <div class="kasutaja_tabeli_container">
        <main class="kasutaja_tabel">
            <table>
                <tr>
                    <th>Ees- ja Perekonnanimi</a></th>
                    <th>Login</a></th>
                    <th>Parool</a></th>
                    <th>Staatus</a></th>
                    <th>Haldus</th>
                </tr>
                <?php foreach($kasutajad as $kasutaja): ?>
                    <?php if(isSet($_REQUEST["muutmisid"]) && intval($_REQUEST["muutmisid"])==$kasutaja->id): ?>
                        <tr>
                            <td><input type="text" name="kasutaja_nimi" value="<?=$kasutaja->kasutajaa_nimi ?>" /></td>
                            <td><input type="text" name="login" value="<?=$kasutaja->login ?>" /></td>
                            <td><input type="text" name="parool" value="<?=$kasutaja->parool ?>" /></td>
                            <td>
                                <select name="istootaja">
                                    <?php
                                    if ($kasutaja->istootaja==0) {
                                        echo '<option value="0" selected>Kasutaja</option>';
                                        echo '<option value="1">Töötaja</option>';
                                    } else {
                                        echo '<option value="0">Kasutaja</option>';
                                        echo '<option value="1" selected>Töötaja</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <input type="submit" name="muutmine" class="muuda_nupp" value="Muuda" />
                                <input type="submit" name="katkestus" class="kustuta_nupp" value="Katkesta" />
                                <input type="hidden" name="muudetudid" value="<?=$kasutaja->id ?>" />
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td><?=$kasutaja->kasutaja_nimi ?></td>
                            <td><?=$kasutaja->login ?></td>
                            <td><?=$kasutaja->parool ?></td>
                            <td>
                                <?php
                                if ($kasutaja->onadmin == 1) {
                                    echo "Admin";
                                } elseif ($kasutaja->istootaja == 1) {
                                    echo "Töötaja";
                                } else {
                                    echo "Kasutaja";
                                }
                                ?>
                            </td>
                            <?php if (isAdmin()): ?>
                                <td>
                                    <a href="kasutajad.php?kustutusid=<?=$kasutaja->id ?>" class="kustuta_nupp"
                                       onclick="return confirm('Kas ikka soovid kustutada?')">Kustuta</a>
                                    <a href="kasutajad.php?muutmisid=<?=$kasutaja->id ?>" class="muuda_nupp">Muuda</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </table>
        </main>
    </div>
    <?php include 'footer.php'; ?>
    <?php
}
?>
<?php if (isset($_GET["teade"])): ?>
    <script>
        window.onload = function () {
            <?php if ($_GET["teade"] === "muudetud"): ?>
            alert("Andmed on edukalt muudetud!");
            <?php elseif ($_GET["teade"] === "lisatud"): ?>
            alert("Uus kirje lisatud!");
            <?php elseif ($_GET["teade"] === "kustutatud"): ?>
            alert("Kirje on kustutatud!");
            <?php endif; ?>
        };
    </script>
<?php endif; ?>
</body>
</html>

