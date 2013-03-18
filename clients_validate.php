<?php
session_start();
//require_once ('error_handler.php');
require_once ('clients_validate_class.php');

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
	header("Location:clients.php?edit=".$edit);
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
	$nomvalue = strtoupper($_POST['txt_Nom']);
	$nomvalue = strtr($nomvalue,utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),'AAAAACEEEEIIIINOOOOOUUUUYAAAAACEEEEIIIINOOOOOUUUUY');
	$nomvalue = str_replace(' ','',$nomvalue);
	$nomvalue = str_replace('-','',$nomvalue);
	$prenomvalue = strtoupper($_POST['txt_Prenom']);
	$prenomvalue = strtr($prenomvalue,utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),'AAAAACEEEEIIIINOOOOOUUUUYAAAAACEEEEIIIINOOOOOUUUUY');
	$prenomvalue = str_replace(' ','',$prenomvalue);
	$prenomvalue = str_replace('-','',$prenomvalue);
	$dnvalue = $_POST['txt_DateNaissance'];
	$nomvalue .= "000";
	$nomvalue = substr($nomvalue, 0, 3);
	$prenomvalue .= "0000000";
	$prenomvalue = substr($prenomvalue, 0, 7);
	$date = explode("/", $dnvalue);
	$generatedcode = $date[2].$date[1].$date[0].$nomvalue.$prenomvalue;
	$dnvalue = $date[2]."-".$date[1]."-".$date[0];
	$_POST['chk_status'] = (isset($_POST['chk_status']) ? 1 : 0);

	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	if($edit==0) { //new insert
		$query = "INSERT INTO `".DB."`.`clients` (`clientid`, `clientcode`,".
				 " `clientstatus`, `clientcivilite`, `clientnom`, `clientnommarital`,".
				 " `clientprenom`, `clientprenom2`, `clientdatenaissance`,".
				 " `clientlieunaissance`, `clientidtresor`, `clientemail`,".
				 " `clientcps`, `clienttelephone`, `clientfax`, `clientbp`,".
				 " `clientcp`, `clientville`, `clientcommune`, `clientpays`, `aroa`, `quartier`, `clientrib`, `obs`)".
				 " VALUES (NULL, '".$generatedcode."', '".$_POST['chk_status']."', '".$_POST['box_Civilite'].
				 "', '".strtoupper($_POST['txt_Nom'])."', '".strtoupper($_POST['txt_NomMarital']).
				 "', '".strtoupper($_POST['txt_Prenom'])."', '".strtoupper($_POST['txt_Prenom2']).
				 "', '".$dnvalue."', '".strtoupper($_POST['txt_LieuNaissance']).
				 "', '".$_POST['txt_IDTresor']."', '".strtolower($_POST['txt_Email']).
				 "', '".$_POST['txt_CPS']."', '".$_POST['txt_Telephone'].
				 "', '".$_POST['txt_Fax']."', '".$_POST['txt_BP'].
				 "', '".$_POST['txt_CP']."', '".addslashes(strtoupper($_POST['txt_Ville'])).
				 "', '".addslashes(strtoupper($_POST['txt_Commune']))."', '".strtoupper($_POST['txt_Pays']).
				 "', '".addslashes(strtoupper($_POST['txt_Aroa']))."', '".addslashes(strtoupper($_POST['txt_Quartier'])).
				 "', '".$_POST['txt_RIB']."', '".addslashes($_POST['txt_obs'])."');";
	$Mysqli->query($query);
	$lastid = $Mysqli->insert_id;
	//print $query;
	if ($Mysqli->affected_rows > 0){
		reinitialize();
		header("Location:clients.php?edit=$lastid&success=1&reset=1");
	} else {
		header("Location:clients.php?edit=$edit&success=0");
	}
	
	} else { //update
		$query = "UPDATE `".DB."`.`clients` SET `clientcode`='".$generatedcode."',".
				 " `clientstatus`='".$_POST['chk_status']."', `clientcivilite`='".$_POST['box_Civilite'].
				 "', `clientnom`='".strtoupper($_POST['txt_Nom'])."', `clientnommarital`='".strtoupper($_POST['txt_NomMarital']).
				 "', `clientprenom`='".htmlentities(strtoupper($_POST['txt_Prenom']), ENT_QUOTES, 'UTF-8')."', `clientprenom2`='".strtoupper($_POST['txt_Prenom2']).
				 "', `clientdatenaissance`='".$dnvalue."',".
				 " `clientlieunaissance`='".strtoupper($_POST['txt_LieuNaissance']).
				 "', `clientidtresor`='".$_POST['txt_IDTresor']."', `clientemail`='".strtolower($_POST['txt_Email']).
				 "', `clientcps`='".$_POST['txt_CPS']."', `clienttelephone`='".$_POST['txt_Telephone'].
				 "', `clientfax`='".$_POST['txt_Fax']."', `clientbp`='".$_POST['txt_BP'].
				 "', `clientcp`='".$_POST['txt_CP']."', `clientville`='".addslashes(strtoupper($_POST['txt_Ville'])).
				 "', `clientcommune`='".addslashes(strtoupper($_POST['txt_Commune']))."', `clientpays`='".strtoupper($_POST['txt_Pays']).
				 "', `aroa`='".addslashes(strtoupper($_POST['txt_Aroa']))."', `quartier`='".addslashes(strtoupper($_POST['txt_Quartier'])).
				 "', `clientrib`='".$_POST['txt_RIB']."', `obs`='".addslashes($_POST['txt_obs'])."'".
				 " WHERE `clientid`=". $edit;
	$Mysqli->query($query);
	
	//if ($Mysqli->affected_rows > 0){
		reinitialize();
		header("Location:clients.php?edit=$edit&success=1&reset=1");
	//} else {
	//	header("Location:clients.php?edit=$edit&success=0");
	//}
	}

}

function reinitialize(){
	$_SESSION['values']['chk_status'] = '';
	$_SESSION['values']['box_Civilite'] = '';
	$_SESSION['values']['txt_Nom'] = '';
	$_SESSION['values']['txt_NomMarital'] = '';
	$_SESSION['values']['txt_Prenom'] = '';
	$_SESSION['values']['txt_Prenom2'] = '';
	$_SESSION['values']['txt_DateNaissance'] = '';
	$_SESSION['values']['txt_LieuNaissance'] = '';
	$_SESSION['values']['txt_IDTresor'] = '';
	$_SESSION['values']['txt_Email'] = '';
	$_SESSION['values']['txt_CPS'] = '';
	$_SESSION['values']['txt_Telephone'] = '';
	$_SESSION['values']['txt_Fax'] = '';
	$_SESSION['values']['txt_BP'] = '';
	$_SESSION['values']['txt_CP'] = '';
	$_SESSION['values']['txt_Ville'] = '';
	$_SESSION['values']['txt_Commune'] = '';
	$_SESSION['values']['txt_Pays'] = '';
	$_SESSION['values']['txt_Aroa'] = '';
	$_SESSION['values']['txt_Quartier'] = '';
	$_SESSION['values']['txt_enfantNom'] = '';
	$_SESSION['values']['txt_enfantPrenom'] = '';
	$_SESSION['values']['txt_enfantDN'] = '';
	$_SESSION['values']['txt_enfantCPS'] = '';
	$_SESSION['values']['slt_enfantStatus'] = '';
	$_SESSION['values']['slt_enfantEcole'] = '';
	$_SESSION['values']['slt_enfantClasse'] = '';
	$_SESSION['values']['slt_enfantSexe'] = '';
	//$_SESSION['values']['chk_enfantDest'] = '';
}
?>
