<?php
require_once('checksession.php'); //already includes config.php

//###############################procedures#####################################
if (!empty($_SESSION['client'])) {
                $arCompte = getCompteDisplay();
                $arCompte = preg_split("/,/", $arCompte);
        }


//###############################variables######################################

//#################################building forms################################
$listfacture_avalider = getAllFactures($arCompte[1],$arCompte[2]);
$listfacture_validees = getPaidFactures($arCompte[1],$arCompte[2]);

//#################################functions#####################################


function getAllFactures($id,$type){
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	
	switch($type){
		case "client":
				$query = "SELECT * FROM `factures_cantine` WHERE `reglement` = '0' AND `acceptation` = '1' AND `idclient` = $id";
				//echo $query;
				$result = $mysqli->query($query);
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$typef="cantine";
				if($row["duplicata"]=='0'){$pdf="<img src=\"img/opdf.png\" alt=\"original\" class=\"ico\">";}else{$pdf="<img src=\"img/dpdf.png\" alt=\"duplicata\" class=\"ico\">";}
				$comment = str_replace(" ; ","<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$row["comment"]);
				$enfant_prenom = "<br/>".getEnfantPrenom($row['idfacture']);
				if($row["bourse"]=='1'){
				   $classpurple = " class=\"purple\" title=\"Facture b\351n\351ficiant d'une bourse\"";
				   $check = "<img src='img/checked.png' alt='checked' style='width:32px;height:32px;'/>";
				   $montantbourse = get_bourse($row['idfacture']);
				   //$infobourse = "<br/>Prise en charge par la bourse pour un montant de <b>$montantbourse FCP</b>";
				}else{
				   $classpurple = "";
				   $check = "";
				   $montantbourse = "";
				   //$infobourse = "";
				}
				
				$output .= "<tr id=\"tr".$row['idfacture']."\"$classpurple><td>$typef$enfant_prenom</td>";
				$output .= "<td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de <b>".trispace($row["montantfcp"])." FCP</b> (soit ".$row["montanteuro"]." &euro;)".$infobourse;
				$output .= "<br/>Reste &agrave; r&eacute;gler : <b>".trispace($row["restearegler"])." FCP</b>";
				$output .= "<br/>Infos : ".$comment;
				$output .= "<br/>Obs : ".$row["obs"];
				$output .= "</td><td style=\"text-align:center\"><a href=\"createpdf.php?idfacture=".$row['idfacture']."&type=$typef\" target=\"_blank\">$pdf</a></td>";
				$output .= "<td style=\"text-align:center\"><a href=\"javascript:paiement('".$row["idfacture"]."','$typef')\"><img src=\"img/visa-icon.png\" alt=\"visa\" alt=\"visa\" class=\"ico\"></a></td>";
				$output .= "<td style=\"text-align:center;vertical-align:middle;\">$check</td>";
				}
				$result->close();
				
				$query = "SELECT * FROM `factures_amarrage` WHERE `factures_amarrage`.`type_client` = 'C' AND`reglement` = '0' AND `acceptation` = '1' AND `idclient` = $id";
				//echo $query;
				$result = $mysqli->query($query);
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
				if($row["duplicata"]=='0'){$pdf="<img src=\"img/opdf.png\" alt=\"original\" class=\"ico\">";}else{$pdf="<img src=\"img/dpdf.png\" alt=\"duplicata\" class=\"ico\">";}
				$output .= "<tr><td>amarrage<br/>".$row["navire"]."</td>";
				$output .= "<td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de <b>".trispace($row["montantfcp"])." FCP</b> (soit ".$row["montanteuro"]." &euro;)";
				if($row["restearegler"]!==$row["montantfcp"]) {$output .= "<br/>Reste &agrave; r&eacute;gler : <b>".trispace($row["restearegler"])." FCP</b>";}
				$output .= "<br/>Obs : ".$row["obs"];
				$output .= "</td><td style=\"text-align:center\"><a href=\"createpdf.php?idfacture=".$row['idfacture']."&type=amarrage\" target=\"_blank\">$pdf</a></td>";
				$output .= "<td style=\"text-align:center\"><a href=\"javascript:paiement('".$row["idfacture"]."','amarrage')\"><img src=\"img/visa-icon.png\" alt=\"visa\" class=\"ico\"></a></td>";
				}
				$result->close();
		break;
		
		case "mandataire":
				$query = "SELECT * FROM `factures_etal` WHERE `reglement` = '0' AND `acceptation` = '1' AND `idclient` = $id";
				//echo $query;
				$result = $mysqli->query($query);
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
				if($row["duplicata"]=='0'){$pdf="<img src=\"img/opdf.png\" alt=\"original\" class=\"ico\">";}else{$pdf="<img src=\"img/dpdf.png\" alt=\"duplicata\" class=\"ico\">";}
				$output .= "<tr><td>place et &eacute;tal</td>";
				$output .= "<td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de <b>".trispace($row["montantfcp"])." FCP</b> (soit ".$row["montanteuro"]." &euro;)";
				if($row["restearegler"]!==$row["montantfcp"]) {$output .= "<br/>Reste &agrave; r&eacute;gler : <b>".trispace($row["restearegler"])." FCP</b>";}
				$output .= "<br/>Obs : ".$row["obs"];
				$output .= "</td><td style=\"text-align:center\"><a href=\"createpdf.php?idfacture=".$row['idfacture']."&type=etal\" target=\"_blank\">$pdf</a></td>";
				$output .= "<td style=\"text-align:center\"><a href=\"javascript:paiement('".$row["idfacture"]."','etal')\"><img src=\"img/visa-icon.png\" alt=\"visa\" class=\"ico\"></a></td>";
				}
				$result->close();

				
				$query = "SELECT * FROM `factures_amarrage` WHERE `factures_amarrage`.`type_client` = 'M' AND`reglement` = '0' AND `acceptation` = '1' AND `idclient` = $id";
				//echo $query;
				$result = $mysqli->query($query);
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
				if($row["duplicata"]=='0'){$pdf="<img src=\"img/opdf.png\" alt=\"original\" class=\"ico\">";}else{$pdf="<img src=\"img/dpdf.png\" alt=\"duplicata\" class=\"ico\">";}
				$output .= "<tr><td>amarrage<br/>".$row["navire"]."</td>";
				$output .= "<td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de <b>".trispace($row["montantfcp"])."</b> FCP (soit ".$row["montanteuro"]." &euro;)";
				if($row["restearegler"]!==$row["montantfcp"]) {$output .= "<br/>Reste &agrave; r&eacute;gler : <b>".trispace($row["restearegler"])."</b> FCP";}
				$output .= "<br/>Obs : ".$row["obs"];
				$output .= "</td><td style=\"text-align:center\"><a href=\"createpdf.php?idfacture=".$row['idfacture']."&type=amarrage\" target=\"_blank\">$pdf</a></td>";
				$output .= "<td style=\"text-align:center\"><a href=\"javascript:paiement('".$row["idfacture"]."','amarrage')\"><img src=\"img/visa-icon.png\" alt=\"visa\" class=\"ico\"></a></td>";
				}
				$result->close();				
		break;	
	}

	$mysqli->close();
	return $output;
}

