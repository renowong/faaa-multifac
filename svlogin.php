<?php
////////////////////////////////////////////////////////////////////////////////
//Logiciel : Multifac V.2.1
//Auteur : Reno Wong
//Dernière date de modification : 24/02/2009
////////////////////////////////////////////////////////////////////////////////
include_once('config.php');
header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
echo '<response>';
echo query();
echo '</response>';


function query() {
	$login = $_GET['login'];
	$password = $_GET['password'];
	$sessionid;

	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	////set the query
	$query = sprintf("SELECT * FROM `user` WHERE `user`.`userlogin`='%s' AND `user`.`userpassword`='%s' AND `user`.`userisactive`=1",
//		mysql_real_escape_string($_GET['login']),
//		mysql_real_escape_string($_GET['password']));

		$mysqli->real_escape_string($_GET['login']),
				MD5($_GET['password']));
		echo $query;

		$result = $mysqli->query($query);

		if ($result){
			//no errors
			if ($result->num_rows == 0) {
				return "<access>Echec d'authentification pour utilisateur $login</access><user><login>" . $row->userlogin . "</login>".
				"<password>" . $row->userpassword . "</password>".
				"<nom>" . $row->userlastname . "</nom>".
				"<prenom>" . $row->userfirstname . "</prenom></user>";
			} else {
				$row = $result->fetch_object();
				$sessionid = createsession($row->userid);
				updatelastlogin($row->userid);
				return "<access>OK</access><user><userid>" . $row->userid . "</userid>".
				"<login>" . $row->userlogin . "</login>".
				"<password>" . $row->userpassword . "</password>".
				"<nom>" . $row->userlastname . "</nom>".
				"<prenom>" . $row->userfirstname . "</prenom>".
				"<lastlogin>" . $row->userlastlogin . "</lastlogin>".
				"<sessionid>$sessionid</sessionid></user>";
			}
		} else {
			echo $mysqli->error;
		}

		$mysqli->close();
}

function updatelastlogin($userid){
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	////set the query
	$query = "UPDATE `user` SET `userlastlogin` = NOW( ) WHERE `user`.`userid` =" . $userid;

	$result = $mysqli->query($query);

	if (!$mysqli->affected_rows > 0) echo $mysqli->error;

	$mysqli->close();
}

function createsession($userid) {
	$sessionid = md5(date("Y-m-d H:i:s"));

	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	////set the query
	$query = "INSERT INTO `sessions` (
			`sessionid` ,
			`userid` ,
			`sessionmd5` ,
			`sessiontimestamp`
			)
			VALUES (
			NULL , '$userid', '$sessionid',
			CURRENT_TIMESTAMP
			)";

	$result = $mysqli->query($query);

	if ($mysqli->affected_rows > 0){
		return $sessionid;
	} else {
		echo $mysqli->error;
	}

	$mysqli->close();
}
?>


