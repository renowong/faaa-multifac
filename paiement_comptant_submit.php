<?php
////////////////////////////////////////////////////////////////////////////////
//usage :
//http://localhost/paiement_comptant_submit.php?id=50&payeur=reno&type=1&montant=23&obs=&numero_cheque=&organisme=&date_virement&info_tresor=&echelonnage=&mode=
////////////////////////////////////////////////////////////////////////////////
session_start();
//require_once ('error_handler.php');
require_once ('config.php');
$date_paiement = date("Y-m-d");
$table = $_GET['table'];
$payeur = strtoupper($_GET['payeur']);
$type = $_GET['type'];
$numero_cheque = $_GET['numero_cheque'];
$organisme = $_GET['organisme'];
$date_virement = $_GET['date_virement'];
$date_tresor = $_GET['date_tresor'];
$info_tresor = $_GET['info_tresor'];
$tpe = $_GET['tpe'];
$montanttotalcfp = $_GET['montantfcp'];
$montanttotaleuro = $_GET['montanteuro'];
$montantech = $_GET['montantech'];
$restearegler = $_GET['restearegler'];
$mode = $_GET['mode'];
$echelonnage = $_GET['echelonnage'];
$obs = strtoupper(str_replace("'","\'",$_GET['obs']));
$id = $_GET['id'];

if ($echelonnage=='0'){
		$montantcfp = $restearegler;
		$montanteuro = $restearegler/119.332;
		$restearegler = 0;
		$reglement = 1;
} else {
		$montantcfp = $montantech;
		$montanteuro = $montantcfp/119.332;
		$restearegler = $restearegler-$montantech;
		if($restearegler==0){$reglement = 1;}else{$reglement = 0;}
}

$lastid = enterdata($id,$date_paiement,$payeur,$type,$numero_cheque,$organisme,$date_virement,$date_tresor,$info_tresor,$tpe,$montantcfp,$montanteuro,$mode,$echelonnage,$obs,$restearegler,$reglement,$table);

if ($lastid>0) {
		$response = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".
			"<response>".
				"<lastid>$lastid</lastid>".
			"</response>";
	if(ob_get_length()) ob_clean();
	header('Content-Type: text/xml');
} else {
		$response = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".
			"<response>".
				"<lastid>0</lastid>".
			"</response>";
	if(ob_get_length()) ob_clean();
	header('Content-Type: text/xml');
	
}
echo $response;


///////////////////////////functions////////////////////////////////////////////


