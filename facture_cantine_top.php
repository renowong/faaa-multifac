<?php
require_once('checksession.php');  //already includes config.php

//###############################procedures#####################################
if (!empty($_SESSION['client'])) {
                $arCompte = getCompteDisplay();
                $arCompte = preg_split("/,/", $arCompte);
        }


//###############################variables######################################
$legend = 'Cr&eacute;ation d&apos;une facture cantine';
$deliblink = '217-2013';
$fact = buildArray("tarifs_cantines"); //details de la facture
$jsarType = buildJSArray($fact, 'status');
$jsarMontantFCP = buildJSArray($fact, 'MontantFCP');
$jsarMontantEURO = buildJSArray($fact, 'MontantEURO');
$jsarUnite = buildJSArray($fact, 'Unite');

$enf = buildArray("enfants",$arCompte[1]); //details des enfants
//echo(print_r($enf));
$jsarID_Enfant = buildJSArray($enf, 'enfantid');
$jsarNom_Enfant = buildJSArray($enf, 'nom');
$jsarPrenom_Enfant = buildJSArray($enf, 'prenom');
$jsarEcoleID_Enfant = buildJSArray($enf, 'ecole');
$jsarEcole_Enfant = buildJSArray($enf, 'nomecole');
$jsarClasse_Enfant = buildJSArray($enf, 'classe');
$jsarStatus_Enfant = buildJSArray($enf, 'status');


//#################################building forms################################
$InValidationList = buildFacturesEnAttente($arCompte[1]);
$KidsList = buildOptionsKids($enf);

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
	switch ($table){
		case "tarifs_cantines":
		$query = "SELECT * FROM `".DB."`.`status_cantine` ORDER BY idstatus";
		break;

		case "enfants":
		$query = "SELECT * FROM `".DB."`.`enfants` INNER JOIN `".DB."`.`ecoles_faaa` ON `enfants`.`ecole` = `ecoles_faaa`.`ecoleid` WHERE `enfants`.`clientid`=$clientid AND `active`=1 ORDER BY prenom";
		break;
	}
	$result = $mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
	array_push($newarray, $row);
	}
	$mysqli->close();
	return $newarray;
}

function buildOptionsType($selectedOption, $f) {
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	//$query = 'SELECT * FROM `'.DB.'`.`status_cantine` LIMIT 7,7';
	$query = 'SELECT * FROM `status_cantine` WHERE (`idstatus` BETWEEN 1 AND 7) OR (`idstatus` BETWEEN 15 AND 21)';
	$result = $mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
			echo '<option value="' .$row["idstatus"]. '">' .$row["status"]. '</option>';
	}
}

function buildOptionsKids($enf) {
	for($i=0;$i<count($enf);$i++){
		$list .= "<option value='".$enf[$i]["enfantid"]."'>".$enf[$i]["nom"]." ".$enf[$i]["prenom"]." ".$enf[$i]["classe"]." (".$enf[$i]["nomecole"].")</option>";
	}
	return $list;
}


function buildFacturesEnAttente($idclient) {
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT * FROM `".DB."`.`factures_cantine` WHERE `idclient`=$idclient AND `validation`='0'";
	$result = $mysqli->query($query);
	if($mysqli->affected_rows>0) {
		$list = "<br><b>Factures provisoires en attente de validation -- actuellement imprimable en tant que devis</b>";
		while($row = $result->fetch_array(MYSQLI_ASSOC)){
			$list .= "<br/><a href='createpdf.php?idfacture=".$row['idfacture']."&type=cantine' target='_blank'>Devis ".$row['communeid']." du ".$row['datefacture']." montant de ";
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
