<?php
require_once('checksession.php');  //already includes config.php

//###############################procedures#####################################
if (!empty($_SESSION['client'])) {
                $arCompte = getCompteDisplay();
                $arCompte = preg_split("/,/", $arCompte);
        }


//###############################variables######################################
$legend = 'Cr&eacute;ation d&apos;une facture d&apos;amarrage';
$deliblink = '46-2011';
$deliblink2 = '83-2010';
$fact = buildArray(); //details de la facture
$jsarType = buildJSArray($fact, 'Type');
$jsarMontantFCP = buildJSArray($fact, 'MontantFCP');
$jsarMontantEURO = buildJSArray($fact, 'MontantEURO');
$jsarUnite = buildJSArray($fact, 'Unite');


//#################################building forms################################
$InValidationList = buildFacturesEnAttente($arCompte[1]);
$PeriodeList = buildOptionsPeriod();

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
		$query = "SELECT * FROM `".DB."`.`tarifs_amarrage`";
	$result = $mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
	array_push($newarray, $row);
	}
	$mysqli->close();
	return $newarray;
}

function buildOptionsType($selectedOption, $f) {
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = 'SELECT * FROM `'.DB.'`.`tarifs_amarrage`';
	$result = $mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		#if ($f[$i]['IDtarif'] == $selectedOption) {
		#	echo '<option value="' .$i. '" selected="selected">' . $f[$i]['Type'] . '</option>';
		#} else {
			echo '<option value="' .$row["IDtarif"]. '">' .$row["Type"]. '</option>';
	}
}

function buildOptionsPeriod() {
    $year = date("Y");
    $thismonth = date("n");
    $lastyear = $year-1;
    $nextyear = $year+1;
	$months = array("Janvier","F&eacute;vrier","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","D&eacute;cembre");
	for($i=0;$i<count($months);$i++){
		$list .= "<option value='".htmlentities($months[$i])." ".$lastyear."'>".$months[$i]." ".$lastyear."</option>";
	}
    for($i=0;$i<count($months);$i++){
        if(($i+1)==$thismonth){$s=" SELECTED";}else{$s="";}
		$list .= "<option value='".htmlentities($months[$i])." ".$year."'$s>".$months[$i]." ".$year."</option>";
	}
    for($i=0;$i<count($months);$i++){
		$list .= "<option value='".htmlentities($months[$i])." ".$nextyear."'>".$months[$i]." ".$nextyear."</option>";
	}
	return $list;
}

function buildFacturesEnAttente($idclient) {
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT * FROM `".DB."`.`factures_amarrage` WHERE `idclient`=$idclient AND `validation`=0";
	$result = $mysqli->query($query);
	if($mysqli->affected_rows>0) {
		$list = "<br><b>Factures en attente de validation -- actuellement imprimable en tant que devis</b>";
		while($row = $result->fetch_array(MYSQLI_ASSOC)){
			$list .= "<br/><a href='createpdf.php?idfacture=".$row['idfacture']."&type=amarrage' target='_blank'>Devis ".$row['communeid']." du ".$row['datefacture']." montant de ";
			$list .= trispace($row['montantfcp']);
			$list .=" FCP (soit ".$row['montanteuro']." euros)</a>";
		}
	}else{
		$list = "";
	}
	$mysqli->close();
	return $list;
}

function getCompteType(){
	$dom = new domDocument;
	$arr = array(utf8_decode('é') => "\\351", utf8_decode('è') => "\\350",  utf8_decode('ç') => "\\347", utf8_decode('à') => "\\340", utf8_decode('ù') => "\\371");
	$clientdata = strtr($_SESSION['client'], $arr);
	$dom->loadXML($clientdata);
	if (!$dom) {
		echo "Error while parsing the document\n";
		exit;
	}
	$s = simplexml_import_dom($dom);

		if($s->type=="client"){
			return "C";	
		}else{
			return "M";	
		}

}

?>
