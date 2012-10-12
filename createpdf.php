<?php
require('fpdf/fpdf.php');
require('chifrenlettre.php');
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
		$delib = "La présente facture est conforme à la délibération n°160/2010 du 28 août 2012 fixant le tarif des repas de cantine scolaire.";

		//first get information of facture
		$query = "SELECT DATE_FORMAT(`factures_cantine`.`datefacture`, '%d/%m/%Y') AS `datefacture`, ".
			"DATE_FORMAT(DATE_ADD(`factures_cantine`.`datefacture`, INTERVAL 31 DAY), '%d/%m/%Y') AS `datelimite`, ".
			"`factures_cantine`.`validation`, `factures_cantine`.`communeid`, `factures_cantine`.`idclient`, `factures_cantine`.`obs` AS `periode`, ".
			"`clients`.`clientcivilite`, ".
			"`clients`.`clientnom`, `clients`.`clientnommarital`, `clients`.`clientprenom`, `clients`.`clientprenom2`, ".
			"`clients`.`clientbp`, `clients`.`clientcp`, `clients`.`clientville`, `clients`.`clientcommune`, ".
			"`clients`.`clientpays`, `clients`.`clienttelephone`, `clients`.`clientfax`, `clients`.`clientemail` ".
			"FROM `factures_cantine` INNER JOIN `clients` ON `factures_cantine`.`idclient`=`clients`.`clientid` ".
			"WHERE `factures_cantine`.`idfacture` = $idfacture";
		$result = $mysqli->query($query);
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$datefacture = $row['datefacture'];
				$nofacture = $row['communeid'];
				$periode = $row['periode'];
				$facturevalidation = $row['validation'];
				$client = html_entity_decode($row['clientnom']." ".$row['clientprenom'],ENT_QUOTES, "UTF-8");
				$bp = "BP : ".$row['clientbp']." - ".$row['clientcp']." ".$row['clientville'];
				$email = "E-mail : ".$row['clientemail'];
				$telephone = "Téléphone : ".$row['clienttelephone'];
				$fax = "Fax : ".$row['clientfax'];
				$datelimite = $row['datelimite'];
				$idclient = $row['idclient'];
				}
		$result->close();
		

		//next get information on details of facture
		$query = "SELECT `factures_cantine_details`.`quant`, `status_cantine`.`status`, `status_cantine`.`MontantFCP`, `status_cantine`.`MontantEURO`, `status_cantine`.`Unite`, `status_cantine`.`Delib`, `status_cantine`.`Datedelib`, `enfants`.`prenom`, `enfants`.`enfantid` FROM `factures_cantine_details` LEFT JOIN `status_cantine` ON `factures_cantine_details`.`idtarif` = `status_cantine`.`idstatus` RIGHT JOIN `enfants` ON `factures_cantine_details`.`idenfant` = `enfants`.`enfantid` WHERE `factures_cantine_details`.`idfacture` = $idfacture";
		
		$result = $mysqli->query($query);
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
				array_push($details_array, $row);
				}
		
		$result->close();
		
		
		//next get information on the destinataire
		$query = "SELECT  `enfants`.`nom`,`enfants`.`prenom`,`enfants`.`classe`,`ecoles_faaa`.`nomecole` FROM `enfants` RIGHT JOIN `ecoles_faaa` ON `enfants`.`ecole`=`ecoles_faaa`.`ecoleid` WHERE `enfants`.`enfantid` ='".$details_array[0]['enfantid']."'";
		$result = $mysqli->query($query);
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$destinataire = $row['nom']." ".$row['prenom'];
				$destinataire = html_entity_decode($destinataire, ENT_QUOTES);
				$ecole = $row['nomecole'];
				$classe = $row['classe'];
				}
		$result->close();
				
		
	break;

	case "repas":
		$titlefacture = "Facturation Repas";
		$delib = "La présente facture est conforme à la délibération n°160/2010 du 28 août 2012 fixant le tarif des repas de cantine scolaire.";

		//first get information of facture
		$query = "SELECT DATE_FORMAT(`factures_cantine`.`datefacture`, '%d/%m/%Y') AS `datefacture`, ".
			"DATE_FORMAT(DATE_ADD(`factures_cantine`.`datefacture`, INTERVAL 31 DAY), '%d/%m/%Y') AS `datelimite`, ".
			"`factures_cantine`.`validation`, `factures_cantine`.`communeid`, `factures_cantine`.`idclient`, `factures_cantine`.`obs` AS `periode`, ".
			"`clients`.`clientcivilite`, ".
			"`clients`.`clientnom`, `clients`.`clientnommarital`, `clients`.`clientprenom`, `clients`.`clientprenom2`, ".
			"`clients`.`clientbp`, `clients`.`clientcp`, `clients`.`clientville`, `clients`.`clientcommune`, ".
			"`clients`.`clientpays`, `clients`.`clienttelephone`, `clients`.`clientfax`, `clients`.`clientemail` ".
			"FROM `factures_cantine` INNER JOIN `clients` ON `factures_cantine`.`idclient`=`clients`.`clientid` ".
			"WHERE `factures_cantine`.`idfacture` = $idfacture";
		$result = $mysqli->query($query);
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$datefacture = $row['datefacture'];
				$nofacture = $row['communeid'];
				$periode = $row['periode'];
				$facturevalidation = $row['validation'];
				$client = html_entity_decode($row['clientnom']." ".$row['clientprenom'],ENT_QUOTES, "UTF-8");
				$bp = "BP : ".$row['clientbp']." - ".$row['clientcp']." ".$row['clientville'];
				$email = "E-mail : ".$row['clientemail'];
				$telephone = "Téléphone : ".$row['clienttelephone'];
				$fax = "Fax : ".$row['clientfax'];
				$datelimite = $row['datelimite'];
				$idclient = $row['idclient'];
				}
		$result->close();
				
		//next get information on details of facture
		$query = "SELECT `factures_cantine_details`.`quant`, `status_cantine`.`status` AS `Type`, `status_cantine`.`MontantFCP`, `status_cantine`.`MontantEURO`, `status_cantine`.`Unite`, `status_cantine`.`Delib`, `status_cantine`.`Datedelib` FROM `factures_cantine_details` LEFT JOIN `status_cantine` ON `factures_cantine_details`.`idtarif` = `status_cantine`.`idstatus` WHERE `factures_cantine_details`.`idfacture` = $idfacture";
		
		$result = $mysqli->query($query);
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
				array_push($details_array, $row);
				}
		
		$result->close();
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
			"`mandataires`.`mandatairepays`, `mandataires`.`mandatairetelephone`, `mandataires`.`mandatairetelephone2`, `mandataires`.`mandataireemail` ".
			"FROM `factures_etal` INNER JOIN `mandataires` ON `factures_etal`.`idclient`=`mandataires`.`mandataireid` ".
			"WHERE `factures_etal`.`idfacture` = $idfacture";

		$result = $mysqli->query($query);
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$datefacture = $row['datefacture'];
				$nofacture = $row['communeid'];
				$periode = $row['periode'];
				$facturevalidation = $row['validation'];
				$client = $row['mandatairenom']." ".$row['mandataireprenom'];
				$bp = "BP : ".$row['mandatairebp']." - ".$row['mandatairecp']." ".$row['mandataireville'];
				$email = "E-mail : ".$row['mandataireemail'];
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
				$delib = "La présente facture est conforme à la délibération n°114/2012 du 24 avril 2012 fixant le tarif des places et droit d'étal.";
				break;
			case "03-2011":
				$delib = "La présente facture est conforme à la délibération n°03/2011 du 02 mars 2011 fixant le tarif des places et droit d'étal.";
				break;
		}
		
		$result->close();
	break;
}



