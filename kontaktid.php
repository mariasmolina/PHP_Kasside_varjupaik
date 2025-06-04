<?php
session_start();
require("abifunktsioonid.php");
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasside Varjupaik - Kontaktid</title>
    <link rel="icon" href="img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <!-- Montserrat font Google'ist -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>

<header class="main-header">
    <h1>Kontaktid</h1>
</header>
<!-- Lisame navigeerimismenüü -->
<?php include 'nav.php'; ?>

<div class="container">

    <!-- Rida: kontakt ja töötajad -->
    <div class="kontakt_rida">
        <!-- Kontaktinfo -->
        <div class="kontakt_div">
            <h2>Võta meiega ühendust</h2>
            <p>Kui soovid meie kasside kohta rohkem teada või tahad tulla külla, võta meiega ühendust!</p>
            <!--Kontaktandmed SVG ikoonidega - https://heroicons.com/ -->
            <div class="contact_info">
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke-width="2" stroke="#2f7f4f" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 8px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                    info@kassid.ee
                </p>
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#2f7f4f" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 8px;">
                        <path d="M6.62 10.79a15.91 15.91 0 006.59 6.59l2.2-2.2a1 1 0 011.05-.24 11.36 11.36 0 003.55.57 1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1 11.36 11.36 0 00.57 3.55 1 1 0 01-.24 1.05l-2.2 2.2z"/>
                    </svg>
                    +372 5555 1234
                </p>
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#2f7f4f" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 8px;">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1114.5 9 2.5 2.5 0 0112 11.5z"/>
                    </svg>
                    Sõpruse pst 182, Tallinn
                </p>
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke-width="2" stroke="#2f7f4f" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 8px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    E-R: 10-18
                    <br>
                    L, P: suletud
                </p>
            </div>
        </div>

        <!-- Töötajad -->
        <div class="kontakt_div">
            <h2>Meie töötajad</h2>
            <br>
            <ul class="kontakt_info_list">
                <li><strong>Maria Smolina</strong> – Varjupaiga juhataja</li>
                <li><strong>Rasmus Tamm</strong> – Kasside hooldaja</li>
                <li><strong>Liis Tammepuu</strong> – Kasside hooldaja</li>
                <li><strong>Marika Mägi</strong> – Veterinaar</li>
                <li><strong>Toomas Mets</strong> – Veterinaar</li>
            </ul>
        </div>
    </div>

    <!-- Kaart Google Maps'ist -->
    <div class="kontakt_kaart">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1207.219952512161!2d24.704162944739313!3d59.41149291864945!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x469294f1bd8cb15b%3A0xc372ff05bb27e9f4!2sTallinna%20T%C3%B6%C3%B6stushariduskeskus!5e0!3m2!1set!2see!4v1748718187679!5m2!1set!2see"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>

</div>
<!-- Lisame lehe lõpu jaluse -->
<?php include 'footer.php'; ?>
</body>
</html>
