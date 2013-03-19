<?php
require_once('checksession.php');  //already includes config.php

//###############################procedures#####################################
if (!empty($_SESSION['client'])) {
                $arCompte = getCompteDisplay();
                $arCompte = preg_split("/,/", $arCompte);
        }


//###############################variables######################################
$legend = 'Cr&eacute;ation d&apos;une facture place et &eacute;tal';
$deliblink = '03-2011';
$deliblink2 = '114-2012';
$fact = buildArray(); //details de la facture
$jsarType = buildJSArray($fact, 'Type');
$jsarMontantFCP = buildJSArray($fact, 'MontantFCP');
$jsarMontantEURO = buildJSArray($fact, 'MontantEURO');
$jsarUnite = buildJSArray($fact, 'Unite');


//#################################building forms################################
$InValidationList = buildFacturesEnAttente($arCompte[1]);
//$PeriodeList = buildOptionsPeriod(0);

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

function buildArray(){
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$newarray = array();
		$query = "SELECT * FROM `".DB."`.`tarifs_etal`";
	$result = $mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
	array_push($newarray, $row);
	}
	$mysqli->close();
	return $newarray;
}

function buildOptionsType($selectedOption, $f) {
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = 'SELECT * FROM `'.DB.'`.`tarifs_etal`';
	$result = $mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		#if ($f[$i]['IDtarif'] == $selectedOption) {
		#	echo '<option value="' .$i. '" selected="selected">' . $f[$i]['Type'] . '</option>';
		#} else {
			echo '<option value="' .$row["IDtarif"]. '">' .$row["Type"]. '</option>';
	}
}


function buildFacturesEnAttente($idclient) {
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT * FROM `".DB."`.`factures_etal` WHERE `idclient`=$idclient AND `validation`=0";
	$result = $mysqli->query($query);
	if($mysqli->affected_rows>0) {
		$list = "<br><b>Factures provisoires en attente de validation -- actuellement imprimable en tant que devis</b>";
		while($row = $result->fetch_array(MYSQLI_ASSOC)){
			$list .= "<br/><a href='createpdf.php?idfacture=".$row['idfacture']."&type=etal' target='_blank'>Devis ".$row['communeid']." du ".$row['datefacture']." montant de ";
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
