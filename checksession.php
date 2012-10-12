<?php
session_start();
include_once('config.php');
require_once('checksession_class.php');

//check session
if (isset($_GET['sessionid'])) {
	//echo checkalive($_GET['sessionid']);
	if (checkalive($_GET['sessionid'], $_GET['userid'])) {
		$_SESSION['sessionid'] = $_GET['sessionid'];
	} else {
		$_SESSION['sessionid'] = "";
	}
}

$sessionid = $_SESSION['sessionid'];
if ($sessionid=="") header("Location:index.php?expired=1");

//check userid
if (isset($_GET['userid'])) getuserdata($_GET['userid']);

//current account being modified
//$account = "test test test";


function checkalive($id, $user) {
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	////set the query
	$query = "SELECT UNIX_TIMESTAMP(`sessions`.`sessiontimestamp`) AS `sessiontimestamp` FROM `sessions` WHERE `sessions`.`sessionmd5` = '" . $id . "' AND `sessions`.`userid` = " . $user;
//echo $query;
	$result = $mysqli->query($query);

	if ($mysqli->affected_rows > 0){
		$row = $result->fetch_object();
		$date = $row->sessiontimestamp;
	} else {
		echo $mysqli->error;
	}

	$mysqli->close();

	$expires = $date + 7200;
	$hasexpired = $expires - strtotime("now");
//echo $expires."-".strtotime("now")."=".$hasexpired;
	if ($hasexpired < 0) {
		return false;
	} else {
		return true;
	}
}

function getuserdata($id) {
	$cUser = new User();
	$cUser->getdata($id);
	$_SESSION['user'] = serialize($cUser);
	//echo $_SESSION['user'];
}
?>