genpdf($typefacture,$titlefacture,$datefacture,$nofacture,$destinataire,$ecole,$classe,$client,$bp,$email,$telephone,$fax,$details_array,$datelimite,$facturevalidation,$zip,$periode,$delib,$rs);

$mysqli->close();


function genpdf($typefacture,$titlefacture,$datefacture,$nofacture,$destinataire,$ecole,$classe,$client,$bp,$email,$telephone,$fax,$details_array,$datelimite,$facturevalidation,$zip,$periode,$delib,$rs){
	$xreg=-1.5;
	$yreg=3.5;

	$pdf=new FPDF('P','mm','A4');
	$pdf->AddPage();


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
	$pdf->SetXY(13+$xreg,54.2+$yreg);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(55,10,utf8_decode($bp));
	$pdf->SetXY(13+$xreg,58.5+$yreg);
	$pdf->Cell(55,10,utf8_decode($email));
	$pdf->SetXY(13+$xreg,62.8+$yreg);
	$pdf->Cell(55,10,utf8_decode($telephone));
	$pdf->SetXY(13+$xreg,67.1+$yreg);
	$pdf->Cell(55,10,utf8_decode($fax));
	$pdf->SetLineWidth(0.4);
	$pdf->Rect(12+$xreg,48+$yreg,62,27);
	break;

	default:
	$pdf->SetXY(139+$xreg,46+$yreg);
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(55,10,$rs);
	$pdf->SetXY(139+$xreg,50+$yreg);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(55,10,"Attn:".strtoupper($client));
	$pdf->SetXY(139+$xreg,54.2+$yreg);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(55,10,utf8_decode($bp));
	$pdf->SetXY(139+$xreg,58.5+$yreg);
	$pdf->Cell(55,10,utf8_decode($email));
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
	switch($typefacture){
		case "cantine":
		case "etal":
			$pdf->Cell(186,10,utf8_decode($titlefacture." / Période : ").html_entity_decode($periode),0,1,'C');
		break;
		default:
			$pdf->Cell(186,10,utf8_decode($titlefacture).html_entity_decode($periode),0,1,'C');
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
	$solde=0;
	$total=0;
	
	switch($typefacture){
	case "cantine":
	for($count=0;$count<count($details_array);$count++){
		$pdf->SetXY(12+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(89,5, $details_array[$count]['status']." (".$details_array[$count]['prenom'].")",0,1);
		$pdf->SetXY(101+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(17,5, $details_array[$count]['Unite'],0,1,'C');
		$pdf->SetXY(118+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(17,5, $details_array[$count]['quant'],0,1,'C');
		$pdf->SetXY(136+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(30,5, trispace($details_array[$count]['MontantFCP']." F"),0,1,'C');
		$pdf->SetXY(166+$xreg,104.5+$yreg+$ydet);
		$soustotal = $details_array[$count]['quant']*$details_array[$count]['MontantFCP'];
		$pdf->Cell(32,5, trispace($soustotal)." F",0,1,'R');
		$stotal+=$soustotal;
		$ydet+=5.5;
	}
	break;
	default:
	for($count=0;$count<count($details_array);$count++){
		$pdf->SetXY(12+$xreg,104.5+$yreg+$ydet);
		$pdf->Cell(89,5, html_entity_decode($details_array[$count]['Type']),0,1);
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
	//$pdf->Cell(32,5, utf8_decode($stotal." F"),0,1,'R'); //solde a payer
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

	$pdf->SetXY(12+$xreg,190+$yreg);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(89,10, utf8_decode('ARRETE LA PRESENTE FACTURE A LA SOMME DE :'));
	$pdf->SetXY(12+$xreg,195.5+$yreg);
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(89,10, utf8_decode(chifre_en_lettre($total)));

	////////////////////////////////fin arrete au montant////////////////////////////////////
	////////////////////////////////information////////////////////////////////////

	$pdf->SetLineWidth(0.4);
	$pdf->Rect(12+$xreg,222+$yreg,187.6,30);
	$pdf->SetXY(12+$xreg,222+$yreg);
	$pdf->SetFont('Arial','BU',10);
	$pdf->Cell(187.6,5,'Pour votre information :',0);
	$pdf->SetXY(12+$xreg,227.5+$yreg);
	$pdf->SetFont('Arial','BI',10);
	$pdf->MultiCell(187.6,4,utf8_decode($delib),0);
		$pdf->SetXY(12+$xreg,240+$yreg);
		$pdf->SetFont('Arial','I',10);
		$pdf->MultiCell(187.6,4,utf8_decode('Merci de bien vouloir vous acquitter de la présente facture soit auprès de la Régie de la Commune de FAA\'A ou de nous faire parvenir votre règlement soit par chèque soit par virement à l\'ordre du Régisseur de recettes de la Mairie de FAA\'A, domicilié à l\'agence CCP Faa\'a centre, compte n°14168 00001 9024406F068 59'),0);

	////////////////////////////////fin information////////////////////////////////////

	if($zip=='1'){
		$pdf->Output("zippdf/".$nofacture.".pdf","F");
	}else{
		$pdf->Output();	
	}
}

?>
