<?php include('conf.php'); ?>
<?php
session_start();
global $yhendus;

if (isset($_POST["submit"])){

    require("abifunktsioonid.php");

    $kasutaja_nimi = htmlspecialchars(trim($_POST["kasutaja_nimi"]));
    $login = htmlspecialchars(trim($_POST['login']));
    $pass = htmlspecialchars(trim($_POST['pass']));
    $passKord = htmlspecialchars(trim($_POST['pass_kord']));

    if (emptyInputSignup($login, $pass) !== false){
        header('location: signup.php?error=emptyinput');
        exit();
    }
    if (invalidUid($login) !== false){
        header('location: signup.php?error=invaliuid');
        exit();
    }
    if (pwdMatch($pass, $passKord) !== false){
        header('location: signup.php?error=passwordsdontmatch');
        exit();
    }
    if (uidExists($yhendus, $login) !== false){
        header('location: signup.php?error=usernametaken');
        exit();
    }

    createUser($yhendus, $kasutaja_nimi, $login, $pass);
}
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Uue konto registreerimine</title>
    <link rel="icon" href="img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="css/signupStyle.css">
</head>
<body>
<div class="login_leht">
    <div class="signup_form">
        <form action="" method="post">
            <h1>Loo uus konto</h1>
            <p>Registreerimine annab sulle ligipääsu meie kasside maailmale</p>
            <table>
                <tr<label for="signup_login" class="signup_label">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="#24594e" style="vertical-align: middle; margin-right: 5px;  margin-bottom: 3px;">
                        <path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" clip-rule="evenodd" />
                    </svg>
                    Ees- ja Perekonnanimi:</label></tr>
                <tr><input type="text" id="signup_login" name="kasutaja_nimi" placeholder="Kasutajanimi"></tr>
                <tr<label for="signup_login" class="signup_label">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="#24594e" style="vertical-align: middle; margin-right: 5px;  margin-bottom: 3px;">
                        <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                    </svg>
                    Login:</label></tr>
                <tr><input type="text" id="signup_login" name="login" placeholder="Kasutajanimi"></tr>

                <tr><label for="signup_parool" class="signup_label"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"  width="16" height="16" fill="#24594e" style="vertical-align: middle; margin-right: 5px;  margin-bottom: 3px;">
                            <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                        </svg>
                        Parool:</label></tr>
                <tr><input type="password" id="signup_parool" name="pass" placeholder="Parool"></tr>

                <tr><label for="signup_parool2" class="signup_label">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"  width="16" height="16" fill="#24594e" style="vertical-align: middle; margin-right: 5px;  margin-bottom: 3px;">
                            <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                        </svg>
                        Korrake parool:</label></tr>
                <tr><input type="password" id="signup_parool2" name="pass_kord" placeholder="Korrake parool"></tr>
                <tr>
                    <td colspan="3" style="text-align: center;">
                        <input type="submit" name="submit" value="Registreeru">
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="tagasi-link-cell">
                        <a href="login.php" class="tagasi-link">← Tagasi sisselogimisele</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
</body>
</html>
