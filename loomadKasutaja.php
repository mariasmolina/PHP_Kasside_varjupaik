<?php
// võtame ühendust conf.php failist
require ('conf.php');
session_start();
require("abifunktsioonid.php");
global $yhendus;
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Kasside varjupaik</title>
    <link rel="icon" href="img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
<header>
    <h1>Vabad südamesõbrad</h1>
    <!-- Kui admin on sisse logitud, näitame märki -->
    <?php if (isAdmin()): ?>
        <span class="admin-badge">Admin</span>
    <?php endif; ?>
</header>
    <!-- Lisame navigeerimismenüü -->
    <?php include 'nav.php'; ?>
    <div class="valik-sisu">
        <?php
        // Pärime andmebaasist kõik kassid, kes on avalikud
        $kask = $yhendus->prepare("
SELECT pilt, looma_nimi, synniaeg, sugu, toug_nimetus
FROM loom 
JOIN toug ON loom.toug_id = toug.id
WHERE avalik=1
");
        $kask->bind_result($pilt, $nimi, $synniaeg, $sugu, $tougnimi);
        $kask->execute();

        echo "<div class='galerii'>";
        while ($kask->fetch()) {
            // Näitame pildi ja andmed looma kohta
            echo "<div class='galerii_kaart'>";
            echo "<img src='" . htmlspecialchars($pilt) . "' alt='Foto' class='galerii_img'>";
            echo "<h3>" . htmlspecialchars($nimi) . "</h3>";
            echo "<p><strong><i class='fas fa-venus-mars' style='font-size:18px'></i></strong> $sugu</p>";
            echo "<p><strong>Sünniaeg:</strong> $synniaeg</p>";
            echo "<p><strong>Tõug: </strong>". htmlspecialchars($tougnimi) ."</p>";
            echo "</div>";
        }
        echo "</div>";
        ?>
    </div>
<!-- Lisame lehe lõpu jaluse -->
<?php include 'footer.php'; ?>
</body>
</html>
