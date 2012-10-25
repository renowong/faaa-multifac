<?php
require_once('config.php');

$cat = $_POST['cat'];

switch($cat){
    case "validation":
	$count = 0;
	$count += getinfo("SELECT * FROM `factures_cantine` WHERE `validation` = '0'");
	$count += getinfo("SELECT * FROM `factures_etal` WHERE `validation` = '0'");
	$count += getinfo("SELECT * FROM `factures_amarrage` WHERE `validation` = '0'");
        print "{\"num\":\"$count\"}";
    break;
    case "facture":
	$count = 0;
	$count += getinfo("SELECT * FROM `factures_cantine` WHERE `reglement` = '0' AND `acceptation` = '1'");
	$count += getinfo("SELECT * FROM `factures_etal` WHERE `reglement` = '0' AND `acceptation` = '1'");
	$count += getinfo("SELECT * FROM `factures_amarrage` WHERE `reglement` = '0' AND `acceptation` = '1'");
        print "{\"num\":\"$count\"}";
    break;
    case "client":
	$count = 0;
	$count += getinfo("SELECT * FROM `clients` WHERE `clientstatus` = '1'");

        print "{\"num\":\"$count\"}";
    break;
    case "enfant":
	$count = 0;
	$count += getinfo("SELECT * FROM `enfants` WHERE `active` = '1'");
	
        print "{\"num\":\"$count\"}";
    break;
    case "saint":
        print getsaint("01/01");
    break;
    case "rolmre_cantine":
	print getrol("rolmre_cantine");
    break;
}

function getinfo($query){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $result = $mysqli->query($query);
    $num_rows = mysqli_num_rows($result);
    $mysqli->close();
    
    return $num_rows;
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

function getrol($type){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "SELECT `from`,`to`,`filename` FROM `rol` WHERE `type`='$type' ORDER BY `idrol` DESC LIMIT 10";
    $result = $mysqli->query($query);
    $result_array = array();
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            $result_array[] = $row;
	}
    $mysqli->close();
    
    return json_encode($result_array);
}

?>