<?php
require('fpdf/fpdf.php');
require('chiffreenlettre.php');
require_once ('config.php');
require_once('global_functions.php');

$idfacture = $_GET['idfacture'];
$typefacture = $_GET['type'];
$zip = $_GET['zip'];
$details_array = array();
$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);

switch($typefacture){
	case "cantine":
		$titlefacture = "Facturation de la Cantine";

		//first get information of facture
		$query = "SELECT DATE_FORMAT(`factures_cantine`.`datefacture`, '%d/%m/%Y') AS `datefacture`, ".
			"DATE_FORMAT(DATE_ADD(`factures_cantine`.`datefacture`, INTERVAL 31 DAY), '%d/%m/%Y') AS `datelimite`, ".
			"`factures_cantine`.`validation`, `factures_cantine`.`communeid`, `factures_cantine`.`idclient`, `factures_cantine`.`obs` AS `periode`, ".
			"`factures_cantine`.`avoir`, `factures_cantine`.`avoir_on_id`, `clients`.`clientcivilite`, ".
			"`clients`.`clientnom`, `clients`.`clientnommarital`, `clients`.`clientprenom`, `clients`.`clientprenom2`, ".
			"`clients`.`clientbp`, `clients`.`clientcp`, `clients`.`clientville`, `clients`.`clientcommune`, ".
			"`clients`.`clientpays`, `clients`.`clienttelephone`, `clients`.`clientfax`, `clients`.`aroa`, `clients`.`quartier` ".
			"FROM `factures_cantine` INNER JOIN `clients` ON `factures_cantine`.`idclient`=`clients`.`clientid` ".
			"WHERE `factures_cantine`.`idfacture` = $idfacture";
		$result = $mysqli->query($query);
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$datefacture = $row['datefacture'];
				$nofacture = $row['communeid'];
				$periode = html_entity_decode($row['periode'],ENT_QUOTES, "ISO-8859-1");
				$facturevalidation = $row['validation'];
				$client = html_entity_decode($row['clientnom']." ".$row['clientprenom'],ENT_QUOTES, "ISO-8859-1");
				$client = strtoupper($client);
				$contact1 = "BP ".$row['clientbp']." - ".$row['clientcp']." ".$row['clientville'];
				$contact2 = $row['aroa']." / ".$row['quartier'];
				$telephone = "Téléphone : ".$row['clienttelephone'];
				$fax = "Fax : ".$row['clientfax'];
				$datelimite = $row['datelimite'];
				$idclient = $row['idclient'];
				$avoir = $row['avoir'];
				$avoirobs = $row['avoir_on_id'];
				}
		$result->close();
		

		//next get information on details of facture
		$query = "SELECT `factures_cantine_details`.`quant`, `status_cantine`.`status`, `status_cantine`.`valeur`, `status_cantine`.`MontantFCP`, `status_cantine`.`MontantEURO`, `status_cantine`.`Unite`, `status_cantine`.`Delib`, `status_cantine`.`Datedelib`, `enfants`.`prenom`, `enfants`.`enfantid` FROM `factures_cantine_details` LEFT JOIN `status_cantine` ON `factures_cantine_details`.`idtarif` = `status_cantine`.`idstatus` RIGHT JOIN `enfants` ON `factures_cantine_details`.`idenfant` = `enfants`.`enfantid` WHERE `factures_cantine_details`.`idfacture` = $idfacture";
		
		$result = $mysqli->query($query);
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
				array_push($details_array, $row);
				}
		
		$result->close();
		
		
		//next get information on the destinataire
		$query = "SELECT  `enfants`.`enfantid`,`enfants`.`nom`,`enfants`.`prenom`,`enfants`.`classe`,`ecoles_faaa`.`nomecole` FROM `enfants` RIGHT JOIN `ecoles_faaa` ON `enfants`.`ecole`=`ecoles_faaa`.`ecoleid` WHERE `enfants`.`enfantid` ='".$details_array[0]['enfantid']."'";
		$result = $mysqli->query($query);
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$destinataire = $row['nom']." ".$row['prenom'];
				$destinataire = html_entity_decode($destinataire, ENT_QUOTES);
				$ecole = $row['nomecole'];
				$classe = $row['classe'];
				$idenfant = $row['enfantid'];
				}
				
		$d=$details_array[0]['Delib'];
		switch($d){
			case "160-2012":
				$delib = "La présente facture est conforme à la délibération n°160/2012 du 28 août 2012 fixant la tarification des repas de la cuisine centrale.";
				break;
			case "217-2013":
				$delib = "La présente facture est conforme à la délibération n°217/2013 du 18 février 2013 fixant la tarification des repas de la cuisine centrale.";
				break;
		}
		
		$result->close();
		
		$duplicata = get_duplicata_status($idfacture,'factures_cantine');
		
		$solde = get_solde($idfacture,$idclient,'factures_cantine',$idenfant);
		
	break;

	case "etal":
		$titlefacture = "Facturation Place et Etal";
			
		//first get information of facture
		$query = "SELECT DATE_FORMAT(`factures_etal`.`datefacture`, '%d/%m/%Y') AS `datefacture`, ".
			"DATE_FORMAT(DATE_ADD(`factures_etal`.`datefacture`, INTERVAL 31 DAY), '%d/%m/%Y') AS `datelimite`, ".
			"`factures_etal`.`validation`, `factures_etal`.`communeid`, `factures_etal`.`idclient`, `factures_etal`.`obs` AS `periode`, ".
			"`mandataires`.`mandataireprefix`, `mandataires`.`mandataireRS`, ".
			"`mandataires`.`mandatairenom`, `mandataires`.`mandataireprenom`, ".
			"`mandataires`.`mandatairebp`, `mandataires`.`mandatairecp`, `mandataires`.`mandataireville`, `mandataires`.`mandatairecommune`, ".
			"`mandataires`.`mandatairepays`, `mandataires`.`aroa`, `mandataires`.`quartier`, `mandataires`.`mandatairetelephone`, `mandataires`.`mandatairetelephone2` ".
			"FROM `factures_etal` INNER JOIN `mandataires` ON `factures_etal`.`idclient`=`mandataires`.`mandataireid` ".
			"WHERE `factures_etal`.`idfacture` = $idfacture";

		$result = $mysqli->query($query);
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$datefacture = $row['datefacture'];
				$nofacture = $row['communeid'];
				$periode = html_entity_decode($row['periode'],ENT_QUOTES, "ISO-8859-1");
				$facturevalidation = $row['validation'];
				$client = $row['mandatairenom']." ".$row['mandataireprenom'];
				$client = strtoupper($client);
				$contact1 = "BP ".$row['mandatairebp']." / ".$row['mandatairecp']." ".$row['mandataireville'];
				$contact2 = $row['aroa']." ".$row['quartier'];
				$telephone = "Téléphone : ".$row['mandatairetelephone'];
				$fax = "Vini : ".$row['mandatairetelephone2'];
				$datelimite = $row['datelimite'];
				$idclient = $row['idclient'];
				$rs = $row['mandataireprefix']." ".$row['mandataireRS'];
				}
		$result->close();
		
		//next get information on details of facture
		$query = "SELECT `factures_etal_details`.`quant`, `tarifs_etal`.`Type`, `tarifs_etal`.`MontantFCP`, `tarifs_etal`.`MontantEURO`, `tarifs_etal`.`Unite`, `tarifs_etal`.`Delib`, `tarifs_etal`.`Datedelib` FROM `factures_etal_details` LEFT JOIN `tarifs_etal` ON `factures_etal_details`.`idtarif` = `tarifs_etal`.`IDtarif` WHERE `factures_etal_details`.`idfacture` = $idfacture";
		
		$result = $mysqli->query($query);
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
				array_push($details_array, $row);
				}
				
		$d=$details_array[0]['Delib'];
		switch($d){
			case "114-2012":
				$delib = "La présente facture est conforme à la délibération n°114/2012 du 24 avril 2012 fixant la tarification des droits de voirie, de stationnement et de dépôt sur la voie publique du territoire de la commune de Faa'a, et des droits de place et d'étal sur le marché municipal et le centre artisanal.";
				break;
			case "03-2011":
				$delib = "La présente facture est conforme à la délibération n°03/2011 du 02 mars 2011 fixant la tarification des droits de voirie, de stationnement et de dépôt sur la voie publique du territoire de la commune de Faa'a, et des droits de place et d'étal sur le marché municipal et le centre artisanal.";
				break;
		}
		
		$result->close();
		
		$duplicata = get_duplicata_status($idfacture,'factures_etal');
		
		$solde = get_solde($idfacture,$idclient,'factures_etal');
	break;

	case "amarrage":
		$titlefacture = "Facturation d'Amarrage";
		$delib = "La présente facture est conforme à la délibération n°46/2011 du 30 août 2011 adoptant les modalités d'organisation et de fonctionnement de la marina de Vaitupa.";
		$typeclient = get_typeclient($idfacture);
		
		
		if($typeclient=="C"){
		//first get information of facture
		$query = "SELECT DATE_FORMAT(`factures_amarrage`.`datefacture`, '%d/%m/%Y') AS `datefacture`, ".
			"DATE_FORMAT(DATE_ADD(`factures_amarrage`.`datefacture`, INTERVAL 31 DAY), '%d/%m/%Y') AS `datelimite`, ".
			"`factures_amarrage`.`validation`, `factures_amarrage`.`communeid`, `factures_amarrage`.`idclient`, `factures_amarrage`.`obs` AS `periode`, ".
			"`factures_amarrage`.`PY`, `factures_amarrage`.`lieu`, `factures_amarrage`.`navire`,  `factures_amarrage`.`edt`,  `factures_amarrage`.`eau`, ".
			"`clients`.`clientcivilite`, ".
			"`clients`.`clientnom`, `clients`.`clientnommarital`, `clients`.`clientprenom`, `clients`.`clientprenom2`, ".
			"`clients`.`clientbp`, `clients`.`clientcp`, `clients`.`clientville`, `clients`.`clientcommune`, ".
			"`clients`.`clientpays`, `clients`.`clienttelephone`, `clients`.`clientfax`, `clients`.`aroa`, `clients`.`quartier` ".
			"FROM `factures_amarrage` INNER JOIN `clients` ON `factures_amarrage`.`idclient`=`clients`.`clientid` ".
			"WHERE `factures_amarrage`.`idfacture` = $idfacture";
		$result = $mysqli->query($query);
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$datefacture = $row['datefacture'];
				$nofacture = $row['communeid'];
				$periode = html_entity_decode($row['periode'],ENT_QUOTES, "ISO-8859-1");
				$facturevalidation = $row['validation'];
				$client = html_entity_decode($row['clientnom']." ".$row['clientprenom'],ENT_QUOTES, "ISO-8859-1");
				$client = strtoupper($client);
				$contact1 = "BP ".$row['clientbp']." - ".$row['clientcp']." ".$row['clientville'];
				$contact2 = $row['aroa']." / ".$row['quartier'];
				$telephone = "Téléphone : ".$row['clienttelephone'];
				$fax = "Fax : ".$row['clientfax'];
				$datelimite = $row['datelimite'];
				$idclient = $row['idclient'];
				$py = $row['PY'];
				$lieu = $row['lieu'];
				$nav = $row['navire'];
				$edt = $row['edt'];
				$eau = $row['eau'];
				}
		$result->close();
		}else{
		//first get information of facture
		$query = "SELECT DATE_FORMAT(`factures_amarrage`.`datefacture`, '%d/%m/%Y') AS `datefacture`, ".
			"DATE_FORMAT(DATE_ADD(`factures_amarrage`.`datefacture`, INTERVAL 31 DAY), '%d/%m/%Y') AS `datelimite`, ".
			"`factures_amarrage`.`validation`, `factures_amarrage`.`communeid`, `factures_amarrage`.`idclient`, `factures_amarrage`.`obs` AS `periode`, ".
			"`factures_amarrage`.`PY`, `factures_amarrage`.`lieu`, `factures_amarrage`.`navire`,  `factures_amarrage`.`edt`,  `factures_amarrage`.`eau`, ".
			"`mandataires`.`mandataireprefix`, `mandataires`.`mandataireRS`, ".
			"`mandataires`.`mandatairenom`,`mandataires`.`mandataireprenom`, ".
			"`mandataires`.`mandatairebp`, `mandataires`.`mandatairecp`, `mandataires`.`mandataireville`, `mandataires`.`mandatairecommune`, ".
			"`mandataires`.`mandatairepays`, `mandataires`.`aroa`, `mandataires`.`quartier`, `mandataires`.`mandatairetelephone`, `mandataires`.`mandatairetelephone2`, `mandataires`.`mandatairefax` ".
			"FROM `factures_amarrage` INNER JOIN `mandataires` ON `factures_amarrage`.`idclient`=`mandataires`.`mandataireid` ".
			"WHERE `factures_amarrage`.`idfacture` = $idfacture";
		$result = $mysqli->query($query);
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$datefacture = $row['datefacture'];
				$nofacture = $row['communeid'];
				$periode = html_entity_decode($row['periode'],ENT_QUOTES, "ISO-8859-1");
				$facturevalidation = $row['validation'];
				$client = html_entity_decode($row['mandatairenom']." ".$row['mandataireprenom'],ENT_QUOTES, "ISO-8859-1");
				$client = strtoupper($client);
				$contact1 = "BP ".$row['mandatairebp']." - ".$row['mandatairecp']." ".$row['mandataireville'];
				$contact2 = $row['aroa']." / ".$row['quartier'];
				if($row['mandatairetelephone']!==""){
					$telephone = "Téléphone : ".$row['mandatairetelephone'];
				}else{
					$telephone = "Téléphone : ".$row['mandatairetelephone2'];
				}
				$fax = "Fax : ".$row['mandatairefax'];
				$datelimite = $row['datelimite'];
				$idclient = $row['idclient'];
				$py = $row['PY'];
				$lieu = $row['lieu'];
				$nav = $row['navire'];
				$edt = $row['edt'];
				$eau = $row['eau'];
				}
		$result->close();
		}

				
		//next get information on details of facture
		$query = "SELECT `factures_amarrage_details`.`quant`, `tarifs_amarrage`.`Type`, `tarifs_amarrage`.`MontantFCP`, `tarifs_amarrage`.`MontantEURO`, `tarifs_amarrage`.`Unite`, `tarifs_amarrage`.`Delib`, `tarifs_amarrage`.`Datedelib` FROM `factures_amarrage_details` LEFT JOIN `tarifs_amarrage` ON `factures_amarrage_details`.`idtarif` = `tarifs_amarrage`.`IDtarif` WHERE `factures_amarrage_details`.`idfacture` = $idfacture";
		
		$result = $mysqli->query($query);
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
				array_push($details_array, $row);
				}
		
		$result->close();
		
		$duplicata = get_duplicata_status($idfacture,'factures_amarrage');
		
		$solde = get_solde($idfacture,$idclient,'factures_amarrage');
	break;
}



