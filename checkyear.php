<?php
//include_once('config.php');

$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
$query = "SELECT MAX(`id`), `chrono` FROM `chrono`";

$result = $mysqli->query($query);
$row = $result->fetch_row();

$chronomax = $row[0];

$chronoyear = substr($chronomax,0,4);

$year = date("Y");

if($chronoyear!==$year){
    $query = "INSERT INTO `chrono` (`id`,`chrono`) VALUES (NULL, '".$year."00000')";
    $mysqli->query($query);
}

$mysqli->close();
?>