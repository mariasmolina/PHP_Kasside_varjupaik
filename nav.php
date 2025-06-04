<?php
// Saame teada, mis leht on praegu aktiivne (näiteks index.php)
$aktiivne_leht=basename($_SERVER['PHP_SELF']);
?>
<nav class="nav-peek">
    <img src="img/cat_peek.png" alt="Kass piilub" class="peek-nav-cat">
    <!--https://heroicons.com/ SVG ikoonid-->
    <div class="nav-content">
        <div class="nav-links">
            <a href="index.php" <?php if ($aktiivne_leht=='index.php')
                echo 'class="active"'; ?>><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#24594e" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 5px;  margin-bottom: 5px;">
                    <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                    <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                </svg>
                Avaleht</a>

            <?php if (isAdmin()): ?>
                <!-- Adminile eraldi link haldusele ja kasutajavaatele -->
                <a href="loomadAdmin.php" <?php if ($aktiivne_leht=='loomadAdmin.php')
                    echo 'class="active"'; ?>>Loomad - haldus</a>
                <a href="loomadKasutaja.php" <?php if ($aktiivne_leht=='loomadKasutaja.php')
                    echo 'class="active"'; ?>>Loomad</a>

            <?php elseif (isTootaja()): ?>
                <!-- Töötaja näeb ainult üht lehte loomade kohta -->
                <a href="loomadAdmin.php" <?php if ($aktiivne_leht=='loomadAdmin.php')
                    echo 'class="active"'; ?>>Loomad</a>
            <?php else: ?>
                <!-- Tavakasutaja näeb ainult kasutajavaadet -->
                <a href="loomadKasutaja.php" <?php if ($aktiivne_leht=='loomadKasutaja.php')
                    echo 'class="active"'; ?>>Loomad</a>
            <?php endif; ?>
            <!-- Toidu leht on nähtav kõigile rollidele -->
            <a href="toit.php" <?php if ($aktiivne_leht=='toit.php')
                echo 'class="active"'; ?>>Toit</a>

            <?php if (isAdmin()): ?>
                <!-- Adminil on ligipääs toitmise ajaloole -->
                <a href="toitmisAjalugu.php" <?php if ($aktiivne_leht=='toitmisAjalugu.php')
                    echo 'class="active"'; ?>>Toitmise ajalugu</a>
            <?php elseif (isTootaja()): ?>
                <!-- Töötaja saab samuti vaadata toitmise ajalugu -->
                <a href="toitmisAjalugu.php" <?php if ($aktiivne_leht=='toitmisAjalugu.php')
                    echo 'class="active"'; ?>>Toitmise ajalugu</a>
            <?php else: ?>
                <!-- Tavakasutaja ja külaline näevad kontaktilehte -->
                <a href="kontaktid.php" <?php if ($aktiivne_leht=='kontaktid.php')
                    echo 'class="active"'; ?>>Kontaktid</a>
            <?php endif; ?>
        </div>

        <div class="nav-user">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: -5px; margin-top: 4px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>

            <span>
                Tere, <?php
                if (isset($_SESSION['kasutaja_nimi'])) {
                    echo htmlspecialchars($_SESSION['kasutaja_nimi']);
                } else {
                    echo 'Külaline';
                }
                ?>!
            </span>
            <!-- Kui kasutaja on sisse logitud, näitame logout vormi -->
            <?php if (isset($_SESSION["kasutaja"])): ?>
                <form action="logout.php" method="post" class="logout_form">
                    <input type="submit" name="logout" value="Logi välja" class="login_logout_button">
                </form>
            <?php else: ?>
                <!-- Kui ei ole logitud, pakume login nupu -->
                <a href="login.php" class="login_logout_button">Logi sisse</a>
            <?php endif; ?>
        </div>
    </div>
</nav>