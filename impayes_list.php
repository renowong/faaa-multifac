<?php
require_once('config.php');
require_once('global_functions.php');

$type = $_GET['type'];
$set_range = $_GET['range'];

if(isset($_GET['client'])){
     $client = " AND `idclient`='".$_GET['client']."'";
}

switch($set_range){
    case "0":
        $range = " AND `datefacture` < DATE_SUB(NOW(), INTERVAL 30 DAY)";    
    break;
    case "1":
        $range = " AND (`datefacture` BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND DATE_SUB(NOW(), INTERVAL 30 DAY))";    
    break;
    case "2":
        $range = " AND (`datefacture` BETWEEN DATE_SUB(NOW(), INTERVAL 90 DAY) AND DATE_SUB(NOW(), INTERVAL 60 DAY))";    
    break;
    default:
        $range = " AND `datefacture` < DATE_SUB(NOW(), INTERVAL 90 DAY)";    
    break;
}

        $output = "<div style='display: inline-block; height:inherit; overflow:auto;margin: 0px auto;'><table><tr><th>Type de Facture</th><th>Client</th><th width=500px>Facture</th><th>PDF</th><th>Retard</th><th>Courrier</th></tr>";
        switch($type){
                case "cantine":
                        //$output .= getcantinelist($client,$range);
			list($listc,$ar_ids_cantine)= getcantinelist($client,$range);
			$output .= $listc;
                break;
                case "etal":
                        //$output .= getetallist($client,$range);
			list($liste,$ar_ids_etal)= getetallist($client,$range);
			$output .= $liste;
                break;
                case "amarrage":
                        //$output .= getamarragelist($client,$range);
			list($lista,$ar_ids_amarrage)= getamarragelist($client,$range);
			$output .= $lista;
                break;
                default:
			//$output .= getcantinelist($client,$range);
			//$output .= getetallist($client,$range);
                        //$output .= getamarragelist($client,$range);
			list($listc,$ar_ids_cantine)= getcantinelist($client,$range);
			list($liste,$ar_ids_etal)= getetallist($client,$range);
			list($lista,$ar_ids_amarrage)= getamarragelist($client,$range);
			$output .= $listc;
			$output .= $liste;
			$output .= $lista;
                break;
        }
	
        $output .= "</table>";
	foreach($ar_ids_cantine as &$id){
	    $ids_cantine .= $id.",";
	}
	foreach($ar_ids_etal as &$id){
	    $ids_etal .= $id.",";
	}
	foreach($ar_ids_amarrage as &$id){
	    $ids_amarrage .= $id.",";
	}
	$ids_cantine = substr($ids_cantine,0,strlen($ids_cantine)-1);
	$ids_etal = substr($ids_etal,0,strlen($ids_etal)-1);
	$ids_amarrage = substr($ids_amarrage,0,strlen($ids_amarrage)-1);
	$output .= "<input type='hidden' id='ids_cantine' name='ids_cantine' value='$ids_cantine' />";
	$output .= "<input type='hidden' id='ids_etal' name='ids_etal' value='$ids_etal' />";
	$output .= "<input type='hidden' id='ids_amarrage' name='ids_amarrage' value='$ids_amarrage' />";
	$output .= "</div>";      
	$output .= "<br/><button onClick=\"javascript:tocsv();\">Exporter</button>";
print $output;


####################################functions######################################

function getetallist($client,$range){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `factures_etal` INNER JOIN `mandataires` ON `factures_etal`.`idclient` = `mandataires`.`mandataireid` WHERE `factures_etal`.`validation` = '1' AND `acceptation` = '1' AND `reglement` = '0' $range$client ORDER BY `datefacture` ASC";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            	if($row["duplicata"]=='0'){$pdf="<img src=\"img/opdf.png\" alt=\"original\" class=\"ico\">";}else{$pdf="<img src=\"img/dpdf.png\" alt=\"duplicata\" class=\"ico\">";}

                $datef = $row["datefacture"];
                $dayslate = strtotime("now") - strtotime($datef);
                $dayslate = floor($dayslate/86400);
                
                $type='etal';
		
		$relance_date = getRelance($row['idfacture']);
		if(isset($relance_date)) {
		    $relance_date_fr=french_date($relance_date);
		    $titlerelance="Derni&egrave;re relance le ".$relance_date_fr;
		}else{
		    $relance_date_fr="";
		    $titlerelance="";
		}
		
                $output .= "<tr><td>place et &eacute;tal</td><td>MANDATAIRE : <a href='mandataires.php?edit=".$row["mandataireid"]."&hideerrors=1'>".$row["mandataireprefix"]." ".$row["mandataireRS"]." / ".htmlentities($row["mandatairenom"])." ".htmlentities($row["mandataireprenom"])."</a></td>".
                "<td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</td><td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'>$pdf</a></td><td class='center'>$dayslate jours</td>".
		//"<td>$relance_date_fr<br/><a onclick='javascript:relance_warning(\"$relance_date_fr\");' href='create_relance.php?idfacture=".$row['idfacture']."&type=$type&table=factures_etal&montant=".$row["montantfcp"]."&communeid=".$row["communeid"]."&date=".$row["datefacture"]."&relancedate=$relance_date' target='_blank'><img src='img/gmail.png' /></a></td></tr>";
		"<td>$relance_date_fr<br/><a href='javascript:relance_warning(\"$relance_date_fr\",\"create_relance.php?idfacture=".$row['idfacture']."&type=$type&table=factures_etal&montant=".$row["montantfcp"]."&communeid=".$row["communeid"]."&date=".$row["datefacture"]."&relancedate=$relance_date\");'><img src='img/gmail.png' /></a></td></tr>";
        
		$ar_ids[].=$row["idfacture"];
	}
	$mysqli->close();
        return array($output,$ar_ids);
}


