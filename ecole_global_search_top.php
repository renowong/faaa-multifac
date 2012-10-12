<?php
require_once('checksession.php');

if (!empty($_SESSION['client'])) {
                $arCompte = getCompteDisplay();
                $arCompte = preg_split("/,/", $arCompte);
        }

function buildOptionsClasses(){
	$newarray = array();
	$returnarray = array();
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = 'SELECT `idecole`,`classe` FROM `'.DB.'`.`classes` ORDER BY `classe`';
	$result = $mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$newarray[$row['idecole']] = $newarray[$row['idecole']].$row['classe'].',';
	}
	$mysqli->close();
	foreach($newarray as $key => $value){
		$returnarray[$key] = substr($newarray[$key],0,-1);
	}
	return $returnarray;
}

function buildOptionsSchools(){
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = 'SELECT * FROM `'.DB.'`.`ecoles_faaa` ORDER BY nomecole';
	$result = $mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		$list .= "<option value='".$row["ecoleid"]."'>".$row["nomecole"]."</option>";
	}
	$mysqli->close();
	return $list;
}

function buildOptionsPeriod() {
    $year = date("Y");
    $thismonth = date("n");
    $lastyear = $year-1;
    $nextyear = $year+1;
	$months = array("Janvier","F&eacute;vrier","Mars","Avril","Mai","Juin","Juillet","Ao&ucirc;t","Septembre","Octobre","Novembre","D&eacute;cembre");
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

?>