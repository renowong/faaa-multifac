<?php
require('fpdf/fpdf.php');
require_once ('config.php');
require_once('global_functions.php');

$idfacture = $_GET['idfacture'];
$typefacture = $_GET['type'];
$table = $_GET['table'];
$montant = trispace($_GET['montant']);
$nofacture = $_GET['communeid'];
$lastrelancedate = $_GET['relancedate'];
$datefacture = reverse_date_to_normal($_GET['date']);
$chrono = updatechrono($table,$idfacture);

$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);

switch($typefacture){
	case "cantine":
	    $query = "SELECT `clients`.`clientcivilite`,`clients`.`clientnom`,`clients`.`clientprenom`,`clients`.`clientbp`,`clients`.`clientcp`,`clients`.`clientville` FROM `clients` INNER JOIN `factures_cantine` ON `factures_cantine`.`idclient` = `clients`.`clientid` WHERE `factures_cantine`.`idfacture` = '$idfacture'";
	    $result = $mysqli->query($query);
	    $row = $result->fetch_array(MYSQLI_ASSOC);
	    $client = $row["clientnom"]." ".$row["clientprenom"];
	    $contact = "BP ".$row['clientbp']." - ".$row['clientcp']." ".$row['clientville'];
	    switch($row["clientcivilite"]){
		case "Mr":
		    $civilite = "Monsieur";
		break;
		case "Mme":
		    $civilite = "Madame";
		break;
		case "Mlle":
		    $civilite = "Mademoiselle";
		break;
	    }
	    $client = $civilite." ".$client;
	    
	break;

	case "etal":
	    $query = "SELECT `mandataires`.`mandataireprefix`,`mandataires`.`mandataireRS`,`mandataires`.`mandatairenom`,`mandataires`.`mandataireprenom`,`mandataires`.`mandatairebp`,`mandataires`.`mandatairecp`,`mandataires`.`mandataireville` FROM `mandataires` INNER JOIN `factures_etal` ON `factures_etal`.`idclient` = `mandataires`.`mandataireid` WHERE `factures_etal`.`idfacture` = '$idfacture'";
	    $result = $mysqli->query($query);
	    $row = $result->fetch_array(MYSQLI_ASSOC);
	    $client = $row["mandataireprefix"]." ".$row["mandataireRS"]."\nAttn : ".$row["mandatairenom"]." ".$row["mandataireprenom"];
	    $contact = "BP ".$row['mandatairebp']." - ".$row['mandatairecp']." ".$row['mandataireville'];
	    $civilite = "Monsieur, Madame";
	break;

	case "amarrage":
		
	break;
}



genpdf($typefacture,$datefacture,$nofacture,$montant,$civilite,$client,$contact,$chrono);

$mysqli->close();


