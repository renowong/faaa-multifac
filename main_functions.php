<?php
require_once('config.php');

$cat = $_POST['cat'];

switch($cat){
    case "validation":
        print getinfo("SELECT * FROM `factures_cantine` WHERE `factures_cantine`.`validation` = '0'");
    break;
    case "facture":
        print getinfo("SELECT * FROM `factures_cantine` WHERE `factures_cantine`.`reglement` = '0' AND `factures_cantine`.`acceptation` = '1'");
    break;
    case "client":
        print getinfo("SELECT * FROM `clients` WHERE `clientstatus` = '1'");
    break;
    case "enfant":
        print getinfo("SELECT * FROM `enfants` WHERE `active` = '1'");
    break;
    case "saint":
        print getsaint("01/01");
    break;
}

function getinfo($query){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $result = $mysqli->query($query);
    $num_rows = mysqli_num_rows($result);
    $mysqli->close();
    
    return "{\"num\":\"$num_rows\"}";
}

function getsaint(){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $today = date("d/m");
    $query = "SELECT `Fete` FROM `saint` WHERE `JourMois`='$today'";
    $result = $mysqli->query($query);
    $result_array = array();
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            $result_array[] = $row;
	}
    $mysqli->close();
    
    return json_encode($result_array);
}

?>