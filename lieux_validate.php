<?php
session_start();
//require_once ('error_handler.php');
require_once ('lieux_validate_class.php');

$validator = new Validate();

$validationType = '';
if (isset($_GET['validationType'])) {
    $validationType = $_GET['validationType'];
}

(isset($_GET['edit']) ? $edit = $_GET['edit'] : $edit=0);

if ($validationType == 'php') {
	if($validator->ValidatePHP($edit)){
		//ok
		enterdata($edit);
	} else {
		//not ok
		header("Location:lieux.php?edit=".$edit);
	}
} else {
	$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
	'<response>'.
		'<result>'.$validator->ValidateAJAX($_POST['inputValue'], $_POST['fieldID']).'</result>'.
		'<fieldid>'.$_POST['fieldID'].'</fieldid>'.
	'</response>';
	if(ob_get_length()) ob_clean();
	header('Content-Type: text/xml');
	echo $response;
}

function enterdata($edit){
	//echo "entering data";
	//$_POST['chk_status'] = (isset($_POST['chk_status']) ? 1 : 0);
	//$_POST['chk_principal'] = (isset($_POST['chk_principal']) ? 1 : 0);
		
	if($_POST['chk_principal']){
		unsetPrimaryHome($_POST['box_Proprietaire']);
	    }else{
		$primary_home_count = checkPrimaryHome($_POST['box_Proprietaire'],$edit);
		//echo $primary_home_count;
		if($primary_home_count==0) $_POST['chk_principal'] = 1;
	    }
	
	//$status='TRUE';
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	if($edit==0) { //new insert

	$query = "INSERT INTO `".DB."`.`lieux` (".
			 "`lieuid` , `lieustatus` , `lieuprincipal` ,".
			 "`lieuproprietaire` , `lieumandataire` ,".
			 "`lieulocataire` , `lieucategorie` ,".
			 "`lieunomlieu` , `lieusurface` ,".
			 "`lieunmaison` , `lieuservitude` ,".
			 "`lieuquartier` ,`lieufacturer` ,".
			 "`lieuedt` ,`lieucompteur` ,".
			 "`lieuobservations`)".
			 " VALUES (NULL, '".$_POST['chk_status']."', '".$_POST['chk_principal']."', '".$_POST['box_Proprietaire'].
			 "', '".$_POST['box_Mandataire']."', '".$_POST['box_Locataire'].
			 "', '".$_POST['box_Categorie']."', '".ucwords($_POST['txt_Nomlieu']).
			 "', '".$_POST['txt_Surface']."', '".$_POST['txt_Nmaison'].
			 "', '".$_POST['box_Servitude']."', '".$_POST['box_Quartier']."', '".$_POST['box_Facturer'].
			 "', '".strtoupper($_POST['txt_Compteur'])."', '".strtoupper($_POST['txt_EDT'])."', '".$_POST['txt_Observations']."')";
	} else { //update
		$query = "UPDATE `".DB."`.`lieux` SET `lieustatus`='".$_POST['chk_status']."', `lieuprincipal`='".$_POST['chk_principal'].
				 "', `lieuproprietaire`='".$_POST['box_Proprietaire']."', `lieumandataire`='".$_POST['box_Mandataire'].
				 "', `lieulocataire`='".$_POST['box_Locataire']."', `lieucategorie`='".$_POST['box_Categorie'].
				 "',`lieunomlieu`='".ucwords($_POST['txt_Nomlieu']).
				 "', `lieusurface`='".$_POST['txt_Surface']."', `lieunmaison`='".$_POST['txt_Nmaison'].
				 "', `lieuservitude`='".$_POST['box_Servitude']."', `lieuquartier`='".$_POST['box_Quartier'].
				 "', `lieufacturer`='".$_POST['box_Facturer']."', `lieucompteur`='".strtoupper($_POST['txt_Compteur']).
				 "', `lieuedt`='".strtoupper($_POST['txt_EDT'])."', `lieuobservations`='".$_POST['txt_Observations']."'".
				 " WHERE `lieuid`='". $edit."'";
	}
	//echo $query;
	$mysqli->query($query);
	$affectedrows = $mysqli->affected_rows;
	$mysqli->close();
	
	if ($affectedrows > 0){
	reinitialize();
	header("Location:lieux.php?edit=$edit&success=1");
	} else {
	header("Location:lieux.php?edit=$edit&success=0");
	}
	
}

function checkPrimaryHome($clientid,$currentedit){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "SELECT * FROM `lieux` WHERE `lieuproprietaire`='$clientid' AND `lieuprincipal`='1' AND NOT `lieuid`='$currentedit'";
    $result = $mysqli->query($query);
    $count = $result->num_rows;
    $mysqli->close();
    return $count;
}

function unsetPrimaryHome($clientid){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "UPDATE `lieux` SET `lieuprincipal`='0' WHERE `lieuproprietaire`='$clientid'";
    $mysqli->query($query);
    $mysqli->close();
}

function reinitialize(){
	$_SESSION['values']['chk_status'] = '';
	$_SESSION['values']['box_Proprietaire'] = '';
	$_SESSION['values']['box_Mandataire'] = '';
	$_SESSION['values']['box_Locataire'] = '';
	$_SESSION['values']['txt_Categorie'] = '';
	$_SESSION['values']['txt_Nomlieu'] = '';
	$_SESSION['values']['txt_Surface'] = '';
	$_SESSION['values']['txt_Nmaison'] = '';
	$_SESSION['values']['txt_Servitude'] = '';
	$_SESSION['values']['txt_Quartier'] = '';
	$_SESSION['values']['txt_Facturer'] = '';
	$_SESSION['values']['txt_EDT'] = '';
	$_SESSION['values']['txt_Compteur'] = '';
	$_SESSION['values']['txt_Observations'] = '';
}

?>