function enterdata($id,$date_paiement,$payeur,$type,$numero_cheque,$organisme,$date_virement,$date_tresor,$info_tresor,$tpe,$montantcfp,$montanteuro,$mode,$echelonnage,$obs,$restearegler,$reglement,$table){
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$mysqli->set_charset("utf8");
    
    //if($table=="repas")$table="cantine";
	
	switch($mode) {
		case "num":
				$query = "INSERT INTO `paiements` (`idpaiement`,`idfacture`,`date_paiement`,`payeur`,`type`,`mode`,`montantcfp`,`montanteuro`,`obs`)".
					 " VALUES (NULL, '".$id."', '".$date_paiement."', '".$payeur."', '".$type."', '".$mode."', '".$montantcfp."', '".$montanteuro."', '".$obs."')";
				$mysqli->query($query);
				$lastid = $mysqli->insert_id;
				////next update facture
				$query = "UPDATE  `factures_".$table."` SET `reglement`='$reglement', `datereglement`='$date_paiement', `restearegler`='$restearegler',`comment`=CONCAT(`comment`,' ; ".date("d/m/y")." R&eacute;gl&eacute;e ($mode par $payeur)')  WHERE  `factures_".$table."`.`idfacture` = $id";
				$mysqli->query($query);
			
		break;

		case "chq":
				$query = "INSERT INTO `paiements` (`idpaiement`,`idfacture`,`date_paiement`,`payeur`,`type`,`mode`,`montantcfp`,`montanteuro`,`numero_cheque`,`organisme`,`obs`)".
				" VALUES (NULL, '".$id."', '".$date_paiement."', '".$payeur."', '".$type."', '".$mode."', '".$montantcfp."', '".$montanteuro."', '".$numero_cheque."', '".$organisme."', '".$obs."')";
				$mysqli->query($query);
				$lastid = $mysqli->insert_id;
				////next update facture
				$query = "UPDATE  `factures_".$table."` SET  `reglement`='$reglement', `datereglement`='".$date_paiement."', `restearegler`='$restearegler',`comment`=CONCAT(`comment`,' ; ".date("d/m/y")." R&eacute;gl&eacute;e ($mode par $payeur)')  WHERE  `factures_".$table."`.`idfacture` = $id";
				$mysqli->query($query);
		break;

		case "tsr":
				$date_tresor = mysqldateformat($date_tresor);
				$query = "INSERT INTO `paiements` (`idpaiement`,`idfacture`,`date_paiement`,`payeur`,`type`,`mode`,`montantcfp`,`montanteuro`,`date_transaction`,`info_tresor`,`obs`)".
				" VALUES (NULL, '".$id."', '".$date_paiement."', '".$payeur."', '".$type."', '".$mode."', '".$montantcfp."', '".$montanteuro."', '".$date_tresor."', '".$info_tresor."', '".$obs."')";
				$mysqli->query($query);
				$lastid = $mysqli->insert_id;
				////next update facture
				$query = "UPDATE  `factures_".$table."` SET  `reglement`='$reglement', `datereglement`='".$date_paiement."', `restearegler`='$restearegler',`comment`=CONCAT(`comment`,' ; ".date("d/m/y")." R&eacute;gl&eacute;e ($mode par $payeur)')  WHERE  `factures_".$table."`.`idfacture` = $id";
				$mysqli->query($query);
				
		break;
		
		case "12cf":
		case "22cf":
		case "vir":
				$mode = "vir";
				$date_virement = mysqldateformat($date_virement);
				$query = "INSERT INTO `paiements` (`idpaiement`,`idfacture`,`date_paiement`,`payeur`,`type`,`mode`,`montantcfp`,`montanteuro`,`date_transaction`,`obs`)".
				" VALUES (NULL, '".$id."', '".$date_paiement."', '".$payeur."', '".$type."', '".$mode."', '".$montantcfp."', '".$montanteuro."', '".$date_virement."', '".$obs."')";
				$mysqli->query($query);
				$lastid = $mysqli->insert_id;
				////next update facture
				$query = "UPDATE  `factures_".$table."` SET  `reglement`='$reglement', `datereglement`='".$date_paiement."', `restearegler`='$restearegler',`comment`=CONCAT(`comment`,' ; ".date("d/m/y")." R&eacute;gl&eacute;e ($mode par $payeur)')  WHERE  `factures_".$table."`.`idfacture` = $id";
				$mysqli->query($query);
				
		break;

		case "mnd":
				$query = "INSERT INTO `paiements` (`idpaiement`,`idfacture`,`date_paiement`,`payeur`,`type`,`mode`,`montantcfp`,`montanteuro`,`obs`)".
				" VALUES (NULL, '".$id."', '".$date_paiement."', '".$payeur."', '".$type."', '".$mode."', '".$montantcfp."', '".$montanteuro."', '".$obs."')";
				$mysqli->query($query);
				$lastid = $mysqli->insert_id;
				////next update facture
				$query = "UPDATE  `factures_".$table."` SET  `reglement`='$reglement', `datereglement`='".$date_paiement."', `restearegler`='$restearegler',`comment`=CONCAT(`comment`,' ; ".date("d/m/y")." R&eacute;gl&eacute;e ($mode par $payeur)')  WHERE  `factures_".$table."`.`idfacture` = $id";
				$mysqli->query($query);
				
		break;
		
		case "tpe":
				$query = "INSERT INTO `paiements` (`idpaiement`,`idfacture`,`date_paiement`,`payeur`,`type`,`mode`,`tpe`,`montantcfp`,`montanteuro`,`obs`)".
				" VALUES (NULL, '".$id."', '".$date_paiement."', '".$payeur."', '".$type."', '".$mode."', '".$tpe."', '".$montantcfp."', '".$montanteuro."', '".$obs."')";
				$mysqli->query($query);
				$lastid = $mysqli->insert_id;
				////next update facture
				$query = "UPDATE  `factures_".$table."` SET  `reglement`='$reglement', `datereglement`='".$date_paiement."', `restearegler`='$restearegler',`comment`=CONCAT(`comment`,' ; ".date("d/m/y")." R&eacute;gl&eacute;e ($mode par $payeur)')  WHERE  `factures_".$table."`.`idfacture` = $id";
				$mysqli->query($query);
				
		break;

		case "22bc":
				$$reglement = 1;
				$restearegler = 0;
				$query = "UPDATE  `factures_".$table."` SET  `reglement`='$reglement', `datereglement`='".$date_paiement."', `restearegler`='$restearegler',`comment`=CONCAT(`comment`,' ; ".date("d/m/y")." Application 2/2 Bourse Commune')  WHERE  `factures_".$table."`.`idfacture` = $id";
				$mysqli->query($query);
				
				enterbourse($id,$mode,$montantcfp);
				$lastid = 0;
		break;

		case "12bc":
				$query = "UPDATE  `factures_".$table."` SET `reglement`='$reglement', `datereglement`='$date_paiement', `restearegler`='$restearegler',`comment`=CONCAT(`comment`,' ; ".date("d/m/y")." Application 1/2 Bourse Commune')  WHERE  `factures_".$table."`.`idfacture` = $id";
				$mysqli->query($query);
				
				enterbourse($id,$mode,$montantcfp);
				$lastid = 0;
		break;
		
		default:
				$lastid = 0;
		break;
	}

	$mysqli->close();
	return $lastid;
}

