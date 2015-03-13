<?php
require_once('checksession.php');

//###############################variables######################################
$serviceOptions = array("0" => "[S&eacute;lectionner]",
"INF" => "Cellule Informatique",
"REG" => "R&eacute;gie",
"FTR" => "Facturation Taxes",
"EDU" => "Education",
"ETU" => "Etudes");
$legend = 'Nouveau compte';

//###############################procedures#####################################

//echo $_SESSION['user'];
$cUser = unserialize($_SESSION['user']);
$admin = $cUser->userisadmin();
($admin ? $readonly = '' : $readonly = 'readonly ');
($admin ? $lock = '' : $lock = 'blue');

if (isset($_GET['reset']) && $_GET['reset']==1){resetvalues();reseterrors();}

if (isset($_GET['modif'])) {
	switch ($_GET['modif']) {
		case 0:
		$userid = $cUser->userid();
		getData($userid);
		$legend = 'Edition de mon compte';
		break;

		default:
		if (!$admin && $_GET['modif']!=$cUser->userid()) header("Location:compte.php?modif=0");
		$userid = $_GET['modif'];
		getData($userid);
		$legend = 'Edition du compte de '.$_SESSION['values']['txt_Prenom'].' '.$_SESSION['values']['txt_Nom'].' ('.$_SESSION['values']['txt_Login'].')';
		break;
	}

}

if (!empty($_SESSION['client'])) {
		$arCompte = getCompteDisplay();
		$arCompte = preg_split("/,/", $arCompte);
	}

//#################################functions#####################################

function getData($id){
	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);

	$query = "SELECT * FROM `".DB."`.`user` WHERE `user`.`userid` = " . $id;

	$result = $Mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		$_SESSION['values']['txt_Nom'] = strtoupper($row['userlastname']);
		$_SESSION['values']['txt_Prenom'] = ucwords(strtolower($row['userfirstname']));
		$_SESSION['values']['txt_Login'] = $row['userlogin'];
		$_SESSION['values']['box_Service'] = $row['userservice'];
		$_SESSION['values']['txt_Password'] = 'existing';
		$_SESSION['values']['txt_Password2'] = 'existing';
		$_SESSION['values']['chk_isAdmin'] = $row['userisadmin'];
		$_SESSION['values']['chk_isActive'] = $row['userisactive'];
		$_SESSION['values']['chk_isValidator'] = $row['userisvalidator'];
	}
	reseterrors();
	$Mysqli->close();
}

function buildComptes() {
	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT * FROM `".DB."`.`user`";

	$result = $Mysqli->query($query);
	echo '<p>Ouvrir le compte de <select class="input" name="box_Comptes" id="box_Comptes" onchange="jumpto(this.value);">'.
		'<option value="0">S&eacute;lectionner</option>';
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		echo '<option value="' . $row['userid'] . '">' . strtoupper($row['userlastname']) . " " . ucwords(strtolower($row['userfirstname'])) .'</option>';
	}
	echo '</select></p>';
	$Mysqli->close();
}


function buildOptions($options, $selectedOption, $readonly = '') {
	foreach ($options as $value => $text) {
		if ($value === $selectedOption) {
			echo '<option value="' . $value . '" selected="selected">' . $text . '</option>';
		}
		else {
			if ($readonly !== 'readonly ') echo '<option value="' . $value . '">' . $text .'</option>';
		}
	}
}

function reseterrors(){
	$_SESSION['errors']['Nom'] = 'hidden';
	$_SESSION['errors']['Prenom'] = 'hidden';
	$_SESSION['errors']['Login'] = 'hidden';
	$_SESSION['errors']['Service'] = 'hidden';
	$_SESSION['errors']['Password'] = 'hidden';
	$_SESSION['errors']['Password2'] = 'hidden';
}

function resetvalues(){
	$_SESSION['values']['txt_Nom'] = '';
	$_SESSION['values']['txt_Prenom'] = '';
	$_SESSION['values']['txt_Login'] = '';
	$_SESSION['values']['box_Service'] = '';
	$_SESSION['values']['txt_Password'] = '';
	$_SESSION['values']['txt_Password2'] = '';
	$_SESSION['values']['chk_isAdmin'] = '';
	$_SESSION['values']['chk_isActive'] = '';
}

?>
