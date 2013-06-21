<?php
require_once('checksession.php');


//###############################variables######################################
$prefixOptions = array("0" => "[S&eacute;lectionner]",
                                                 "ASS." => "ASS.",
                                                 "SCI" => "SCI",
                                                 "SARL" => "SARL",
                                                 "SA" => "SA",
                                                 "EURL" => "EURL",
                                                 "SEM" => "SEM",
                                                 "EPIC" => "EPIC",
                                                 "MR" => "MR",
                                                 "MME" => "MME",
                                                 "MLLE" => "MLLE");


//###############################procedures#####################################

if (isset($_GET['edit']) && $_GET['edit'] > 0) {
	getData($_GET['edit']); //set client data into session variables (just for the form)
	setPersistData($_GET['edit']); //set client data into persistent variable (XML)
	$legend = "Edition du compte Mandataire ".$_SESSION['values']['txt_Nom']." ".$_SESSION['values']['txt_Prenom'];
} else {
	$legend = "Cr&eacute;ation d'un compte mandataire";
	$_SESSION['values']['chk_status'] = '1';
}

if (!empty($_SESSION['client'])) {
                $arCompte = getCompteDisplay();
                $arCompte = preg_split("/,/", $arCompte);
        }

if (!isset($_SESSION['values']) || (isset($_GET['reset']) && $_GET['reset']==1)){resetvalues();}

if (!isset($_SESSION['errors']) || (isset($_GET['reset']) && $_GET['reset']==1)){reseterrors();}


//#################################functions#####################################

function getData($id){
	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);

	$query = "SELECT * FROM `".DB."`.`mandataires` WHERE `mandataires`.`mandataireid` = " . $id;

	$result = $Mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		$_SESSION['values']['chk_status'] = $row['mandatairestatus'];
		$_SESSION['values']['box_Prefix'] = $row['mandataireprefix'];
		$_SESSION['values']['txt_RS'] = $row['mandataireRS'];
		$_SESSION['values']['txt_Nom'] = $row['mandatairenom'];
		$_SESSION['values']['txt_Prenom'] = $row['mandataireprenom'];
		$_SESSION['values']['txt_IDTresor'] = $row['mandataireidtresor'];
		$_SESSION['values']['txt_Email'] = $row['mandataireemail'];
		$_SESSION['values']['txt_Notahiti'] = $row['mandatairenotahiti'];
		$_SESSION['values']['txt_RC'] = $row['mandataireRC'];
		$_SESSION['values']['txt_Telephone'] = $row['mandatairetelephone'];
		$_SESSION['values']['txt_Telephone2'] = $row['mandatairetelephone2'];
		$_SESSION['values']['txt_Fax'] = $row['mandatairefax'];
		$_SESSION['values']['txt_BP'] = $row['mandatairebp'];
		$_SESSION['values']['txt_CP'] = $row['mandatairecp'];
		$_SESSION['values']['txt_Ville'] = $row['mandataireville'];
		$_SESSION['values']['txt_Commune'] = $row['mandatairecommune'];
		$_SESSION['values']['txt_Pays'] = $row['mandatairepays'];
                $_SESSION['values']['txt_Aroa'] = $row['aroa'];
                $_SESSION['values']['txt_Quartier'] = $row['quartier'];
		$_SESSION['values']['txt_RIB'] = $row['mandatairerib'];
                $_SESSION['values']['txt_obs'] = $row['obs'];
	}
	if ($_GET['hideerrors']) reseterrors();
	$Mysqli->close();
}

function setPersistData($id){
	$_SESSION['client'] = '<?xml version="1.0" encoding="windows-1252" standalone="yes"?>'.
	'<compte>'.
	'<type>mandataire</type>'.
	'<clientid>'.$id.'</clientid>'.
	'<status>'.$_SESSION['values']['chk_status'].'</status>'.
	'<prefix>'.$_SESSION['values']['box_Prefix'].'</prefix>'.
	'<RS>'.$_SESSION['values']['txt_RS'].'</RS>'.
	'<nom>'.$_SESSION['values']['txt_Nom'].'</nom>'.
	'<nommarital></nommarital>'.
	'<prenom>'.$_SESSION['values']['txt_Prenom'].'</prenom>'.
	'<prenom2></prenom2>'.
	'<datenaissance></datenaissance>'.
	'<lieunaissance></lieunaissance>'.
	'<idtresor>'.$_SESSION['values']['txt_IDTresor'].'</idtresor>'.
	'<email>'.$_SESSION['values']['txt_Email'].'</email>'.
	'<notahiti>'.$_SESSION['values']['txt_Notahiti'].'</notahiti>'.
	'<RC>'.$_SESSION['values']['txt_RC'].'</RC>'.
	'<cps></cps>'.
	'<telephone>'.$_SESSION['values']['txt_Telephone'].'</telephone>'.
	'<telephone2>'.$_SESSION['values']['txt_Telephone2'].'</telephone2>'.
	'<fax>'.$_SESSION['values']['txt_Fax'].'</fax>'.
	'<bp>'.$_SESSION['values']['txt_BP'].'</bp>'.
	'<cp>'.$_SESSION['values']['txt_CP'].'</cp>'.
	'<ville>'.$_SESSION['values']['txt_Ville'].'</ville>'.
	'<commune>'.$_SESSION['values']['txt_Commune'].'</commune>'.
	'<pays>'.$_SESSION['values']['txt_Pays'].'</pays>'.
        '<aroa>'.$_SESSION['values']['txt_Aroa'].'</aroa>'.
        '<quartier>'.$_SESSION['values']['txt_Quartier'].'</quartier>'.
	'<rib>'.$_SESSION['values']['txt_RIB'].'</rib>'.
	'</compte>';
}

