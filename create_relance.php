<?php
require('fpdf/fpdf.php');
require_once ('config.php');
require_once('global_functions.php');

$idfacture = $_GET['idfacture'];
$typefacture = $_GET['type'];
$montant = $_GET['montant'];
$nofacture = $_GET['communeid'];
$datefacture = $_GET['date'];

$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);

switch($typefacture){
	case "cantine":
		
	break;

	case "etal":
		
	break;

	case "amarrage":
		
	break;
}



genpdf($typefacture,$datefacture,$nofacture,$client,$contact1,$contact2,$telephone,$fax);

$mysqli->close();


function genpdf($typefacture,$datefacture,$nofacture,$client,$contact1,$contact2,$telephone,$fax){	
	$pdf=new FPDF('P','mm','A4');
	$pdf->AddPage();


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



	///////////////////////////////////fin colonne droite////////////////////////////

	/////////////////////////////////details//////////////////////////////////////

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
	
	
	$pdf->Output();	
	
}


?>
