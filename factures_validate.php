<?php
require_once ('config.php');
$type = $_POST['type'];
$factureid = $_POST['factureid'];
$acceptation = $_POST['acceptation'];
$comment = $_POST['comment'];
$date =  date("Y-m-d");

$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
switch($type){
    case "cantine":
    case "repas":   
        $query = "UPDATE `".DB."`.`factures_cantine` SET `validation`='1', `acceptation`= '$acceptation', `comment`='$comment', `date_validation`='$date' WHERE `factures_cantine`.`idfacture`=$factureid";
    break;
    case "etal":
        $query = "UPDATE `".DB."`.`factures_etal` SET `validation`='1', `acceptation`= '$acceptation', `comment`='$comment', `date_validation`='$date' WHERE `factures_etal`.`idfacture`=$factureid";
    break;
}
$mysqli->query($query);
$mysqli->close();

print $query;
?>