function getEnfantPrenom($idfacture){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "SELECT `enfants`.`prenom` FROM `factures_cantine_details` INNER JOIN `enfants` ".
            " ON `factures_cantine_details`.`idenfant`=`enfants`.`enfantid` WHERE `factures_cantine_details`.`idfacture`='$idfacture'";
    $result = $mysqli->query($query);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $output = $row["prenom"];
    $mysqli->close();
    return $output;
}

function getPaidFactures($id,$type){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);

	switch($type){
		case "client":
			/*
			 $query = "SELECT `factures_cantine`.`idfacture`, `factures_cantine`.`datefacture`, `factures_cantine`.`communeid`, `factures_cantine`.`montantfcp`, `factures_cantine`.`montanteuro`, `factures_cantine`.`comment`".
			", `paiements`.`date_paiement`, `paiements`.`payeur`, `paiements`.`mode`, `paiements`.`montantcfp`, `paiements`.`idpaiement`, `paiements`.`obs` FROM `factures_cantine` ".
			" LEFT JOIN `paiements` ON `factures_cantine`.`idfacture`=`paiements`.`idfacture` WHERE `factures_cantine`.`reglement` = 1 AND `factures_cantine`.`acceptation` = 1 AND `factures_cantine`.`idclient` = $id ORDER BY `paiements`.`idpaiement` DESC LIMIT 10";
			*/
			
			$query = "SELECT `factures_cantine`.`idfacture`,`factures_cantine`.`communeid`,`factures_cantine`.`datefacture`,`factures_cantine`.`montantfcp` as `fmontantfcp`,`factures_cantine`.`montanteuro` as `fmontanteuro`,`factures_cantine`.`obs`,`factures_cantine`.`comment`,`factures_cantine`.`duplicata`,".
			"`paiements`.`idpaiement`,`paiements`.`montantcfp` as `pmontantfcp`,`paiements`.`montanteuro` as `pmontanteuro`,`paiements`.`date_paiement`,`paiements`.`mode`,`paiements`.`payeur` FROM `factures_cantine` ".
			" LEFT JOIN `paiements` ON `factures_cantine`.`idfacture`=`paiements`.`idfacture` WHERE (`factures_cantine`.`montantfcp`>`factures_cantine`.`restearegler`) AND `factures_cantine`.`acceptation` = 1 AND `factures_cantine`.`idclient` = $id ORDER BY `paiements`.`idpaiement` DESC LIMIT 10";
			
			
			//echo $query;
			$result = $mysqli->query($query);
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
			$typef="cantine";
			if($row["duplicata"]=='0'){$pdf="<img src=\"img/opdf.png\" alt=\"original\" class=\"ico\">";}else{$pdf="<img src=\"img/dpdf.png\" alt=\"duplicata\" class=\"ico\">";}
			$comment = str_replace(" ; ","<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$row["comment"]);
			$enfant_prenom = "<br/>".getEnfantPrenom($row['idfacture']);
			$output .= "<tr><td>$typef$enfant_prenom</td>".
			"<td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ".trispace($row["fmontantfcp"])." FCP (soit ".$row["fmontanteuro"]." &euro;)<br/>";
			if(isset($row['idpaiement'])){
				$output .= "- R&eacute;gl&eacute;e la somme de <b>".trispace($row["pmontantfcp"])." FCP</b> par ".strtoupper($row["payeur"])." (".translatemode($row["mode"])." le ".french_date($row["date_paiement"]).")<br/>";
				$output .= "- Infos : $comment<br/>";
			}else{
				$output .= "- R&eacute;gl&eacute;e par bourse ou avoir (pas de quitance)<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$comment<br/>";
			}
			$output .= "- Obs: ".$row["obs"]."</td>".
			"<td style=\"text-align:center\"><a href=\"createpdf.php?idfacture=".$row['idfacture']."&type=$typef\" target=\"_blank\">$pdf</a></td>";
			if(isset($row['idpaiement'])){
				$output .= "<td style=\"text-align:center\"><a href=\"createrecu.php?id=".$row['idpaiement']."&type=$typef\" target=\"_blank\"><img src=\"img/pdf_blue.png\" alt=\"pdf_blue\" class=\"ico\"></a></td>";
				}
			}
			$result->close();
			
			$query = "SELECT `factures_amarrage`.`idfacture`, `factures_amarrage`.`datefacture`, `factures_amarrage`.`communeid`, `factures_amarrage`.`montantfcp`, `factures_amarrage`.`montanteuro`, `factures_amarrage`.`navire`, `factures_amarrage`.`duplicata`".
			", `paiements`.`date_paiement`, `paiements`.`payeur`, `paiements`.`mode`, `paiements`.`montantcfp`, `paiements`.`idpaiement`, `paiements`.`obs` FROM `".DB."`.`factures_amarrage` ".
			" JOIN `".DB."`.`paiements` ON `factures_amarrage`.`idfacture`=`paiements`.`idfacture` WHERE `factures_amarrage`.`type_client` = 'C' AND (`factures_amarrage`.`montantfcp`>`factures_amarrage`.`restearegler`) AND `factures_amarrage`.`acceptation` = '1' AND `factures_amarrage`.`idclient` = $id ORDER BY `paiements`.`idpaiement` DESC LIMIT 10";
			//echo $query;
			$result = $mysqli->query($query);
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
			if($row["duplicata"]=='0'){$pdf="<img src=\"img/opdf.png\" alt=\"original\" class=\"ico\">";}else{$pdf="<img src=\"img/dpdf.png\" alt=\"duplicata\" class=\"ico\">";}
			$output .= "<tr><td>amarrage<br/>".$row["navire"]."</td>".
			"<td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ".trispace($row["montantfcp"])." FCP (soit ".$row["montanteuro"]." &euro;)<br/>".
			"- R&eacute;gl&eacute;e la somme de <b>".trispace($row["montantcfp"])." FCP</b> par ".strtoupper($row["payeur"])." (".translatemode($row["mode"])." le ".french_date($row["date_paiement"]).")<br/>".
			"- Obs: ".$row["obs"]."</td>".
			"<td style=\"text-align:center\"><a href=\"createpdf.php?idfacture=".$row['idfacture']."&type=amarrage\" target=\"_blank\">$pdf</a></td>".
			"<td style=\"text-align:center\"><a href=\"createrecu.php?id=".$row['idpaiement']."&type=amarrage\" target=\"_blank\"><img src=\"img/pdf_blue.png\" alt=\"pdf_blue\" class=\"ico\"></a></td>";
			}
			$result->close();
		break;
		
		case "mandataire":
			$query = "SELECT `factures_etal`.`idfacture`, `factures_etal`.`datefacture`, `factures_etal`.`communeid`, `factures_etal`.`montantfcp`, `factures_etal`.`montanteuro`".
			", `paiements`.`date_paiement`, `paiements`.`payeur`, `paiements`.`mode`, `paiements`.`montantcfp`, `paiements`.`idpaiement`, `paiements`.`obs` FROM `".DB."`.`factures_etal` ".
			" JOIN `".DB."`.`paiements` ON `factures_etal`.`idfacture`=`paiements`.`idfacture` WHERE (`factures_etal`.`montantfcp`>`factures_etal`.`restearegler`) AND `factures_etal`.`acceptation` = 1 AND `factures_etal`.`idclient` = $id ORDER BY `paiements`.`idpaiement` DESC LIMIT 10";
			//echo $query;
			$result = $mysqli->query($query);
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
			$output .= "<tr><td>place et &eacute;tal</td>".
			"<td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ".trispace($row["montantfcp"])." FCP (soit ".$row["montanteuro"]." &euro;)<br/>".
			"- R&eacute;gl&eacute;e la somme de <b>".trispace($row["montantcfp"])." FCP</b> par ".strtoupper($row["payeur"])." (".translatemode($row["mode"])." le ".french_date($row["date_paiement"]).")<br/>".
			"- Obs: ".$row["obs"]."</td>".
			"<td style=\"text-align:center\"><a href=\"createpdf.php?idfacture=".$row['idfacture']."&type=etal\" target=\"_blank\"><img src=\"img/pdf.png\" alt= class=\"ico\"></a></td>".
			"<td style=\"text-align:center\"><a href=\"createrecu.php?id=".$row['idpaiement']."&type=etal\" target=\"_blank\"><img src=\"img/pdf_blue.png\" alt=\"pdf_blue\" class=\"ico\"></a></td>";
			}
			$result->close();
			
			$query = "SELECT `factures_amarrage`.`idfacture`, `factures_amarrage`.`datefacture`, `factures_amarrage`.`communeid`, `factures_amarrage`.`montantfcp`, `factures_amarrage`.`montanteuro`, `factures_amarrage`.`navire`".
			", `paiements`.`date_paiement`, `paiements`.`payeur`, `paiements`.`mode`, `paiements`.`montantcfp`, `paiements`.`idpaiement`, `paiements`.`obs` FROM `".DB."`.`factures_amarrage` ".
			" JOIN `".DB."`.`paiements` ON `factures_amarrage`.`idfacture`=`paiements`.`idfacture` WHERE `factures_amarrage`.`type_client` = 'M' AND (`factures_amarrage`.`montantfcp`>`factures_amarrage`.`restearegler`) AND `factures_amarrage`.`acceptation` = '1' AND `factures_amarrage`.`idclient` = $id ORDER BY `paiements`.`idpaiement` DESC LIMIT 10";
			//echo $query;
			$result = $mysqli->query($query);
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
			$output .= "<tr><td>amarrage<br/>".$row["navire"]."</td>".
			"<td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ".trispace($row["montantfcp"])." FCP (soit ".$row["montanteuro"]." &euro;)<br/>".
			"- R&eacute;gl&eacute;e la somme de <b>".trispace($row["montantcfp"])." FCP</b> par ".strtoupper($row["payeur"])." (".translatemode($row["mode"])." le ".french_date($row["date_paiement"]).")<br/>".
			"- Obs: ".$row["obs"]."</td>".
			"<td style=\"text-align:center\"><a href=\"createpdf.php?idfacture=".$row['idfacture']."&type=amarrage\" target=\"_blank\"><img src=\"img/pdf.png\" alt=\"pdf\"class=\"ico\"></a></td>".
			"<td style=\"text-align:center\"><a href=\"createrecu.php?id=".$row['idpaiement']."&type=amarrage\" target=\"_blank\"><img src=\"img/pdf_blue.png\" alt=\"pdf_blue\" class=\"ico\"></a></td>";
			}
			$result->close();
		break;
	}
	$mysqli->close();
	return $output;		
}

