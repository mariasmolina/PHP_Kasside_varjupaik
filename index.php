<?php
require("conf.php");
session_start();
require("abifunktsioonid.php");
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasside Varjupaik</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="icon" href="img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>
<header>
    <img src="img/logo.png" alt="Kass" class="logo-img">
    <h1>KassiKODU</h1>
    <?php if (isAdmin()): ?>
        <span class="admin-badge">Admin</span>
    <?php endif; ?>
    <?php if (isTootaja()): ?>
        <span class="tootaja-badge">Töötaja</span>
    <?php endif; ?>
</header>
<?php include 'nav.php'; ?>
<?php if (isAdmin()): ?>
    <div class="container">
        <div class="welcome_block" >
            <img src="img/cat7.png" alt="Kass" class="welcome_img">
            <div style="height: 250px;">
                <h1><i class='fas fa-paw' style='font-size:24px'></i> Kasside varjupaik - PHP andmebaasi projekt</h1>
                <p>Tutvuge minu projektiga GitHubis – seal näete kogu lähtekoodi ja toimimist!</p>
                <br>
                <a href="https://github.com/mariasmolina/PHP_Kasside_varjupaik" class="button">Vaata GitHubis<i class="fa fa-github" style="font-size:24px; position:relative; top:3px; left:8px;" ></i></a>
            </div>
        </div>
        <div class="index_div" >
            <div class="lehe_info">
                <h2>Kasutaja rollid</h2>
                <p>Erinevate kasutajate õigused süsteemis:</p>
                <h3><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#24594e" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 5px;  margin-bottom: 5px;">
                        <path d="M4.5 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM14.25 8.625a3.375 3.375 0 1 1 6.75 0 3.375 3.375 0 0 1-6.75 0ZM1.5 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM17.25 19.128l-.001.144a2.25 2.25 0 0 1-.233.96 10.088 10.088 0 0 0 5.06-1.01.75.75 0 0 0 .42-.643 4.875 4.875 0 0 0-6.957-4.611 8.586 8.586 0 0 1 1.71 5.157v.003Z" />
                    </svg>
                    Külaline ja kasutaja</h3>
                <ul>
                    <li>Saab vaadata loomi, sh pilte ja kirjeldusi, toitude loetelu ja kontakti leht</li>
                    <li> Ei saa midagi lisada, muuta ega kustutada</li>
                </ul>
                <h3><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#24594e" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 5px;  margin-bottom: 5px;">
                        <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                    </svg>
                    Töötaja</h3>
                <ul>
                    <li>Saab vaadata loomi ja toitu</li>
                    <li>Saab lisada uusi toidu ja uusi toitmiskirjeid</li>
                    <li>Saab muuta ja kustutada AINULT enda toitmiskirjeid</li>
                    <li>Ei saa muuta loomade andmeid ja teiste töötajate ega administraatorite kirjeid</li>
                </ul>
                <h3><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#24594e" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 5px;  margin-bottom: 5px;">
                        <path fill-rule="evenodd" d="M12 6.75a5.25 5.25 0 0 1 6.775-5.025.75.75 0 0 1 .313 1.248l-3.32 3.319c.063.475.276.934.641 1.299.365.365.824.578 1.3.64l3.318-3.319a.75.75 0 0 1 1.248.313 5.25 5.25 0 0 1-5.472 6.756c-1.018-.086-1.87.1-2.309.634L7.344 21.3A3.298 3.298 0 1 1 2.7 16.657l8.684-7.151c.533-.44.72-1.291.634-2.309A5.342 5.342 0 0 1 12 6.75ZM4.117 19.125a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75h-.008a.75.75 0 0 1-.75-.75v-.008Z" clip-rule="evenodd" />
                        <path d="m10.076 8.64-2.201-2.2V4.874a.75.75 0 0 0-.364-.643l-3.75-2.25a.75.75 0 0 0-.916.113l-.75.75a.75.75 0 0 0-.113.916l2.25 3.75a.75.75 0 0 0 .643.364h1.564l2.062 2.062 1.575-1.297Z" />
                        <path fill-rule="evenodd" d="m12.556 17.329 4.183 4.182a3.375 3.375 0 0 0 4.773-4.773l-3.306-3.305a6.803 6.803 0 0 1-1.53.043c-.394-.034-.682-.006-.867.042a.589.589 0 0 0-.167.063l-3.086 3.748Zm3.414-1.36a.75.75 0 0 1 1.06 0l1.875 1.876a.75.75 0 1 1-1.06 1.06L15.97 17.03a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                    Admin</h3>
                <ul>
                    <li>Täielik ligipääs kõigile süsteemi osadele</li>
                    <li>Saab lisada, muuta ja kustutada kõike</li>
                    <li>Saab peita või kuvada loomi avalikul lehel</li>
                </ul>
            </div>
            <div class="lehe_info">
                <h2>Paroolid</h2>
                <p>Logi sisse erinevatesse rollidesse ja katseta süsteemi võimalusi:</p>
                <ul style="text-align: left;">
                    <li><strong>Admin</strong><br>
                        Kasutajanimi: <code>admin</code><br>
                        Parool: <code>admin</code></li><br>

                    <li><strong>Töötajad</strong><br>
                        Kasutajanimi: <code>tootaja1</code> / <code>tootaja2</code><br>
                        Parool: <code>tootaja1</code> / <code>tootaja2</code></li><br>

                    <li><strong>Kasutaja</strong><br>
                        Kasutajanimi: <code>kasutaja</code><br>
                        Parool: <code>kasutaja123</code></li>
                </ul>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="container">
        <div class="welcome_block">
            <img src="img/cat7.png" alt="Kass" class="welcome_img">
            <div>
                <h1><i class='fas fa-paw' style='font-size:24px'></i> KassiKODU – koht, kus saba leiab kodu</h1>
                <p>Meie eesmärk on leida iga kassile hooliv kodu.<br>
                    Vaadake allpool meie armsaid kasse, kes otsivad uut peret.</p>
                <br>
                <a href="loomadKasutaja.php" class="button">Kasulik siit</a>
                <br>
            </div>
        </div>
        <div class="index_div">
            <div class="lehe_info">
                <h2>Loomad</h2>
                <p>Tutvu meie armsate kassidega, kes otsivad uut kodu – ehk ootab just sind üks nurruv sõber!</p>
                <br>
                <a href="loomadKasutaja.php" class="button">Vaata kasse</a>
            </div>
            <div class="lehe_info">
                <h2>Toit</h2>
                <p>Vaadake, milline kassitoit on hetkel varjupaigas olemas – see aitab teil otsustada, mida võiks juurde tuua.</p>
                <br>
                <a href="toit.php" class="button">Vaata toitu</a>
            </div>
        </div>
        <h2 class="index_title">Kuidas saad aidata?</h2>
        <div class="abi_info_div">
            <div class="abi_info">
                <!-- https://www.w3schools.com/icons/fontawesome_icons_intro.asp -->
                <i class="fa-solid fa-hands-helping"></i>
                <h3>Vabatahtlik töö</h3>
                <p>Aita kasse igapäevastes tegevustes ja leia endale südamesõber.</p>
            </div>
            <div class="abi_info">
                <i class="fa-solid fa-euro-sign"></i>
                <h3>Annetused</h3>
                <p>Iga euro loeb – toeta meie varjupaika ja selle elanikke.</p>
            </div>
            <div class="abi_info">
                <i class="fa-solid fa-gift"></i>
                <h3>Too asju</h3>
                <p>Mänguasjad, liiv, toit – kõik aitab muuta elu hubasemaks.</p>
            </div>
        </div>
        <br>
        <h2 class="index_title">Meie armsad kassid</h2>
        <div class="kassi_info_div">
            <?php
            global $yhendus;
            $kask = $yhendus->prepare("SELECT pilt, looma_nimi FROM loom LIMIT 3");
            $kask->bind_result($pilt, $nimi);
            $kask->execute();

            while ($kask->fetch()) {
                echo "<div class='kassi_info'>";
                echo "<img src='" . htmlspecialchars($pilt) . "' alt='" . htmlspecialchars($nimi) . "'>";
                echo "<h3>" . htmlspecialchars($nimi) . "</h3>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
<?php endif; ?>
<?php include 'footer.php'; ?>
</body>
</html>
