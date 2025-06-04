<?php
$serverinimi="d133857.mysql.zonevs.eu";
$kasutaja="d133857_mariasmolina1";
$parool="opilaneTARgv24!";
$andmebaas="d133857_phpbaas";

$yhendus=new mysqli($serverinimi,$kasutaja,$parool,$andmebaas);
$yhendus->set_charset("utf8");

