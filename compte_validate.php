<?php
session_start();
//require_once ('error_handler.php');
require_once ('compte_validate_class.php');

$validator = new Validate();
$validationType = '';

if (isset($_GET['validationType'])) {
	$validationType = $_GET['validationType'];
}

if ($validationType == 'php') {
	if($validator->ValidatePHP()){
		//ok
		enterdata();
	} else {
		//not ok
		header("Location:compte.php");
	}
} else {
	$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
	'	<response>'.
	'		<result>'.$validator->ValidateAJAX($_POST['inputValue'], $_POST['fieldID']).'</result>'.
	'		<fieldid>'.$_POST['fieldID'].'</fieldid>'.
	'	</response>';
	if(ob_get_length()) ob_clean();
	header('Content-Type: text/xml');
	echo $response;

}

function enterdata(){
	($_POST['txt_Password']=='existing' ? $qPassword = "" : $qPassword = ", `userpassword`='".MD5($_POST['txt_Password'])."'");
	$_POST['chk_Admin'] = (isset($_POST['chk_Admin']) ? 1 : 0);
	$_POST['chk_Active'] = (isset($_POST['chk_Active']) ? 1 : 0);
    $_POST['chk_Validator'] = (isset($_POST['chk_Validator']) ? 1 : 0);
	$userid = $_POST['userid'];

	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);

	if ($userid > 0){
		$query = "UPDATE `".DB."`.`user` SET `userfirstname`='".$_POST['txt_Prenom']."', `userlastname`='".$_POST['txt_Nom'].
				"', `userlogin`='".strtolower($_POST['txt_Login'])."', `userservice`='".strtoupper($_POST['box_Service']).
				"'".$qPassword.", `userisadmin`=".$_POST['chk_Admin'].", `userisactive`=".$_POST['chk_Active'].", `userisvalidator`=".$_POST['chk_Validator'].
				" WHERE `userid`=".$_POST['userid'];
		$update = 1;
	} else {
		$query = "INSERT INTO `".DB."`.`user` (`userfirstname`, `userlastname`, `userlogin`, `userservice`, `userpassword`, `userisadmin`, `userisactive`, `userisvalidator`)".
				" VALUES ('".$_POST['txt_Prenom']."', '".$_POST['txt_Nom']."', '".strtolower($_POST['txt_Login']).
				"', '".strtoupper($_POST['box_Service'])."', '".MD5($_POST['txt_Password'])."', ".$_POST['chk_Admin'].", ".$_POST['chk_Active'].", ".$_POST['chk_Validator'].")";
		$update = 0;
		$userid = getlastuser();
	}

	//echo $query;
	$Mysqli->query($query);
	if ($Mysqli->affected_rows > 0){
		header("Location:compte.php?success=1&update=".$update."&modif=".$userid);
	} else {
		header("Location:compte.php?success=0&update=".$update."&modif=".$userid);
	}

}

function getlastuser() {
	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT MAX(`user`.`userid`) FROM `".DB."`.`user`";
	$result = $Mysqli->query($query);
	$row = $result->fetch_row();
	return $row[0];
}

?>
