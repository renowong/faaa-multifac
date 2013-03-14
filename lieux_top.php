<?php
////////////////////////////////////////////////////////////////////////////////
//Logiciel : Multifac V.2.1
//Auteur : Reno Wong
////////////////////////////////////////////////////////////////////////////////
require_once('checksession.php');
//require_once('config.php');

//###############################variables######################################


//###############################procedures#####################################
if (!empty($_SESSION['client'])) {
		$arCompte = '';
		$arCompte = getCompteDisplay();
		$arCompte = preg_split("/,/", $arCompte);
		//print_r ($arCompte);
	}

	if (isset($_GET['edit']) && $_GET['edit'] > 0) {
	getData($_GET['edit']);
	$legend = "Edition d'un lieu";
} else {
	$legend = "Cr&eacute;ation d'un lieu";
}

if (!isset($_SESSION['values']) || $_GET['reset']==1){resetvalues();}

if (!isset($_SESSION['errors']) || $_GET['reset']==1){reseterrors();}


//#################################functions#####################################


function buildOptionsCategories($selectedOption) {
	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT `tarifs_ramassage_om`.`IDtarif`, `tarifs_ramassage_om`.`Type` FROM `".DB."`.`tarifs_ramassage_om`";
	$result = $Mysqli->query($query);

	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		if ($row['IDtarif'] == $selectedOption) {
			echo '<option value="' .$row['IDtarif']. '" selected="selected">' . $row['Type'] . '</option>';
		} else {
			echo '<option value="' . $row['IDtarif'] . '">' . $row['Type'] . '</option>';
		}
	}
	 $Mysqli->close();
}

function buildOptionsPersonnes($selectedOption) {
	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT `clientid`, `clientnom`, `clientprenom`, `clientdatenaissance` FROM `clients` WHERE `clientstatus`='1' ORDER BY `clientnom`";
	$result = $Mysqli->query($query);

	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		if ($row['clientid'] == $selectedOption) {
			echo '<option value="' .$row['clientid']. '" selected="selected">' . strtoupper($row['clientnom']) . " " . strtoupper($row['clientprenom']) . " - " . date("d/m/Y",strtotime($row['clientdatenaissance'])) . '</option>';
		} else {
			echo '<option value="' . $row['clientid'] . '">' . strtoupper($row['clientnom']) . " " . strtoupper($row['clientprenom']) . " - " . date("d/m/Y",strtotime($row['clientdatenaissance'])) . '</option>';
		}
	}
	$Mysqli->close();
}

function buildOptionsMandataires($selectedOption) {
	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT `mandataireid`, `mandatairenom`, `mandataireprenom`, `mandatairetelephone` FROM `mandataires`  WHERE `mandatairestatus`='1' ORDER BY `mandatairenom`";
	$result = $Mysqli->query($query);

	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		if ($row['mandataireid'] == $selectedOption) {
			echo '<option value="' .$row['mandataireid']. '" selected="selected">' . strtoupper($row['mandatairenom']) . " " . strtoupper($row['mandataireprenom']) . " - " . $row['mandatairetelephone'] . '</option>';
		} else {
			echo '<option value="' . $row['mandataireid'] . '">' . strtoupper($row['mandatairenom']) . " " . strtoupper($row['mandataireprenom']) . " - T&eacute;l." . $row['mandatairetelephone'] . '</option>';
		}
	}
	$Mysqli->close();
}

function buildOptionsServitudes($selectedOption) {
	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DBRUES);
	$query = "SELECT `rues`.`IDRue`, `rues`.`Rue` FROM `".DBRUES."`.`rues` ORDER BY `Rue`";
	$result = $Mysqli->query($query);

	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		if ($row['IDRue'] == $selectedOption) {
			echo '<option value="' .$row['IDRue']. '" selected="selected">' . strtoupper($row['Rue']) . '</option>';
		} else {
			echo '<option value="' . $row['IDRue'] . '">' . strtoupper($row['Rue']) . '</option>';
		}
	}
	$Mysqli->close();
}

