<?php
require ('conf.php');

// Kontrollime, kas kasutaja on töötaja
function isTootaja(): bool {
    return isset($_SESSION['tootaja']) && $_SESSION['tootaja'];
}

// Kontrollime, kas kasutaja on admin
function isAdmin(): bool {
    return isset($_SESSION['admin']) && $_SESSION['admin'];
}

// Tabel 'toitmisajalugu'
function kysiAndmed($sorttulp="kuupaev", $otsiloom = '', $otsikuupaev = ''){
    global $yhendus;
    $lubatudtulbad=array("looma_nimi", "kuupaev", "toidu_nimetus", "kogus", "kasutaja_nimi");
    if(!in_array($sorttulp, $lubatudtulbad)){
        return "lubamatu tulp";
    }

    $tingimused=" WHERE toitmisajalugu.toit_id=toit.id AND 
    toitmisajalugu.loom_id = loom.id AND
    toitmisajalugu.tootaja_id = ab_kasutajad.id";

    if (!empty($otsiloom)) {
        $otsiloom=addslashes($otsiloom);
        $tingimused = $tingimused . " AND loom.id='$otsiloom' ";
    }
    if (!empty($otsikuupaev)) {
        $otsikuupaev=addslashes($otsikuupaev);
        $tingimused = $tingimused . " AND DATE(kuupaev)='$otsikuupaev' ";
    }

    $kask = $yhendus->prepare("
        SELECT 
            toitmisajalugu.id, 
            toitmisajalugu.kuupaev, 
            toitmisajalugu.kogus, 
            toitmisajalugu.toit_id,
            toit.toidu_nimetus, 
            toitmisajalugu.loom_id,
            loom.looma_nimi, 
            toitmisajalugu.tootaja_id,
            ab_kasutajad.kasutaja_nimi
        FROM toitmisajalugu, toit, loom, ab_kasutajad
        $tingimused
        ORDER BY $sorttulp
    ");

    $kask->bind_result($id, $kuupaev, $kogus, $toit_id, $toidu_nimetus, $loom_id, $looma_nimi, $tootaja_id, $kasutaja_nimi);
    $kask->execute();

    $hoidla=array();
    while($kask->fetch()){
        $loom=new stdClass();
        $loom->id = $id;
        $loom->kuupaev = htmlspecialchars($kuupaev);
        $loom->kogus = htmlspecialchars($kogus);
        $loom->toit_id = $toit_id;
        $loom->toidu_nimetus = $toidu_nimetus;
        $loom->loom_id = $loom_id;
        $loom->looma_nimi = $looma_nimi;
        $loom->tootaja_id = $tootaja_id;
        $loom->kasutaja_nimi = $kasutaja_nimi;
        array_push($hoidla, $loom);
    }
    return $hoidla;
}


function looRippMenyy($sqllause, $valikunimi, $valitudid=""){
    global $yhendus;
    $kask=$yhendus->prepare($sqllause);
    $kask->bind_result($id, $sisu);
    $kask->execute();
    $tulemus="<select name='$valikunimi' id='$valikunimi'>";
    while($kask->fetch()){
        $lisand="";
        if($id==$valitudid){$lisand=" selected='selected'";}
        $tulemus.="<option value='$id' $lisand >$sisu</option>";
    }
    $tulemus.="</select>";
    return $tulemus;
}

// Tabel 'loom'
function kysiLoomaAndmed($sorttulp="synniaeg", $otsitoug = '', $otsisugu = ''){
    global $yhendus;
    $lubatudtulbad=array("looma_nimi", "kaal", "synniaeg", "sugu", "toug_nimetus", "avalik");
    if(!in_array($sorttulp, $lubatudtulbad)){
        return "lubamatu tulp";
    }

    $tingimused=" WHERE loom.toug_id=toug.id";

    if (!empty($otsitoug)) {
        $otsitoug=addslashes($otsitoug);
        $tingimused=$tingimused . " AND loom.toug_id='$otsitoug' ";
    }
    if (!empty($otsisugu)) {
        $otsisugu=addslashes($otsisugu);
        $tingimused = $tingimused . " AND sugu='$otsisugu' ";
    }

    $kask = $yhendus->prepare("
        SELECT 
            loom.id, 
            loom.looma_nimi, 
            loom.kaal, 
            loom.synniaeg,
            loom.sugu,
            loom.toug_id,
            toug.toug_nimetus,
            loom.avalik
        FROM loom, toug
        $tingimused
        ORDER BY $sorttulp
    ");

    $kask->bind_result($id, $looma_nimi, $kaal, $synniaeg, $sugu, $toug_id, $toug_nimetus, $avalik);
    $kask->execute();

    $hoidla=array();
    while($kask->fetch()){
        $loom=new stdClass();
        $loom->id = $id;
        $loom->looma_nimi = htmlspecialchars($looma_nimi);
        $loom->kaal = htmlspecialchars($kaal);
        $loom->synniaeg = htmlspecialchars($synniaeg);
        $loom->sugu = htmlspecialchars($sugu);
        $loom->toug_id = $toug_id;
        $loom->toug_nimetus = $toug_nimetus;
        $loom->avalik = htmlspecialchars($avalik);
        array_push($hoidla, $loom);
    }
    return $hoidla;
}

// Lisame uue looma andmebaasi
function lisaLoom($looma_nimi, $kaal, $synniaeg, $sugu, $toug_id, $pilt){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO loom (looma_nimi, kaal, synniaeg, sugu, toug_id, pilt)
        VALUES (?, ?, ?, ?, ?, ?)");
    $kask->bind_param("sdssis", $looma_nimi, $kaal, $synniaeg, $sugu, $toug_id, $pilt);
    $kask->execute();
}

// Lisame uue looma tõugu andmebaasi
function lisaToug($toug_nimetus, $allergiasobralik){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO toug (toug_nimetus, allergiasobralik)
VALUES (?, ?)");
    $kask->bind_param("si", $toug_nimetus, $allergiasobralik);
    $kask->execute();
}

// Lisame uue toitmise ajalugu andmed andmebaasi
function lisaToitmine($kuupaev, $kogus, $toit_id, $loom_id, $tootaja_id){
    global $yhendus;
    // Muudame formaadi "2025-05-10T14:30" -> "2025-05-10 14:30:00"
    $kuupaev=str_replace("T"," ",$kuupaev).":00";

    $kask=$yhendus->prepare("INSERT INTO
toitmisajalugu (kuupaev, kogus, toit_id, loom_id, tootaja_id)
VALUES (?, ?, ?, ?, ?)");
    $kask->bind_param("siiii", $kuupaev, $kogus, $toit_id, $loom_id, $tootaja_id);
    $kask->execute();
}

function kustutaLoomatoitmine($id){
    global $yhendus;
    $kask=$yhendus->prepare("DELETE FROM toitmisajalugu WHERE id=?");
    $kask->bind_param("i", $id);
    $kask->execute();
}

// Toitmisajalugu tabeli muutmine
function muudaLoomatoitmine($id, $loom_id, $kuupaev, $toit_id, $kogus, $tootaja_id){
    global $yhendus;

    $kuupaev = str_replace("T", " ", $kuupaev) . ":00";

    $kask = $yhendus->prepare("UPDATE toitmisajalugu SET loom_id=?, kuupaev=?, toit_id=?, kogus=?, tootaja_id=? WHERE id=?");
    $kask->bind_param("isiiii", $loom_id, $kuupaev, $toit_id, $kogus, $tootaja_id, $id);
    $kask->execute();
}

// Looma tabeli muutmine
function muudaLoom($id, $looma_nimi, $kaal, $synniaeg, $sugu, $toug_id){
    global $yhendus;
    $kask = $yhendus->prepare("UPDATE loom SET looma_nimi=?, kaal=?, synniaeg=?, sugu=?, toug_id=? WHERE id=?
    ");
    $kask->bind_param("sdssii", $looma_nimi, $kaal, $synniaeg, $sugu, $toug_id, $id);
    $kask->execute();
}

// Tabelist 'loom' andmeid kusututamine
function kustutaLoom($id){
    global $yhendus;
    $kask = $yhendus->prepare("DELETE FROM toitmisajalugu WHERE loom_id = ?");
    $kask->bind_param("i", $id);
    $kask->execute();

    $kask = $yhendus->prepare("DELETE FROM loom WHERE id = ?");
    $kask->bind_param("i", $id);
    $kask->execute();
}

// Tabel 'loom'
function kysiToiduAndmed($sorttulp="tootja", $otsityyp = '', $otsitootja = ''){
    global $yhendus;
    $lubatudtulbad=array("toidu_nimetus", "tootja", "tyyp", "sailivus_paevad");
    if(!in_array($sorttulp, $lubatudtulbad)){
        return "lubamatu tulp";
    }

    $tingimused = "WHERE 1=1";

    if (!empty($otsityyp)) {
        $otsityyp=addslashes($otsityyp);
        $tingimused .= " AND tyyp='$otsityyp' ";
    }
    if (!empty($otsitootja)) {
        $otsitootja=addslashes($otsitootja);
        $tingimused .= " AND tootja='$otsitootja' ";
    }

    $kask = $yhendus->prepare("
        SELECT id, toidu_nimetus, tootja, tyyp, sailivus_paevad
        FROM toit
        $tingimused
        ORDER BY $sorttulp
    ");

    $kask->bind_result($id, $toidu_nimetus, $tootja, $tyyp, $sailivus_paevad);
    $kask->execute();

    $hoidla=array();
    while($kask->fetch()){
        $toit=new stdClass();
        $toit->id = $id;
        $toit->toidu_nimetus = htmlspecialchars($toidu_nimetus);
        $toit->tootja = htmlspecialchars($tootja);
        $toit->tyyp = htmlspecialchars($tyyp);
        $toit->sailivus_paevad = htmlspecialchars($sailivus_paevad);
        array_push($hoidla, $toit);
    }
    return $hoidla;
}

function lisaToit($toidu_nimetus, $tootja, $tyyp, $sailivus_paevad){
    global $yhendus;

    $kask=$yhendus->prepare("INSERT INTO
toit(toidu_nimetus, tootja, tyyp, sailivus_paevad)
VALUES (?, ?, ?, ?)");
    $kask->bind_param("sssi", $toidu_nimetus, $tootja, $tyyp, $sailivus_paevad);
    $kask->execute();
}

function kustutaToit($id){
    global $yhendus;
    $kask=$yhendus->prepare("DELETE FROM toit WHERE id=?");
    $kask->bind_param("i", $id);
    $kask->execute();
}

// Toit tabeli muutmine
    function muudaToit($id, $toidu_nimetus, $tootja, $tyyp, $sailivus_paevad){
    global $yhendus;

    $kask = $yhendus->prepare("UPDATE toit SET toidu_nimetus=?, tootja=?, tyyp=?, sailivus_paevad=? WHERE id=?");
    $kask->bind_param("sssii", $toidu_nimetus, $tootja, $tyyp, $sailivus_paevad, $id);
    $kask->execute();
}


function touguKontroll($toug_nimetus, $allergiasobralik){
    global $yhendus;
    $kask=$yhendus->prepare("SELECT * FROM toug WHERE toug_nimetus = ? AND allergiasobralik = ?");
    $kask->bind_param("si", $toug_nimetus, $allergiasobralik);
    if($kask->execute()){
        $kask->store_result();
        $rida=$kask->num_rows;
        return $rida;
    }
}

function toitOlemas($nimetus, $tyyp) {
    global $yhendus;
    $kask = $yhendus->prepare("SELECT id FROM toit WHERE toidu_nimetus = ? AND tyyp = ?");
    $kask->bind_param("ss", $nimetus, $tyyp);
    $kask->execute();
    $kask->store_result();
    $rida=$kask->num_rows;
    return $rida;
}

// Registreerimis vorm - funktisoonid
function emptyInputSignup($login, $pass){
    global $result;
    if(empty($login) || empty($pass)){
        $result = true;
    }
    else{
        $result = false;
    }
    return $result;
}

function invalidUid($login){
    global $result;
    if(!preg_match("/^[a-zA-Z0-9]*$/", $login)){
        $result = true;
    }
    else{
        $result = false;
    }
    return $result;
}

function pwdMatch($pass, $passKord){
    global $result;
    if($pass != $passKord){
        $result = true;
    }
    else{
        $result = false;
    }
    return $result;
}

function uidExists($yhendus, $login){
    $sql = "SELECT * FROM ab_kasutajad WHERE login = ?";
    $stmt = mysqli_stmt_init($yhendus);
    if (!mysqli_stmt_prepare($stmt, $sql)){
        header("location: signup.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $login);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)){
        return $row;
    }
    else {
        $result = false;
        return $result;
    }

    mysqli_stmt_close($stmt);
}

function createUser($yhendus, $kasutaja_nimi, $login, $pass){
    $sql = "INSERT INTO ab_kasutajad (kasutaja_nimi, login, parool) VALUES (?, ?, ?)";
    $stmt = mysqli_stmt_init($yhendus);
    if (!mysqli_stmt_prepare($stmt, $sql)){
        header("location:signup.php?error=stmtfailed");
        exit();
    }

    $sool = "cool";
    $krypt = crypt($pass, $sool);

    mysqli_stmt_bind_param($stmt, "sss", $kasutaja_nimi, $login, $krypt);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("location:login.php?signup=success");
    exit();
}
?>

