<?php
////////////////////////////////////////////////////////////////////////////////
//Page : mandataires_validate.php
//Auteur : Reno Wong
////////////////////////////////////////////////////////////////////////////////

session_start();
//require_once ('error_handler.php');
require_once ('mandataires_validate_class.php');

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
		header("Location:mandataires.php?edit=".$edit);
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
	$nomvalue = strtoupper($_POST['txt_Nom']);
	$prenomvalue = strtoupper($_POST['txt_Prenom']);
	$prenomvalue = strtr($prenomvalue,utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
	$_POST['chk_status'] = (isset($_POST['chk_status']) ? 1 : 0);
	($_POST['txt_Notahiti'] == '' ? $notahiti='NULL' : $notahiti=$_POST['txt_Notahiti']);

	//$status='TRUE';
	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	if($edit==0) { //new insert
	$query = "INSERT INTO `".DB."`.`mandataires` (`mandataireid`,".
			 " `mandataireprefix`, `mandataireRS`,".
			 " `mandatairestatus`, `mandatairenom`,".
			 " `mandataireprenom`, `mandataireidtresor`, `mandataireemail`, `mandatairenotahiti`, `mandataireRC`,".
			 " `mandatairetelephone`,`mandatairetelephone2`, `mandatairefax`, `mandatairebp`,".
			 " `mandatairecp`, `mandataireville`, `mandatairecommune`, `mandatairepays`, `aroa`, `quartier`, `mandatairerib`,`obs`)".
			 " VALUES (NULL, '".$_POST['box_Prefix']."', '".strtoupper($_POST['txt_RS']).
		 	 "', '".$_POST['chk_status']."', '".strtoupper($_POST['txt_Nom'])."', '".strtoupper($_POST['txt_Prenom']).
			 "', '".$_POST['txt_IDTresor']."', '".strtolower($_POST['txt_Email']).
			 "', '".$notahiti."', '".$_POST['txt_RC'].
			 "', '".$_POST['txt_Telephone']."', '".$_POST['txt_Telephone2'].
			 "', '".$_POST['txt_Fax']."', '".$_POST['txt_BP'].
			 "', '".$_POST['txt_CP']."', '".addslashes(strtoupper($_POST['txt_Ville'])).
			 "', '".addslashes(strtoupper($_POST['txt_Commune']))."', '".strtoupper($_POST['txt_Pays']).
                         "', '".addslashes(strtoupper($_POST['txt_Commune']))."', '".addslashes(strtoupper($_POST['txt_Pays'])).
                         "', '".$_POST['txt_RIB']."', '".addslashes($_POST['txt_obs'])."')";
			//echo $query;
			$Mysqli->query($query);
			$lastid = $Mysqli->insert_id;
			if ($Mysqli->affected_rows > 0){
				reinitialize();
				header("Location:mandataires.php?edit=$lastid&success=1");
			} else {
				header("Location:mandataires.php?edit=$edit&success=0");
			}
	} else { //update
				$query = "UPDATE `".DB."`.`mandataires` SET `mandatairestatus`='".$_POST['chk_status'].
			 "', `mandataireprefix`='".$_POST['box_Prefix']."', `mandataireRS`='".strtoupper($_POST['txt_RS']).
			 "', `mandatairenom`='".strtoupper($_POST['txt_Nom'])."', `mandataireprenom`='".strtoupper($_POST['txt_Prenom']).
			 "', `mandataireidtresor`='".$_POST['txt_IDTresor']."', `mandataireemail`='".strtolower($_POST['txt_Email']).
			 "', `mandatairenotahiti`='".$notahiti."', `mandataireRC`='".$_POST['txt_RC'].
			 "', `mandatairetelephone`='".$_POST['txt_Telephone']."', `mandatairetelephone2`='".$_POST['txt_Telephone2'].
			 "', `mandatairefax`='".$_POST['txt_Fax']."', `mandatairebp`='".$_POST['txt_BP'].
			 "', `mandatairecp`='".$_POST['txt_CP']."', `mandataireville`='".addslashes(strtoupper($_POST['txt_Ville'])).
			 "', `mandatairecommune`='".addslashes(strtoupper($_POST['txt_Commune']))."', `mandatairepays`='".strtoupper($_POST['txt_Pays']).
                         "', `aroa`='".addslashes(strtoupper($_POST['txt_Aroa']))."', `quartier`='".addslashes(strtoupper($_POST['txt_Quartier'])).
                         "', `mandatairerib`='".$_POST['txt_RIB']."', `obs`='".addslashes($_POST['txt_obs'])."'".
			 " WHERE `mandataireid`=". $edit;
			//echo $query;
			$Mysqli->query($query);
			if ($Mysqli->affected_rows > 0){
				reinitialize();
				header("Location:mandataires.php?edit=$edit&success=1");
			} else {
				header("Location:mandataires.php?edit=$edit&success=0");
			}			 
	}

}

function reinitialize(){
	$_SESSION['values']['chk_status'] = '';
	$_SESSION['values']['box_Prefix'] = '';
	$_SESSION['values']['txt_RS'] = '';
	$_SESSION['values']['txt_Nom'] = '';
	$_SESSION['values']['txt_Prenom'] = '';
	$_SESSION['values']['txt_Prenom2'] = '';
	$_SESSION['values']['txt_IDTresor'] = '';
	$_SESSION['values']['txt_Email'] = '';
	$_SESSION['values']['txt_Notahiti'] = '';
	$_SESSION['values']['txt_RC'] = '';
	$_SESSION['values']['txt_Telephone'] = '';
	$_SESSION['values']['txt_Telephone2'] = '';
	$_SESSION['values']['txt_Fax'] = '';
	$_SESSION['values']['txt_BP'] = '';
	$_SESSION['values']['txt_CP'] = '';
	$_SESSION['values']['txt_Ville'] = '';
	$_SESSION['values']['txt_Commune'] = '';
	$_SESSION['values']['txt_Pays'] = '';
	$_SESSION['values']['txt_RIB'] = '';
}

?>