function genpdf($typefacture,$datefacture,$nofacture,$montant,$civilite,$client,$contact,$chrono){	
	$pdf=new FPDF("P","mm","A4");
	$pdf->AddPage();
	$pdf->SetMargins(20,20);
	$today = date("d/m/Y");
	$pdf->AddFont("Arialb","","arialb.php");
	//$pdf->AddFont("Courier","","arialb.php");

	/////////////////////////////////////en tete////////////////////////////////////////
	//logo
	$pdf->SetXY(18,24);
	
	$X=$pdf->GetX();
	$Y=$pdf->GetY();
	
	$pdf->Image("img/logo.jpg",$X,$Y,23,19,"jpg");

	//Commune de Faa"a
	$pdf->SetXY(42,22);
	$X=$pdf->GetX();
	$Y=$pdf->GetY();
	
    	$pdf->SetFont("Arialb","",13);

	$pdf->Cell(55,10,utf8_decode("COMMUNE DE FAA'A"));
    
	$pdf->SetXY($X+1,$Y+8);
	$pdf->SetFont("Arial","",12);
	$pdf->Cell(55,5,utf8_decode("N° $chrono/DAF/FTR-Régie-hp"),1,1,"C");
	//$X=$pdf->GetX();
	$Y=$pdf->GetY();
	$pdf->SetXY($X,$Y);
	
	$pdf->SetFont("Arial","",9);
	$pdf->Cell(55,5,utf8_decode("Affaire suivie par : Hinatini Parker"),0,1);
	
	$Y=$pdf->GetY();
	$pdf->SetXY($X,$Y);

	$pdf->SetFont("Arial","",9);
	$pdf->Cell(55,3,utf8_decode("Téléphone : 800 960 poste 421"),0,0);

	///////////////////////////////////fin en tete/////////////////////////////////////
	///////////////////////////////////colonne droite/////////////////////////////////
	
	//Date
	$pdf->SetXY($X+100,$Y);
	$pdf->SetFont("Arial","",10);
	$pdf->Cell(55,3,"Faa'a le ".$today,0);



	///////////////////////////////////fin colonne droite////////////////////////////

	/////////////////////////////////info exp/dest //////////////////////////////////////
	//Le Maire
	$pdf->SetY(60);
	$pdf->SetFont("times","",18);
	$pdf->Cell(0,10,"Le Maire",0,1,"C");
	//Destinataire
	$pdf->SetY(70);
	$pdf->SetFont("Arial","",10);
	$pdf->Multicell(0,5,utf8_decode("à\n$client\n$contact"),0,"C");
	$currentY = $pdf->GetY();
	$pdf->SetY($currentY+10);
	$pdf->SetFont("Arialb","U",10);
	$pdf->Cell(20,5,utf8_decode("Objet :"),0,0,"L");
	$pdf->SetFont("Arialb","",10);
	$pdf->Cell(0,5,utf8_decode("Relance des factures impayées"),0,1,"L");
	$pdf->SetFont("Arial","U",10);
	$pdf->Cell(20,5,utf8_decode("N/Réf :"),0,0,"L");
	$pdf->SetFont("Arial","",10);
	$pdf->Cell(0,5,utf8_decode("Facture $typefacture N° $nofacture du $datefacture"),0,1,"L");
	
	$Y = $pdf->GetY();
	$pdf->SetY($Y+20);
	
	$text = "$civilite,\n\nJe vous informe que, sauf erreur de ma part, votre extrait de compte ci-joint présente un impayé ".
	"envers la Commune de FAA'A d'un montant de $montant FCP au titre de la (des) $typefacture.\n\n".
	"Aussi, je vous demande de bien vouloir vous rapprocher de la Régie municipale pour vous acquitter de la somme due.\n\n".
	"A défaut de réponse de votre part dans un délai de 45 jours à compter de la date du présent courrier,".
	"votre dossier sera transmis en contentieux à la Trésorerie des Iles du Vent, des Australes et des Archipels pour commandement de payer.\n\n".
	"La Régie reste à votre disposition au 800.960 poste 421 pour toute entente préalable avant poursuite.\n\n".
	"Je vous prie d'agréer, $civilite, l'expression de mes salutations distinguées";
	
	$pdf->Multicell(0,5,utf8_decode($text),0,"L");
	
	$pdf->Image("img/marianne.jpg",150,220,31,31,"jpg");

	////////////////////////////////information////////////////////////////////////
	
	$pdf->Line(20,273,190,273);
	$pdf->SetXY(20,273);
	$pdf->SetFont("Arial","",7);
	$pdf->Cell(0,3,utf8_decode("PK 4 côté mer - BP 60 002 - 98702 Faa’a Centre - Tahiti / Tél. : (689) 800 960 - Télécopie : (689) 834 890 - E-mail : mairiefaaa@mail.pf
"),0,0,"C");

	////////////////////////////////fin information////////////////////////////////////
	
	
	$pdf->Output();	
	
}

function updatechrono($table,$idfacture){
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$mysqli->query("INSERT INTO `chrono_relance` (`table`,`idfacture`,`date`) VALUES ('$table','$idfacture',NOW())");
	$lastid = $mysqli->insert_id;
	$mysqli->close();
	return $lastid;
}


?>
