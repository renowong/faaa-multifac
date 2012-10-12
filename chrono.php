<?php
////////////////////////////////////////////////////////////////////////////////
//Page : chrono.php
//Auteur : Reno Wong
//NOTE : This file needs config.php to be included in parent file.:w
////////////////////////////////////////////////////////////////////////////////


function getchrono(){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $result = $mysqli->query("SELECT MAX(`chrono`) FROM `".DB."`.`chrono`");
        $row = $result->fetch_row();
        $chrono = $row[0];

	$mysqli->close();

        $newchrono = updatechrono($chrono);

        return $newchrono;
}

function updatechrono($currentchrono){
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$chronoyear = substr($currentchrono, 0, 4);
	$thisyear = date("Y");

	if($chronoyear===$thisyear){
		$newchrono = $currentchrono+1;
		$mysqli->query("UPDATE `".DB."`.`chrono` SET `chrono`=$newchrono WHERE `chrono`=$currentchrono");
	}else{
		$newchrono = $thisyear."00001";
		$mysqli->query("INSERT INTO `".DB."`.`chrono` (`chrono`) VALUES ($newchrono)");

	}
	$mysqli->close();
	return $newchrono;
}
?>
