<?php
require_once ('config.php');
$avoirid = $_POST['avoirid'];
$acceptation = $_POST['acceptation'];
$comment = $_POST['comment'];
$userid = $_POST['userid'];

$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
$query = "UPDATE `".DB."`.`avoirs` SET `validation`='1', `acceptation`= '$acceptation',".
"`obs_valideur`='$comment', `date_validation`= CURRENT_TIMESTAMP, `valideur_id`='$userid' WHERE `idavoir`=$avoirid";
$mysqli->query($query);
$mysqli->close();

print $query;
?>