function buildOptionsQuartiers($selectedOption) {
	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DBRUES);
	$query = "SELECT `quartiers`.`IDQuartier`, `quartiers`.`Quartier` FROM `".DBRUES."`.`quartiers` ORDER BY Quartier";
	$result = $Mysqli->query($query);

	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		if ($row['IDQuartier'] == $selectedOption) {
			echo '<option value="' .$row['IDQuartier']. '" selected="selected">' . $row['Quartier'] . '</option>';
		} else {
			echo '<option value="' . $row['IDQuartier'] . '">' . $row['Quartier'] . '</option>';
		}
	}
	$Mysqli->close();
}

function getData($id){
	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT * FROM `".DB."`.`lieux` WHERE `lieux`.`lieuid` = " . $id;

	$result = $Mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		$_SESSION['values']['chk_status'] = $row['lieustatus'];
		$_SESSION['values']['chk_principal'] = $row['lieuprincipal'];
		$_SESSION['values']['box_Proprietaire'] = $row['lieuproprietaire'];
		$_SESSION['values']['box_Mandataire'] = $row['lieumandataire'];
		$_SESSION['values']['box_Locataire'] = $row['lieulocataire'];
		$_SESSION['values']['box_Categorie'] = $row['lieucategorie'];
		$_SESSION['values']['txt_Nomlieu'] = $row['lieunomlieu'];
		$_SESSION['values']['txt_Surface'] = ($row['lieusurface']==0 ? '' : $row['lieusurface']);
		$_SESSION['values']['txt_Nmaison'] = ($row['lieunmaison']==0 ? '' : $row['lieunmaison']);
		$_SESSION['values']['box_Servitude'] = $row['lieuservitude'];
		$_SESSION['values']['box_Quartier'] = $row['lieuquartier'];
		$_SESSION['values']['txt_Facturer'] = $row['lieufacturer'];
		$_SESSION['values']['txt_EDT'] = ($row['lieuedt']==0 ? '' : $row['lieuedt']);
		$_SESSION['values']['txt_Compteur'] = ($row['lieucompteur']==0 ? '' : $row['lieucompteur']);
		$_SESSION['values']['txt_Observations'] = $row['lieuobservations'];
	}
	if ($_GET['hideerrors']) reseterrors();
	$Mysqli->close();
}

function reseterrors(){
	$_SESSION['errors']['Proprietaire'] = 'hidden';
	$_SESSION['errors']['Mandataire'] = 'hidden';
	$_SESSION['errors']['Locataire'] = 'hidden';
	$_SESSION['errors']['Categorie'] = 'hidden';
	$_SESSION['errors']['Nomlieu'] = 'hidden';
	$_SESSION['errors']['Surface'] = 'hidden';
	$_SESSION['errors']['Nmaison'] = 'hidden';
	$_SESSION['errors']['Servitude'] = 'hidden';
	$_SESSION['errors']['Quartier'] = 'hidden';
	$_SESSION['errors']['Facturer'] = 'hidden';
	$_SESSION['errors']['EDT'] = 'hidden';
	$_SESSION['errors']['Compteur'] = 'hidden';
	$_SESSION['errors']['Observations'] = 'hidden';
}

function resetvalues(){
	$_SESSION['values']['chk_status'] = '1';
	$_SESSION['values']['chk_principal'] = '1';
	$_SESSION['values']['box_Proprietaire'] = '';
	$_SESSION['values']['box_Mandataire'] = '';
	$_SESSION['values']['box_Locataire'] = '';
	$_SESSION['values']['box_Categorie'] = '';
	$_SESSION['values']['txt_Nomlieu'] = '';
	$_SESSION['values']['txt_Surface'] = '';
	$_SESSION['values']['txt_Nmaison'] = '';
	$_SESSION['values']['box_Servitude'] = '';
	$_SESSION['values']['box_Quartier'] = '';
	$_SESSION['values']['box_Facturer'] = '';
	$_SESSION['values']['txt_EDT'] = '';
	$_SESSION['values']['txt_Compteur'] = '';
	$_SESSION['values']['txt_Observations'] = '';
}

?>