genpdf($typefacture,$titlefacture,$datefacture,$nofacture,$destinataire,$ecole,$classe,$client,$contact1,$contact2,$telephone,$fax,$details_array,$datelimite,$facturevalidation,$zip,$periode,$delib,$rs,$py,$lieu,$nav,$avoir,$avoirobs,$edt,$eau,$duplicata,$solde);

$mysqli->close();


function genpdf($typefacture,$titlefacture,$datefacture,$nofacture,$destinataire,$ecole,$classe,$client,$contact1,$contact2,$telephone,$fax,$details_array,$datelimite,$facturevalidation,$zip,$periode,$delib,$rs,$py,$lieu,$nav,$avoir,$avoirobs,$edt,$eau,$duplicata,$solde){	
	$pdf=new FPDF('P','mm','A4');
	$pdf->AddPage();

	if($duplicata){
	$pdf->SetXY(10,100);
	$pdf->image('fpdf/watermark.jpg');
	}
	

	$xreg=-1.5;
	$yreg=3.5;
	/////////////////////////////////////en tete////////////////////////////////////////
	//logo
	$pdf->Image('img/logo.jpg',16.5+$xreg,21+$yreg,21,18,'jpg');

	//Commune de Faa'a
	//$pdf->Image('img/communedefaaa.png',38.5+$xreg,21+$yreg,50,3.2,'png');
	$pdf->AddFont('Arialb','','arialb.php');
	$pdf->SetFont('Arialb','',13);
	$pdf->SetXY(37.5+$xreg,17.5+$yreg);
	$pdf->Cell(55,10,utf8_decode('COMMUNE DE FAA\'A'));

	//Direction des affaires financière
	$pdf->SetXY(37.5+$xreg,22.5+$yreg);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(55,10,utf8_decode('Direction des Affaires Financières'));

	$pdf->SetXY(37.5+$xreg,26.5+$yreg);
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(55,10,utf8_decode('Service Facturation, Taxes et Recouvrement'));

	$pdf->SetXY(37.5+$xreg,30.2+$yreg);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(55,10,utf8_decode('Tél.: 800.954'));

	$pdf->SetXY(37.5+$xreg,33.5+$yreg);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(55,10,utf8_decode('BP 60.002 - 98702 Faa\'a Centre'));

	$pdf->SetXY(37.5+$xreg,37+$yreg);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(55,10,utf8_decode('E-mail : facturation@mairiefaaa.pf'));
	///////////////////////////////////fin en tete/////////////////////////////////////
	///////////////////////////////////colonne droite/////////////////////////////////
	//facture
	$pdf->SetLineWidth(0.4);
	//$pdf->Image('img/facture.png',156+$xreg,20.5+$yreg,23,3.2,'png');
	$pdf->SetFont('Arialb','',13);
	$pdf->SetXY(138+$xreg,17.5+$yreg);
	if($facturevalidation){
		$pdf->Cell(62,7,utf8_decode('FACTURE'),1,1,'C');
	}else{
		$pdf->Cell(62,7,utf8_decode('DEVIS'),1,1,'C');
	}
	//$pdf->Rect(138+$xreg,18.5+$yreg,62,7);

	//date d'édition
	$pdf->SetXY(139+$xreg,33+$yreg);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(55,10,utf8_decode('Date d\'édition : ').$datefacture);

	//n.facture
	$pdf->SetXY(139+$xreg,37+$yreg);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(55,10,utf8_decode('N° facture : ').$nofacture);

switch($typefacture){
	case "cantine":
	//données enfant destinataire
	$pdf->SetXY(139+$xreg,46+$yreg);
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(55,10,strtoupper(utf8_decode($destinataire)));
	$pdf->SetXY(139+$xreg,54.2+$yreg);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(55,10,"Ecole : ".$ecole);
	$pdf->SetXY(139+$xreg,58.5+$yreg);
	$pdf->Cell(55,10,"Classe : ".$classe);
	$pdf->SetLineWidth(0.4);
	$pdf->Rect(138+$xreg,48+$yreg,62,27);
	
	//données client
	$pdf->SetXY(13+$xreg,46+$yreg);
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(55,10,$client);
	$pdf->SetXY(13+$xreg,49.9+$yreg);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(55,10,utf8_decode($contact1));
	$pdf->SetXY(13+$xreg,54.2+$yreg);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(55,10,utf8_decode($contact2));
	$pdf->SetXY(13+$xreg,62.8+$yreg);
	$pdf->Cell(55,10,utf8_decode($telephone));
	$pdf->SetXY(13+$xreg,67.1+$yreg);
	$pdf->Cell(55,10,utf8_decode($fax));
	$pdf->SetLineWidth(0.4);
	$pdf->Rect(12+$xreg,48+$yreg,62,27);
	break;

	case "amarrage":
	$pdf->SetXY(139+$xreg,46+$yreg);
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(55,10,"Navire ".$nav);
	$pdf->SetXY(139+$xreg,50+$yreg);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(55,10,"Attn : ".strtoupper($client));
	$pdf->SetXY(139+$xreg,54.2+$yreg);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(55,10,utf8_decode($contact1));
	$pdf->SetXY(139+$xreg,58.4+$yreg);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(55,10,utf8_decode($contact2));
	$pdf->SetXY(139+$xreg,58.5+$yreg);
	$pdf->Cell(55,10,utf8_decode($bp));
	$pdf->SetXY(139+$xreg,62.8+$yreg);
	$pdf->Cell(55,10,utf8_decode("PY : $py/Emp. : ".$lieu));
	$pdf->SetXY(139+$xreg,67.1+$yreg);
	$pdf->Cell(55,10,utf8_decode($telephone));
	$pdf->SetLineWidth(0.4);
	$pdf->Rect(138+$xreg,48+$yreg,62,27);
	break;

	default:
	$pdf->SetXY(139+$xreg,46+$yreg);
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(55,10,$rs);
	$pdf->SetXY(139+$xreg,50+$yreg);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(55,10,"Attn : ".strtoupper($client));
	$pdf->SetXY(139+$xreg,54.2+$yreg);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(55,10,utf8_decode($contact1));
	$pdf->SetXY(139+$xreg,58.4+$yreg);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(55,10,utf8_decode($contact2));
	$pdf->SetXY(139+$xreg,58.5+$yreg);
	$pdf->Cell(55,10,utf8_decode($bp));
	$pdf->SetXY(139+$xreg,62.8+$yreg);
	$pdf->Cell(55,10,utf8_decode($telephone));
	$pdf->SetXY(139+$xreg,67.1+$yreg);
	$pdf->Cell(55,10,utf8_decode($fax));
	$pdf->SetLineWidth(0.4);
	$pdf->Rect(138+$xreg,48+$yreg,62,27);
	break;

}

	///////////////////////////////////fin colonne droite////////////////////////////
	///////////////////////////////////type de facture//////////////////////////////
	$pdf->SetXY(12+$xreg,82+$yreg);
	$pdf->SetFont('Arialb','U',13);
	$periode = str_replace("&eacute;","\351",$periode);
	switch($typefacture){
		case "cantine":
		case "etal":
		case "amarrage":	
			$pdf->Cell(186,10,utf8_decode($titlefacture." / Période : ").$periode,0,1,'C');
		break;
		default:
			$pdf->Cell(186,10,utf8_decode($titlefacture).$periode,0,1,'C');
		break;
	}


	//////////////////////////////////fin type de facture////////////////////////////
	//////////////////////////////////draw table////////////////////////////////////
	$pdf->SetLineWidth(0.4);
	$pdf->Rect(12+$xreg,94+$yreg,187.6,87);
	$pdf->Line(12.2+$xreg,104+$yreg,198,104+$yreg);
	for($count=106+$yreg;$count<180;$count+=5.5){ //compteur pour lignes horizontales
	$pdf->Line(12.2+$xreg,$count+$yreg,198,$count+$yreg);
	}
	$pdf->Line(166+$xreg,94+$yreg,166+$xreg,180.5+$yreg); //ligne vertical extreme droite
	$pdf->Line(135.5+$xreg,94+$yreg,135.5+$xreg,158.5+$yreg); //ligne PU
	$pdf->Line(118+$xreg,94+$yreg,118+$xreg,158.5+$yreg); //ligne Quantite
	$pdf->Line(100.5+$xreg,94+$yreg,100.5+$xreg,158.5+$yreg); //ligne Unité
	/////////////////////////////////fin draw table////////////////////////////////
	/////////////////////////////////details//////////////////////////////////////

	// usage $details_array[$count][column];
	$pdf->SetFont('Arial','',10);
	$ydet=0; //y position for details
	$stotal=0;
	//$solde=0;
	$total=0;
	
	switch($typefacture){
	case "cantine":
	for($count=0;$count<count($details_array);$count++){
		$pdf->SetXY(12+$xreg,104.5+$yreg+$ydet);
//		$pdf->Cell(89,5, $details_array[$count]['status']." (".$details_array[$count]['prenom'].")",0,1);
		if($details_array[$count]['valeur']>0){
			$pdf->Cell(89,5, "allocataire",0,1);
			$pdf->SetXY(12+$xreg,109.9+$yreg+$ydet);
			$pdf->Cell(89,5, html_entity_decode($details_array[$count]['status'],ENT_NOQUOTES,"cp1252"),0,1);
			$cf = $details_array[$count]['quant']*$details_array[$count]['MontantFCP'];
			$cf = $cf*$details_array[$count]['valeur']/100;
			$pdf->SetXY(166+$xreg,109.9+$yreg+$ydet);
			$pdf->Cell(32,5, "-".trispace($cf)." F",0,1,'R');

		}else{
			$pdf->Cell(89,5, html_entity_decode($details_array[$count]['status'],ENT_NOQUOTES,"cp1252"),0,1);
		}
		$pdf->SetXY(101+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(17,5, $details_array[$count]['Unite'],0,1,'C');
		$pdf->SetXY(118+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(17,5, $details_array[$count]['quant'],0,1,'C');
		$pdf->SetXY(136+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(30,5, trispace($details_array[$count]['MontantFCP']." F"),0,1,'C');
		$pdf->SetXY(166+$xreg,104.5+$yreg+$ydet);
		$soustotal = $details_array[$count]['quant']*$details_array[$count]['MontantFCP'];
		$pdf->Cell(32,5, trispace($soustotal)." F",0,1,'R');
		
		if($avoir>0){
		$ydet+=5.5;
		$pdf->SetXY(12+$xreg,109.9+$yreg+$ydet);
		$pdf->Cell(89,5, "Avoir sur facture ".$avoirobs,0,1);
		$pdf->SetXY(166+$xreg,109.9+$yreg+$ydet);
		$pdf->Cell(32,5, "-".trispace($avoir)." F",0,1,'R');
		}
		
		$stotal+=$soustotal;
		$stotal-=$cf;
		$stotal-=$avoir;
		$ydet+=5.5;
	}
	break;
	case "amarrage":
	for($count=0;$count<count($details_array);$count++){
		$pdf->SetXY(12+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(89,5, html_entity_decode($details_array[$count]['Type'],ENT_NOQUOTES,"cp1252"),0,1);
		$pdf->SetXY(101+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(17,5, $details_array[$count]['Unite'],0,1,'C');
		$pdf->SetXY(118+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(17,5, $details_array[$count]['quant'],0,1,'C');
		$pdf->SetXY(136+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(30,5, trispace($details_array[$count]['MontantFCP'])." F",0,1,'C');
		$pdf->SetXY(166+$xreg,104.5+$yreg+$ydet);
		$soustotal = $details_array[$count]['quant']*$details_array[$count]['MontantFCP'];
		$pdf->Cell(32,5, trispace($soustotal)." F",0,1,'R');
		
		if($edt>0){
		$ydet+=5.5;
		$pdf->SetXY(12+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(89,5, "Electricit\351",0,1);
		$pdf->SetXY(166+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(32,5,trispace($edt)." F",0,1,'R');
		$soustotal+=$edt;
		}
		
		if($eau>0){
		$ydet+=5.5;
		$pdf->SetXY(12+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(89,5, "Eau",0,1);
		$pdf->SetXY(166+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(32,5,trispace($eau)." F",0,1,'R');
		$soustotal+=$eau;
		}
		
		$stotal+=$soustotal;
		$ydet+=5.5;
	}
	break;
	default:
	for($count=0;$count<count($details_array);$count++){
		$pdf->SetXY(12+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(89,5, html_entity_decode($details_array[$count]['Type'],ENT_NOQUOTES,"cp1252"),0,1);
		$pdf->SetXY(101+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(17,5, $details_array[$count]['Unite'],0,1,'C');
		$pdf->SetXY(118+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(17,5, $details_array[$count]['quant'],0,1,'C');
		$pdf->SetXY(136+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(30,5, trispace($details_array[$count]['MontantFCP'])." F",0,1,'C');
		$pdf->SetXY(166+$xreg,104.5+$yreg+$ydet);
		$soustotal = $details_array[$count]['quant']*$details_array[$count]['MontantFCP'];
		$pdf->Cell(32,5, trispace($soustotal)." F",0,1,'R');
		$stotal+=$soustotal;
		$ydet+=5.5;
	}		
	break;
	}
	
	$pdf->SetXY(166+$xreg,159+$yreg);
	$pdf->Cell(32,5, utf8_decode(trispace($stotal)." F"),0,1,'R');
	$pdf->SetXY(166+$xreg,164.5+$yreg);
	$pdf->Cell(32,5, utf8_decode(trispace($solde)." F"),0,1,'R'); //solde a payer
	$pdf->SetXY(166+$xreg,170+$yreg);
	$total=$stotal+$solde;
	$pdf->Cell(32,5, utf8_decode(trispace($total)." F"),0,1,'R');
	$pdf->SetXY(166+$xreg,175.5+$yreg);
	$pdf->Cell(32,5, utf8_decode($datelimite),0,1,'R');
	/////////////////////////////////fin details//////////////////////////////////////
	////////////////////////////////title table////////////////////////////////////

	$pdf->SetXY(12+$xreg,94+$yreg);
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(89,10, utf8_decode('Désignation'),0,1,'C');
	$pdf->SetXY(101+$xreg,94+$yreg);
	$pdf->Cell(17,10, utf8_decode('Unité'),0,1,'C');
	$pdf->SetXY(118+$xreg,94+$yreg);
	$pdf->Cell(17,10, utf8_decode('Quantité'),0,1,'C');
	$pdf->SetXY(136+$xreg,94+$yreg);
	$pdf->Cell(30,10, utf8_decode('P.U.'),0,1,'C');
	$pdf->SetXY(166+$xreg,94+$yreg);
	$pdf->Cell(32,10, utf8_decode('Montant total'),0,1,'C');

	////////////////////////////////fin title table////////////////////////////////////
	////////////////////////////////sous designation////////////////////////////////////

	$pdf->SetXY(12+$xreg,159+$yreg);
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(154,5.5,utf8_decode('MONTANT TOTAL DE LA FACTURE :'),0,1,'R');
	$pdf->SetXY(12+$xreg,164.5+$yreg);
	$pdf->Cell(154,5.5,utf8_decode('SOLDE A PAYER SUR LES FACTURES PRECEDENTES :'),0,1,'R');
	$pdf->SetXY(12+$xreg,170+$yreg);
	$pdf->Cell(154,5.5,utf8_decode('MONTANT TOTAL A PAYER :'),0,1,'R');
	$pdf->SetXY(12+$xreg,175.5+$yreg);
	$pdf->Cell(154,5.5,utf8_decode('Date limite de paiement :'),0,1,'R');

	////////////////////////////////fin sous designation////////////////////////////////////
	////////////////////////////////Arrete au montant////////////////////////////////////

	$pdf->SetXY(12+$xreg,185+$yreg);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(89,10, utf8_decode('ARRETE LA PRESENTE FACTURE A LA SOMME DE :'));
	$pdf->SetXY(12+$xreg,190.5+$yreg);
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(89,10, utf8_decode(chiffre_en_lettre($total)));

	////////////////////////////////fin arrete au montant////////////////////////////////////
	////////////////////////////////information////////////////////////////////////

	$pdf->SetLineWidth(0.4);
	$pdf->Rect(12+$xreg,222+$yreg,187.6,35);
	$pdf->SetXY(12+$xreg,222+$yreg);
	$pdf->SetFont('Arial','BU',10);
	$pdf->Cell(187.6,5,'Pour votre information :',0);
	$pdf->SetXY(12+$xreg,227.5+$yreg);
	$pdf->SetFont('Arial','BI',10);
	$pdf->MultiCell(187.6,4,utf8_decode($delib),0);
		$pdf->SetXY(12+$xreg,245+$yreg);
		$pdf->SetFont('Arial','I',10);
		$pdf->MultiCell(187.6,4,utf8_decode('Merci de bien vouloir vous acquitter de la présente facture soit auprès de la Régie de la Commune de FAA\'A ou de nous faire parvenir votre règlement soit par chèque soit par virement à l\'ordre du Régisseur de recettes de la Mairie de FAA\'A, domicilié à l\'agence CCP Faa\'a centre, compte n°14168 00001 9024406F068 59'),0);

	////////////////////////////////fin information////////////////////////////////////
	
	if($zip=='1'){
		$pdf->Output("zippdf/".$nofacture.".pdf","F");
	}else{
		$pdf->Output();	
	}
}


function get_typeclient($idfacture){
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $result = $mysqli->query("SELECT `type_client` FROM `factures_amarrage` WHERE `idfacture`='$idfacture'");
        $row = $result->fetch_row();
        $type = $row[0];

	$mysqli->close();

        return $type;
}

function get_duplicata_status($idfacture,$table){
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $result = $mysqli->query("SELECT `duplicata`,`acceptation` FROM `$table` WHERE `idfacture`='$idfacture'");
        $row = $result->fetch_row();
        $status = $row[0];
	$acceptation = $row[1];
	
	if($status=='0' && $acceptation=='1'){
		$mysqli->query("UPDATE `$table` SET `duplicata`='1' WHERE `idfacture`='$idfacture'");
	}
	
	$mysqli->close();

        return $status;
}

function get_solde($idfacture,$idclient,$table,$idenfant){
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	
	if($idenfant>0){
		$result = $mysqli->query("SELECT `$table`.`restearegler`,`$table`.`idfacture`,`".$table."_details`.`idtarif` FROM `$table` INNER JOIN `".$table."_details` ON `$table`.`idfacture`=`".$table."_details`.`idfacture` WHERE `$table`.`idfacture`<'$idfacture' AND `$table`.`idclient`='$idclient' AND `$table`.`reglement`='0' AND `$table`.`acceptation`='1' AND `".$table."_details`.`idenfant`='$idenfant'");
	}else{
		$result = $mysqli->query("SELECT `restearegler`,`idfacture` FROM `$table` WHERE `idfacture`<'$idfacture' AND `idclient`='$idclient' AND `reglement`='0' AND `acceptation`='1'");
	}
        
        
	$result_array = array();
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            $result_array[] = $row;
	}
	$mysqli->close();
    
	foreach($result_array as &$value){
		$solde += $value['restearegler'];
		
		if($value['idtarif']=='15' || $value['idtarif']=='16'){ //tarif CPS 50% et CPS 100% seulement!!
			$montantpayecps = get_cps_paiements($value['idfacture']);
			if($montantpayecps==0){
				$solde -= get_bourse($value['idfacture']);
			}
		}
		
		
	}
	return $solde;
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

function get_cps_paiements($idfacture){
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $result = $mysqli->query("SELECT `montantcfp` FROM `paiements` WHERE `idfacture`='$idfacture' AND `payeur`='CPS'");
        $row = $result->fetch_row();
        $paiement = $row[0];
	
	$mysqli->close();

	return $paiement;
}

?>
