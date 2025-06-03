<?php
session_start();
if (!isset($_SESSION['kasutaja'])) {
    header('Location: index.php');
    exit();
}
if(isset($_POST['logout'])){
    // Tühjendame kõik sessiooni andmed
    $_SESSION = [];  // puhastame $_SESSION massiivi
    session_unset();  // eemaldame kõik sessiooni muutujad
    session_destroy();  // lõpetame sessiooni serveris
    header('Location: index.php');
    exit();
}
?>
