<?php
require_once('checksession.php');

if (!empty($_SESSION['client'])) {
                $arCompte = getCompteDisplay();
                $arCompte = preg_split("/,/", $arCompte);
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

?>