function resetvalues(){
	$_SESSION['values']['chk_status'] = '1';
	$_SESSION['values']['box_Prefix'] = '';
	$_SESSION['values']['txt_RS'] = '';
	$_SESSION['values']['txt_Nom'] = '';
	$_SESSION['values']['txt_Prenom'] = '';
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
        $_SESSION['values']['txt_Aroa'] = '';
        $_SESSION['values']['txt_Quartier'] = '';
	$_SESSION['values']['txt_RIB'] = '';
        $_SESSION['values']['txt_obs'] = '';
}

function reseterrors(){
	$_SESSION['errors']['Prefix'] = 'hidden';
	$_SESSION['errors']['RS'] = 'hidden';
	$_SESSION['errors']['Nom'] = 'hidden';
	$_SESSION['errors']['Prenom'] = 'hidden';
	$_SESSION['errors']['IDTresor'] = 'hidden';
	$_SESSION['errors']['Email'] = 'hidden';
	$_SESSION['errors']['Notahiti'] = 'hidden';
	$_SESSION['errors']['RC'] = 'hidden';
	$_SESSION['errors']['Telephone'] = 'hidden';
	$_SESSION['errors']['Telephone2'] = 'hidden';
	$_SESSION['errors']['Fax'] = 'hidden';
	$_SESSION['errors']['BP'] = 'hidden';
	$_SESSION['errors']['CP'] = 'hidden';
	$_SESSION['errors']['Ville'] = 'hidden';
	$_SESSION['errors']['Commune'] = 'hidden';
	$_SESSION['errors']['Pays'] = 'hidden';
        $_SESSION['errors']['Aroa'] = 'hidden';
        $_SESSION['errors']['Quartier'] = 'hidden';
	$_SESSION['errors']['RIB'] = 'hidden';
}

function getLieu($type){
	$output;
	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD);

	$query = "SELECT `".DB."`.`lieux`.`lieuid`, `".DB."`.`tarifs_ramassage_om`.`Type`, `".DBRUES."`.`rues`.`Rue`, `".DBRUES.
			 "`.`quartiers`.`Quartier` FROM `".DB."`.`lieux` INNER JOIN `".DBRUES.
			 "`.`rues` ON `lieux`.`lieuservitude` = `rues`.`IDRue` ".
			 "INNER JOIN `".DBRUES."`.`quartiers` ON `lieux`.`lieuquartier` = `quartiers`.`IDQuartier` ".
			 "INNER JOIN `".DB."`.`tarifs_ramassage_om` ON `lieux`.`lieucategorie` = `tarifs_ramassage_om`.`IDtarif` ".
			 "WHERE `lieux`.`$type` = ". $_GET['edit'];

	$result = $Mysqli->query($query);
	$output = '';
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		$output .= "<a href='lieux.php?edit=".$row['lieuid']."&hideerrors=1'>".$row['Type']." &agrave; ".$row['Rue']." ".$row['Quartier']."</a><br/><br/>";
	}
	return $output;
	$mysqli->close();
}

function buildLieuTable(){
	if (isset($_GET['edit']) && $_GET['edit'] > 0) {
	$output = '<td><table id="tbllieu" class="tblform">'.
				'	<thead>'.
				'		<tr>'.
				'			<th>Lieux</th>'.
				'		</tr>'.
				'	</thead>'.
				'	<tbody>'.
				'		<tr>'.
				'				<td><label>Mandataire de</label></td>'.
				'		</tr>'.
				'		<tr>'.
				'			<td>'.getLieu("lieumandataire").'</td>'.
				'		</tr>'.
				'	</tbody>'.
				'</table></td>';
//	'</td></tr>'.
//	'</table>';

	return $output;
	}
}

function buildOptions($options, $selectedOption) {
        foreach ($options as $value => $text) {
                if ($value === $selectedOption) {
                        echo '<option value="' . $value . '" selected="selected">' . $text . '</option>';
                } else {
                        echo '<option value="' . $value . '">' . $text . '</option>';
                }
        }
}

function buildFacturesEnCoursTable($id,$ar_tables){
//print_r($ar_tables);
    
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$output = "<table class=\"tblform\"><tbody><tr><th>Type de Facture</th><th>Facture</th><th>Status</th><th>Commentaire</th></tr></tbody>";

foreach( $ar_tables as &$val ){
        $query = "SELECT * FROM `".$val['table']."` WHERE `idclient` = $id ORDER BY datefacture DESC, idfacture DESC limit 30";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            $comment = str_replace(" ; ","<br/>",$row["comment"]);
		if($row["validation"]==0) {$status="En cours de validation";$reject="";
		}else{
			if($row["acceptation"]==0){$status="Refus&eacute;e";$reject="reject";}else{$status="Valid&eacute;e";$reject="";}
		}
                $output .= "<tbody class=\"$reject\"><tr><td>".$val['title']."</td><td><a href=\"createpdf.php?idfacture=".$row['idfacture']."&type=".$val['link']."\" target=\"_blank\">Facture du ".$row["datefacture"]." montant de ";
		$output .= trispace($row["montantfcp"]);
		$output .= " FCP (soit ".$row["montanteuro"]." &euro;)</a></td><td>$status</td><td>$comment</td></tr></tbody>";
        }
    }  

	$output .= "</table>";
    $mysqli->close();
    return $output;

}
?>
