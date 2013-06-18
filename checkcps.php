<?php
require_once ('config.php');

$factureid = $_POST["factureid"];
$bool = $_POST["bool"];


$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);      
$query = "UPDATE `factures_cantine` SET `bourse`='$bool' WHERE `idfacture`='$factureid'";
$mysqli->query($query);
$mysqli->close();

echo $query;

?>