function translatemode($input){
		switch($input){
		case "num":
			$mode = "Num&eacute;raire";	
		break;

		case "chq":
			$mode = "Ch&egrave;que";	
		break;

		case "vir":
			$mode = "Virement";	
		break;
				
		case "tsr":
			$mode = "Tr&eacute;sor";	
		break;

		case "mnd":
			$mode = "Mandat";	
		break;

		case "tpe":
			$mode = "TPE";	
		break;

		case "anl":
			$mode = "Annulation";	
		break;
				
		default:
			$mode = "ERREUR TRADUCTION MODE";
		break;
		}
		return $mode;
}

function get_bourse($idfacture){
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $result = $mysqli->query("SELECT `status_cantine`.`valeur`,`status_cantine`.`MontantFCP`,`factures_cantine_details`.`quant` FROM `factures_cantine_details` INNER JOIN `status_cantine` ON `factures_cantine_details`.`idtarif`=`status_cantine`.`idstatus` WHERE `factures_cantine_details`.`idfacture`='$idfacture'");
        
	$result_array = array();
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            $result_array[] = $row;
	}
	$mysqli->close();
    
	foreach($result_array as &$value){
		$valpriseencharge = ($value['valeur']*$value['MontantFCP'])/100;
		$quantite = $value['quant'];
		$bourse += $valpriseencharge*$quantite;
	}
	return $bourse;
}

?>