function getamarragelist($client,$range){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `factures_amarrage` INNER JOIN `clients` ON `factures_amarrage`.`idclient` = `clients`.`clientid` WHERE `factures_amarrage`.`validation` = '1' AND `acceptation` = '1' AND `reglement` = '0' AND `type_client`='C' $range$client ORDER BY `datefacture` ASC";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                if($row["duplicata"]=='0'){$pdf="<img src=\"img/opdf.png\" alt=\"original\" class=\"ico\">";}else{$pdf="<img src=\"img/dpdf.png\" alt=\"duplicata\" class=\"ico\">";}

                $datef = $row["datefacture"];
                $dayslate = strtotime("now") - strtotime($datef);
                $dayslate = floor($dayslate/86400);
            
                $type='amarrage';
		
		$relance_date = getRelance($row['idfacture']);
		if(isset($relance_date)) {
		    $relance_date_fr=french_date($relance_date);
		    $titlerelance="Derni&egrave;re relance le ".$relance_date_fr;
		}else{
		    $relance_date_fr="";
		    $titlerelance="";
		}
		
                $output .= "<tr><td>$type<br/>".$row["navire"]."</td><td><a href='clients.php?edit=".$row["clientid"]."&hideerrors=1'>".$row["clientcivilite"]." ".strtoupper(htmlentities($row["clientnom"]))." ".strtoupper(htmlentities($row["clientprenom"]))." ".strtoupper(htmlentities($row["clientprenom2"]))."</a></td>".
                "<td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</td><td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'>$pdf</a></td><td class='center'>$dayslate jours</td>".
		//"<td>$relance_date_fr<br/><a onclick='javascript:relance_warning(\"$relance_date_fr\");' href='create_relance.php?idfacture=".$row['idfacture']."&type=$type&table=factures_amarrage&montant=".$row["montantfcp"]."&communeid=".$row["communeid"]."&date=".$row["datefacture"]."&relancedate=$relance_date' target='_blank'><img src='img/gmail.png' /></a></td></tr>";
		"<td>$relance_date_fr<br/><a href='javascript:relance_warning(\"$relance_date_fr\",\"create_relance.php?idfacture=".$row['idfacture']."&type=$type&table=factures_amarrage&montant=".$row["montantfcp"]."&communeid=".$row["communeid"]."&date=".$row["datefacture"]."&relancedate=$relance_date\");'><img src='img/gmail.png' /></a></td></tr>";
		
		$ar_ids[].=$row["idfacture"];
	}
	
        $query = "SELECT * FROM `factures_amarrage` INNER JOIN `mandataires` ON `factures_amarrage`.`idclient` = `mandataires`.`mandataireid` WHERE `factures_amarrage`.`validation` = '1' AND `acceptation` = '1' AND `reglement` = '0' AND `type_client`='M' $range$mandataire ORDER BY `datefacture` ASC";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                if($row["duplicata"]=='0'){$pdf="<img src=\"img/opdf.png\" alt=\"original\" class=\"ico\">";}else{$pdf="<img src=\"img/dpdf.png\" alt=\"duplicata\" class=\"ico\">";}

                $datef = $row["datefacture"];
                $dayslate = strtotime("now") - strtotime($datef);
                $dayslate = floor($dayslate/86400);
            
                $type='amarrage';
		
		$relance_date = getRelance($row['idfacture']);
		if(isset($relance_date)) {
		    $relance_date_fr=french_date($relance_date);
		    $titlerelance="Derni&egrave;re relance le ".$relance_date_fr;
		}else{
		    $relance_date_fr="";
		    $titlerelance="";
		}
		
                $output .= "<tr><td>$type<br/>".$row["navire"]."</td><td>MANDATAIRE : <a href='mandataires.php?edit=".$row["mandataireid"]."&hideerrors=1'>".$row["mandatairecivilite"]." ".strtoupper(htmlentities($row["mandatairenom"]))." ".strtoupper(htmlentities($row["mandataireprenom"]))." ".strtoupper(htmlentities($row["mandataireprenom2"]))."</a></td>".
                "<td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</td><td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'>$pdf</a></td><td class='center'>$dayslate jours</td>".
		//"<td>$relance_date_fr<br/><a onclick='javascript:relance_warning(\"$relance_date_fr\");' href='create_relance.php?idfacture=".$row['idfacture']."&type=$type&table=factures_amarrage&montant=".$row["montantfcp"]."&communeid=".$row["communeid"]."&date=".$row["datefacture"]."&relancedate=$relance_date' target='_blank'><img src='img/gmail.png' /></a></td></tr>";
		"<td>$relance_date_fr<br/><a href='javascript:relance_warning(\"$relance_date_fr\",\"create_relance.php?idfacture=".$row['idfacture']."&type=$type&table=factures_amarrage&montant=".$row["montantfcp"]."&communeid=".$row["communeid"]."&date=".$row["datefacture"]."&relancedate=$relance_date\");'><img src='img/gmail.png' title='$titlerelance'/></a></td></tr>";
		
		$ar_ids[].=$row["idfacture"];
	}
	
	$mysqli->close();
        return array($output,$ar_ids);
}

