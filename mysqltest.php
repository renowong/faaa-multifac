<?php
echo "testing connection";
$link = mysql_connect('localhost','multifac','multifac');
if (!$link) { die('Could not connect to MySQL: ' . mysql_error());} echo 'Connection OK';
mysql_close($link);

?>