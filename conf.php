<?php
$serverinimi="localhost";
$kasutaja="mariasm";
$parool="12345";
$andmebaas="mariasm";

$yhendus=new mysqli($serverinimi,$kasutaja,$parool,$andmebaas);
$yhendus->set_charset("utf8");
