<?php
$parool = 'parool';
$sool = 'cool';
$krypt = crypt($parool, $sool);
echo $krypt;
