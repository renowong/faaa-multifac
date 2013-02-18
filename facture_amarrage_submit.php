<?php
session_start();
//require_once ('error_handler.php');
require_once ('config.php');
require_once ('chrono.php'); //chrono update
$rawdata = $_GET['fdata'];
$fdata = explode("$",$rawdata);
$clientid = $_GET['clientid'];
$period = $_GET['period'];
$py = $_GET['py'];
$edt = $_GET['edt'];
$eau = $_GET['eau'];
$lieu = strtoupper($_GET['lieu']);
$nav = strtoupper($_GET['nav']);
$comptetype = $_GET['comptetype'];

$communefactureid = enterdata($fdata,$clientid,$period,$py,$lieu,$nav,$edt,$eau,$comptetype);

	$response = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".
			"<response>".
				"<facturetempid>$communefactureid</facturetempid>".
			"</response>";
	if(ob_get_length()) ob_clean();
	header('Content-Type: text/xml');
	
	echo $response;


///////////////////////////functions////////////////////////////////////////////

//////duplicate at ecole_global_search_function.php///////////
function enterdata($fdata,$clientid,$period,$py,$lieu,$nav,$edt,$eau,$comptetype){
$totalfcp = 0;
for($counter=0;$counter<count($fdata);$counter+=1){
	$detail = explode("#",$fdata[$counter]);
	$totalfcp += ($detail[1]*$detail[2]);
}
$totalfcp += $edt;
$totalfcp += $eau;
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
	
		$query = "INSERT INTO `".DB."`.`factures_amarrage` (`idfacture`, `idclient`, `type_client`,".
				 " `datefacture`, `communeid`, `montantfcp`, `montanteuro`, `restearegler`,`obs`,`PY`,`lieu`,`navire`,`edt`,`eau`)".
				 " VALUES (NULL, '".$clientid."', '".$comptetype."', '".$today."', '".$chrono."', '".$totalfcp."', '".$totaleuro."', '".$totalfcp."', '".$period."', '".$py."', '".$lieu."', '".$nav."', '".$edt."', '".$eau."')";
	//return $query;
	$mysqli->query($query);
	$lastid = $mysqli->insert_id; //use it to insert the details.

// insert details now
	for($counter=0;$counter<count($fdata);$counter+=1){
		$detail = explode("#",$fdata[$counter]);
		$query = "INSERT INTO `".DB."`.`factures_amarrage_details` (`iddetail`, `idfacture`,".
				" `idtarif`, `quant`)".
				" VALUES (NULL, '".$lastid."', '".$detail[0]."', '".$detail[1]."')";
		$mysqli->query($query);
	}

	$mysqli->close();

	return $lastid;
}

?>
