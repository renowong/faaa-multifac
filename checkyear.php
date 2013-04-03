<?php
//include_once('config.php');

$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
$query = "SELECT `chrono` FROM `chrono` WHERE `id` = (SELECT MAX(`id`) FROM `chrono`)";

$result = $mysqli->query($query);
$row = $result->fetch_row();

$chronomax = $row[0];

//print $chronomax;

$chronoyear = substr($chronomax,0,4);

$year = date("Y");

if($chronoyear!==$year){
    $query = "INSERT INTO `chrono` (`id`,`chrono`) VALUES (NULL, '".$year."00000')";
    $mysqli->query($query);
}

$mysqli->close();
?>