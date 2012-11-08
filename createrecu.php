<?php
require_once('checksession.php');
require('fpdf/fpdf.php');
require_once ('config.php');
require_once('global_functions.php');

$typefacture = $_GET['type'];
$cUser = unserialize($_SESSION['user']);
$prenom = $cUser->userfirstname();
$nom = $cUser->userlastname();
$initiales = substr($prenom,0,1).substr($nom,0,1);
$id = $_GET['id'];

//echo $initiales;
getinfo($id,$initiales,$typefacture);

function getinfo($id,$initiales,$typefacture){
	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    
    switch($typefacture){
        case "cantine":
        $query = "SELECT *,`paiements`.`obs` AS `observations` FROM `paiements` JOIN `factures_cantine` ON `paiements`.`idfacture`=`factures_cantine`.`idfacture` WHERE `idpaiement`= $id";
        break;
        case "etal":
        $query = "SELECT *,`paiements`.`obs` AS `observations` FROM `paiements` JOIN `factures_etal` ON `paiements`.`idfacture`=`factures_etal`.`idfacture` WHERE `idpaiement`= $id";    
        break;
	case "amarrage":
        $query = "SELECT *,`paiements`.`obs` AS `observations` FROM `paiements` JOIN `factures_amarrage` ON `paiements`.`idfacture`=`factures_amarrage`.`idfacture` WHERE `idpaiement`= $id";    
        break;
    }
	$result = $Mysqli->query($query);
	$row = $result->fetch_array(MYSQLI_ASSOC);
	$date = $row['date_paiement'];
	$payeur = $row['payeur'];
	$type = $row['type'];
	$mode = $row['mode'];
	$montant = $row['montantcfp'];
	$idpaiement = $row['idpaiement'];
	$idfacture = $row['communeid'];
	$no_cheque = $row['numero_cheque'];
	$organisme = $row['organisme'];
	$obs = $row['observations'];
	$Mysqli->close();
	
	genpdf($idfacture,$idpaiement,$date,$payeur,$type,$mode,$montant,$initiales,$no_cheque,$organisme,$obs);	
}



function genpdf($idfacture,$idpaiement,$date,$payeur,$type,$mode,$montant,$initiales,$no_cheque,$organisme,$obs){
	$xreg=1.5;
	$yreg=3.5;

	$pdf=new FPDF('P','mm','A4');
	$pdf->AddPage();


	//logo
	$pdf->Image('img/logo.jpg',10+$xreg,14+$yreg,12,10,'jpg');
	
	$pdf->SetLineWidth(0.4);
	$pdf->Rect(4+$xreg,7+$yreg,196,30); //rectangle global
	$pdf->Line(4+$xreg,13+$yreg,200+$xreg,13+$yreg); //1ere barre horizontale
	$pdf->Line(27+$xreg,7+$yreg,27+$xreg,37+$yreg); //1ere barre verticale
	$pdf->Line(77+$xreg,7+$yreg,77+$xreg,37+$yreg);//2nd barre verticale
	$pdf->Line(127+$xreg,7+$yreg,127+$xreg,37+$yreg);//3eme barre verticale
	$pdf->Line(152+$xreg,7+$yreg,152+$xreg,37+$yreg);//4eme barre verticale
	$pdf->Line(177+$xreg,7+$yreg,177+$xreg,37+$yreg);//5eme barre verticale
	
	$pdf->SetLineWidth(0.2);
	$pdf->Line(27+$xreg,21+$yreg,200+$xreg,21+$yreg); //1ere barre fine horizontale
	$pdf->Line(4+$xreg,31+$yreg,200+$xreg,31+$yreg);//2nd barre fine horizontale

	$pdf->SetFont('Arial','',8);
	$pdf->SetXY(4+$xreg,7+$yreg);
	$pdf->Cell(23,7, utf8_decode('DATE'),0,1,'C');
	$pdf->SetXY(27+$xreg,7+$yreg);
	$pdf->Cell(50,7, utf8_decode('NOM de la partie versante'),0,1,'C');
	$pdf->SetXY(27+$xreg,13+$yreg);
	$pdf->Cell(50,7, utf8_decode('Reçu de M.'),0,1,'');
	$pdf->SetXY(77+$xreg,7+$yreg);
	$pdf->Cell(50,7, utf8_decode('DESIGNATION DES PRODUITS'),0,1,'C');
	$pdf->SetXY(127+$xreg,7+$yreg);
	$pdf->Cell(25,3.5, utf8_decode('VERSEMENT'),0,1,'C');
	$pdf->SetXY(127+$xreg,9.5+$yreg);
	$pdf->Cell(25,3.5, utf8_decode('en numéraire'),0,1,'C');
	$pdf->SetXY(152+$xreg,7+$yreg);
	$pdf->Cell(25,7, utf8_decode('CHEQUES'),0,1,'C');
	$pdf->SetXY(177+$xreg,7+$yreg);
	$pdf->Cell(25,7, utf8_decode('DIVERS'),0,1,'C');
	
	//data
	$pdf->SetXY(4+$xreg,25+$yreg);
	$pdf->Cell(23,7,"No. ".$idpaiement,0,1,'C');
	$pdf->SetXY(4+$xreg,31+$yreg);
	$pdf->Cell(23,7,standarddateformat($date),0,1,'C');
	$pdf->SetXY(27+$xreg,31+$yreg);
	$pdf->Cell(50,7, utf8_decode($obs),0,1,'');
	$pdf->SetXY(27+$xreg,21+$yreg);
	$pdf->Cell(50,10, strtoupper(utf8_decode($payeur)),0,1,'C');
	switch($mode){
		case 'num':
			$pdf->SetXY(125+$xreg+$xreg,21+$yreg);
		break;
		case 'chq':
			$pdf->SetXY(150+$xreg+$xreg,19+$yreg);
		break;
		default:
			$pdf->SetXY(175+$xreg+$xreg,21+$yreg);
		break;
	}
	$pdf->Cell(25,10, utf8_decode(trispace($montant).' FCP'),0,1,'C');
	$pdf->SetXY(150+$xreg+$xreg,23+$yreg);
	$pdf->Cell(25,10, utf8_decode($organisme.' '.$no_cheque),0,1,'C');
	$pdf->SetXY(77+$xreg+$xreg,13+$yreg);
	$pdf->Cell(50,10, utf8_decode('Facture n° '.$idfacture),0,1,'C');
	$pdf->SetXY(77+$xreg+$xreg,21+$yreg);
	$pdf->Cell(50,10, utf8_decode($type),0,1,'C');
	$pdf->SetXY(77+$xreg+$xreg,29+$yreg);
	$pdf->Cell(50,10, utf8_decode('Receveur '.$initiales),0,1,'C');
	
	$pdf->Output();
}

function standarddateformat($input){
		$arr = explode('-', $input);
		return $arr[2].'-'.$arr[1].'-'.$arr[0];
}

?>
