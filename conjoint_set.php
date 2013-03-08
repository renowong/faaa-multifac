<?php
include_once('config.php');
$id = $_GET["id"];
$conjointid = $_GET["conjointid"];

$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);

if($id!==$conjointid){
    $query = "UPDATE `clients` SET `conjointid`='0' WHERE `conjointid`='$id'";
    $mysqli->query($query);
    
    $query = "UPDATE `clients` SET `conjointid`='0' WHERE `conjointid`='$conjointid'";
    $mysqli->query($query);
    
    $query = "UPDATE `clients` SET `conjointid`='$conjointid' WHERE `clientid`='$id'";
    $mysqli->query($query);
    
    $query = "UPDATE `clients` SET `conjointid`='$id' WHERE `clientid`='$conjointid'";
    $mysqli->query($query);
    
    $mysqli->close();
}



?>