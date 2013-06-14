<?php
session_start();
//require_once ('error_handler.php');
require_once ('config.php');
require_once ('chrono.php'); //chrono update
$rawdata = $_GET['fdata'];
$fdata = explode("$",$rawdata);
$clientid = $_GET['clientid'];
$period = $_GET['period'];
$communefactureid = enterdata($fdata,$clientid,$period);

	$response = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".
			"<response>".
				"<facturetempid>$communefactureid</facturetempid>".
			"</response>";
	if(ob_get_length()) ob_clean();
	header('Content-Type: text/xml');
	
	echo $response;


///////////////////////////functions////////////////////////////////////////////

//////duplicate at ecole_global_search_function.php///////////
function enterdata($fdata,$clientid,$period){
$totalfcp = 0;
for($counter=0;$counter<count($fdata);$counter+=1){
	$detail = explode("#",$fdata[$counter]);
	$totalfcp += ($detail[1]*$detail[2]);
}
$totaleuro = $totalfcp/120;
$totaleuro = round($totaleuro, 2);
$today = date("Y-m-d");

	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
		$query = "SELECT `chrono` FROM `".DB."`.`chrono` ORDER BY `id` DESC LIMIT 1;";
		$result = $mysqli->query($query);
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$chrono = ($row["chrono"])+1;
		
		$query = "INSERT INTO `".DB."`.`chrono` (`chrono`) VALUES ('".$chrono."')";
		$mysqli->query($query);
	
		$query = "INSERT INTO `".DB."`.`factures_cantine` (`idfacture`, `idclient`,".
				 " `datefacture`, `communeid`, `montantfcp`, `montanteuro`, `restearegler`,`obs`)".
				 " VALUES (NULL, '".$clientid."', '".$today."', '".$chrono."', '".$totalfcp."', '".$totaleuro."', '".$totalfcp."', '".$period."')";
	//return $query;
	$mysqli->query($query);
	$lastid = $mysqli->insert_id; //use it to insert the details.

// insert details now
	for($counter=0;$counter<count($fdata);$counter+=1){
		$detail = explode("#",$fdata[$counter]);
		$query = "INSERT INTO `".DB."`.`factures_cantine_details` (`iddetail`, `idfacture`,".
				" `idtarif`, `quant`, `idenfant`)".
				" VALUES (NULL, '".$lastid."', '".$detail[0]."', '".$detail[1]."', '".$detail[3]."')";
		$mysqli->query($query);
                
                //if($detail[0]=='1'||$detail[0]=='2'||$detail[0]=='3'||$detail[0]=='4'||$detail[0]=='15'||$detail[0]=='16'||$detail[0]=='17'||$detail[0]=='18'){
                //    activate_bourse($lastid);
                //}
		// more elegant solution....
		if(check_ifbourse($detail[0])) activate_bourse($lastid);
                
	}

	$mysqli->close();

	return $lastid;
}

function activate_bourse($idfacture){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "UPDATE `factures_cantine` SET `bourse`='1' WHERE `idfacture`='$idfacture'";
    $mysqli->query($query);
    $mysqli->close();
}

function check_ifbourse($idstatus){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "SELECT `valeur` FROM `status_cantine` WHERE `idstatus`='$idstatus'";
    $result = $mysqli->query($query);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $value = $row["valeur"];
    $mysqli->close();
    
    if($value>0){return true;}else{return false;}
}

?>