function getcantinelist($client,$range){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `factures_cantine` INNER JOIN `clients` ON `factures_cantine`.`idclient` = `clients`.`clientid` WHERE `factures_cantine`.`validation` = '1' AND `acceptation` = '1' AND `reglement` = '0' AND `bourse` = '0' $range$client ORDER BY `datefacture` ASC";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                if($row["duplicata"]=='0'){$pdf="<img src=\"img/opdf.png\" alt=\"original\" class=\"ico\">";}else{$pdf="<img src=\"img/dpdf.png\" alt=\"duplicata\" class=\"ico\">";}

                $datef = $row["datefacture"];
                $dayslate = strtotime("now") - strtotime($datef);
                $dayslate = floor($dayslate/86400);
                
                $type='cantine';
                $enfant_prenom = "<br/>".getEnfantPrenom($row['idfacture']);
		
		$relance_date = getRelance($row['idfacture']);
		if(isset($relance_date)) {
		    $relance_date_fr=french_date($relance_date);
		    $titlerelance="Derni&egrave;re relance le ".$relance_date_fr;
		}else{
		    $relance_date_fr="";
		    $titlerelance="";
		}
		
                $output .= "<tr><td>$type$enfant_prenom</td><td><a href='clients.php?edit=".$row["clientid"]."&hideerrors=1'>".$row["clientcivilite"]." ".strtoupper(htmlentities($row["clientnom"]))." ".strtoupper(htmlentities($row["clientprenom"]))." ".strtoupper(htmlentities($row["clientprenom2"]))."</a></td>".
                "<td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</td><td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'>$pdf</a></td>".
                //"<td class='center'>$dayslate jours</td><td>$relance_date_fr<br/><a onclick='javascript:relance_warning(\"$relance_date_fr\");' href='create_relance.php?idfacture=".$row['idfacture']."&type=$type&table=factures_cantine&montant=".$row["montantfcp"]."&communeid=".$row["communeid"]."&date=".$row["datefacture"]."&relancedate=$relance_date' target='_blank'><img src='img/gmail.png' title='$titlerelance'/></a></td></tr>";
                "<td class='center'>$dayslate jours</td><td>$relance_date_fr<br/><a href='javascript:relance_warning(\"$relance_date_fr\",\"create_relance.php?idfacture=".$row['idfacture']."&type=$type&table=factures_cantine&montant=".$row["montantfcp"]."&communeid=".$row["communeid"]."&date=".$row["datefacture"]."&relancedate=$relance_date\");'><img src='img/gmail.png' title='$titlerelance'/></a></td></tr>";
		
		$ar_ids[].=$row["idfacture"];
        }
	$mysqli->close();
        //return $output;
	//print_r($ar_ids);
	return array($output,$ar_ids);
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

function getRelance($idfacture){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "SELECT `date` FROM `chrono_relance` WHERE `idfacture`='$idfacture' ORDER BY `chrono` DESC LIMIT 1";
    $result = $mysqli->query($query);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $output = $row["date"];
    $mysqli->close();
    return $output;
}
?>