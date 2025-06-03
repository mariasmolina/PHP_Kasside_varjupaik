<?php
$parool = 'kasutaja123';
$sool = 'cool';
$krypt = crypt($parool, $sool);
echo $krypt;
