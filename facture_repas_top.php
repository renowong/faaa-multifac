<?php
require_once('checksession.php');  //already includes config.php

//###############################procedures#####################################
if (!empty($_SESSION['client'])) {
                $arCompte = getCompteDisplay();
                $arCompte = preg_split("/,/", $arCompte);
        }


//###############################variables######################################
$legend = 'Cr&eacute;ation d&apos;une facture repas';
$deliblink = '160-2012';
$fact = buildArray("tarifs_cantines"); //details de la facture
$jsarType = buildJSArray($fact, 'status');
$jsarMontantFCP = buildJSArray($fact, 'MontantFCP');
$jsarMontantEURO = buildJSArray($fact, 'MontantEURO');
$jsarUnite = buildJSArray($fact, 'Unite');


//#################################building forms################################
$InValidationList = buildFacturesEnAttente($arCompte[1]);


//#################################functions#####################################

function buildJSArray($f,$col){
	$i=0;
	$jsar='';
	while($i < count($f)){
		$jsar .= '"'.$f[$i][$col].'",';
		$i++;
	}
	$jsar = substr($jsar, 0, strlen($jsar)-1);
	return $jsar;
}

function buildArray($table,$clientid=0){
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$newarray = array();
		$query = "SELECT * FROM `".DB."`.`status_cantine` ORDER BY idstatus";
	$result = $mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
	array_push($newarray, $row);
	}
	$mysqli->close();
	return $newarray;
}

function buildOptionsType($selectedOption, $f) {
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = 'SELECT * FROM `'.DB.'`.`status_cantine` ORDER BY `idstatus` DESC LIMIT 1';
	$result = $mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
			echo '<option value="' .$row["idstatus"]. '">' .$row["status"]. '</option>';
	}
}

function buildFacturesEnAttente($idclient) {
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT * FROM `".DB."`.`factures_cantine` WHERE `idclient`=$idclient AND `validation`='0' AND `repas`='1'";
	$result = $mysqli->query($query);
	if($mysqli->affected_rows>0) {
		$list = "<br><b>Factures en attente de validation -- actuellement imprimable en tant que devis</b>";
		while($row = $result->fetch_array(MYSQLI_ASSOC)){
			$list .= "<br/><a href='createpdf.php?idfacture=".$row['idfacture']."&type=repas' target='_blank'>Devis ".$row['communeid']." du ".$row['datefacture']." montant de ";
			$list .= trispace($row['montantfcp']);
			$list .=" FCP (soit ".$row['montanteuro']." euros)</a>";
		}
	}else{
		$list = "";
	}
	$mysqli->close();
	return $list;
}

?>
