<?php
require_once ('config.php');

$avoirid = $_POST['avoirid'];
$facturecode = $_POST['facturecode'];
$montant = $_POST['montant'];
$typefacture = substr($facturecode,0,4);
$factureid= substr($facturecode,4);

$restearegler = getfactureamount($factureid);
$communeid = getfacturenumber($avoirid);

updatedata($avoirid,$typefacture,$factureid,$montant,$restearegler,$communeid);


///////////////////////////functions////////////////////////////////////////////



function updatedata($avoirid,$typefacture,$factureid,$avoir,$restearegler,$communeid){

	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$difference = $restearegler - $avoir;
	if($difference<0){
		$avoirleft = $avoir - $restearegler;
		$appliedavoir = $restearegler;
		$difference = 0;
	}else{
		$avoirleft = 0;
		$appliedavoir = $avoir;
	}
	if($difference=='0'){
		$query = "UPDATE `factures_cantine` SET  `avoir` =  '$appliedavoir', `restearegler` = '$difference', `avoir_on_id` = '$communeid', `comment`=CONCAT(`comment`,' ; R&eacute;gl&eacute;e par application avoir'), `reglement` = '1' WHERE  `idfacture` = '$factureid'";
	}else{
		$query = "UPDATE `factures_cantine` SET  `avoir` =  '$appliedavoir', `restearegler` = '$difference', `avoir_on_id` = '$communeid', `comment`=CONCAT(`comment`,' ; Application avoir') WHERE  `idfacture` = '$factureid'";

	}
	$mysqli->query($query);
	
	$query = "UPDATE `avoirs` SET  `reste` =  '$avoirleft' WHERE  `idavoir` = '$avoirid'";
	$mysqli->query($query);
	$mysqli->close();
}

function getfactureamount($factureid){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT `restearegler` FROM `factures_cantine` WHERE `idfacture`='$factureid';";
	$result = $mysqli->query($query);
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $amount = $row['restearegler'];
        $mysqli->close();
        
        return $amount;
}

function getfacturenumber($avoirid){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT `communeid` FROM `factures_cantine` INNER JOIN `avoirs` ON `factures_cantine`.`idfacture`=`avoirs`.`idfacture` WHERE `idavoir`='$avoirid';";
	$result = $mysqli->query($query);
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $number = $row['communeid'];
        $mysqli->close();
        
        return $number;
}
?>