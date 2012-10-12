<?php
require_once('config.php');

$type = $_POST['type'];
$from = $_POST['from'];
$to = $_POST['to'];

if($to>0){
    switch($type){
        case "factures":
            $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
            $query = "UPDATE `factures_cantine` SET `idclient`='$to'  WHERE `factures_cantine`.`reglement` = 0 AND `factures_cantine`.`acceptation` = 1 AND `factures_cantine`.`idclient` = '$from'";
            $result = $mysqli->query($query);
            $mysqli->close();
        break;
        case "kid":
            $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
            $query = "UPDATE `enfants` SET `clientid`='$to' WHERE `clientid` = '$from'";
            $result = $mysqli->query($query);
            $mysqli->close();
        break;
        
    }
}



print $query;
?>