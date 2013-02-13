<?php
require_once ('config.php');

$userid = $_POST['userid'];
$facturecode = $_POST['facturecode'];
$montant = $_POST['montant'];
$obs = htmlentities($_POST['obs']);
$clientid = $_POST['client'];
$typefacture = substr($facturecode,0,4);
$factureid= substr($facturecode,4);
if($typefacture=="CANT"){$enfantid = getenfantid($factureid);}

$avoirid = enterdata($clientid,$enfantid,$typefacture,$factureid,$montant,$userid,$obs);

	/*$response = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".
			"<response>".
				"<avoirid>$avoirid</avoirid>".
			"</response>";
	if(ob_get_length()) ob_clean();
	header('Content-Type: text/xml');
	
	echo $response;*/


///////////////////////////functions////////////////////////////////////////////

function getenfantid($factureid){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT `idenfant` FROM `".DB."`.`factures_cantine_details` WHERE `idfacture`='$factureid';";
	$result = $mysqli->query($query);
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $idenfant = $row['idenfant'];
        $mysqli->close();
        
        return $idenfant;
}


function enterdata($clientid,$enfantid,$typefacture,$factureid,$montant,$userid,$obs){

	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
		$query = "INSERT INTO `".DB."`.`avoirs` ".
                "(`idavoir`, `idclient`, `idenfant`, `idfacture`, `type_facture`, `validation`, `acceptation`, `montant`, `reste`, `date`, `agent_id`, `valideur_id`, `obs`)".
                "VALUES (NULL, '$clientid', '$enfantid', '$factureid', '$typefacture', '0', '0', '$montant', '$montant', CURRENT_TIMESTAMP, '$userid', '0', '$obs');";

	$mysqli->query($query);
	$lastid = $mysqli->insert_id;
	$mysqli->close();
	return $lastid;
}

?>