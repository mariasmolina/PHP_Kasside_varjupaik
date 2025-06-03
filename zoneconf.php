<?php
$serverinimi="d133857.mysql.zonevs.eu";
$kasutaja="d133857_mariasmolina1";
$parool="Pengu1nS61017";
$andmebaas="d133857_phpbaas";

$yhendus=new mysqli($serverinimi,$kasutaja,$parool,$andmebaas);
$yhendus->set_charset("utf8");