function enterbourse($factureid,$mode,$montant){
		$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
		$mysqli->set_charset("utf8");
		$query = "SELECT `factures_cantine_details`.`idfacture`, `enfants`.`nom`, `enfants`.`prenom`, `enfants`.`classe`, `enfants`.`dn`, `status_cantine`.`status`, `ecoles_faaa`.`nomecole` ".
		"FROM `factures_cantine_details` ".
		"LEFT JOIN `enfants` ON `factures_cantine_details`.`idenfant`=`enfants`.`enfantid` ".
		"LEFT JOIN `status_cantine` ON `factures_cantine_details`.`idtarif`=`status_cantine`.`idstatus` ".
		"LEFT JOIN `ecoles_faaa` ON `enfants`.`ecole`=`ecoles_faaa`.`ecoleid` ".
		"WHERE `factures_cantine_details`.`idfacture` = '$factureid'";
		
		$result = $mysqli->query($query);
		$row = $result->fetch_array(MYSQLI_ASSOC);
                $nom = $row['nom'];
		$prenom = $row['prenom'];
		$classe = $row['classe'];
		$dn = $row['dn'];
		$status = $row['status'];
		$ecole = $row['nomecole'];
		
		
		$query = "INSERT INTO `bourses` (`idbourse`, `type`, `nom`, `prenom`, `status`, `ecole`, `classe`, `dn`, `montant`, `date`,`idfacture`) ".
		"VALUES (NULL, '$mode','$nom','$prenom','$status','$ecole','$classe','$dn','$montant',CURRENT_TIMESTAMP,'$factureid')";
		$mysqli->query($query);
		$mysqli->close();
		
		print $query;
}


function mysqldateformat($input){
		$arr = explode('/', $input);
		return $arr[2].'-'.$arr[1].'-'.$arr[0];
}
?>
