<?php
require_once('checksession.php'); //already includes config.php

//###############################procedures#####################################
if (!empty($_SESSION['client'])) {
                $arCompte = getCompteDisplay();
                $arCompte = preg_split("/,/", $arCompte);
        }


//###############################variables######################################
$info = getInfo();

//#################################building forms################################


//#################################functions#####################################

function Modes(){
		$mode = getMode();
		$output = "<option value=\"0\">S&eacute;lectionner</option>";
		switch($mode){
				case "1":
				case "15":		
				$output .= "<option value=\"22cf\">CF 100% CPS</option>";	
				break;
				
				case "3":
				case "17":		
				$output .= "<option value=\"22bc\">2/2 Bourse Commune</option>";	
				break;
		
				case "2":
				case "16":		
				$output .= "<option value=\"num\">Num&eacute;raire</option>";
				$output .= "<option value=\"chq\">Ch&egrave;que</option>";
				$output .= "<option value=\"vir\">Virement</option>";
				$output .= "<option value=\"tsr\">Tr&eacute;sor</option>";
				$output .= "<option value=\"mnd\">Mandat</option>";
				$output .= "<option value=\"tpe\">TPE</option>";
				$output .= "<option value=\"12cf\">CF 50% CPS</option>";	
				break;
		
				case "4":
				case "18":		
				$output .= "<option value=\"num\">Num&eacute;raire</option>";
				$output .= "<option value=\"chq\">Ch&egrave;que</option>";
				$output .= "<option value=\"vir\">Virement</option>";
				$output .= "<option value=\"tsr\">Tr&eacute;sor</option>";
				$output .= "<option value=\"mnd\">Mandat</option>";
				$output .= "<option value=\"tpe\">TPE</option>";
				$output .= "<option value=\"12bc\">1/2 Bourse Commune</option>";	
				break;
				
				default:
				$output .= "<option value=\"num\">Num&eacute;raire</option>";
				$output .= "<option value=\"chq\">Ch&egrave;que</option>";
				$output .= "<option value=\"vir\">Virement</option>";
				$output .= "<option value=\"tsr\">Tr&eacute;sor</option>";
				$output .= "<option value=\"mnd\">Mandat</option>";
				$output .= "<option value=\"tpe\">TPE</option>";
		}
		
		return $output;
}

function getMode(){
		$id = $_GET['id'];
		$type = $_GET['type'];
		$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
		
		switch($type){
			    case "cantine":
				$query = "SELECT `idtarif` FROM `factures_cantine_details` WHERE `idfacture` = $id";
				
				$result = $mysqli->query($query);
				$row = $result->fetch_array(MYSQLI_ASSOC);
				$output = $row['idtarif'];
			    break;
			   
			    default:
				$output = "0";
			}

		$mysqli->close();
		return $output;
}


function getInfo(){
		$id = $_GET['id'];
		$type = $_GET['type'];
		$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        
        switch($type){
            case "repas":
		$output[0] = "<b>Encaissement au comptant (Ticket Repas)</b><br/>";
                $output[1] = strtoupper($type);
            break;		
            case "cantine":
                $query = "SELECT `datefacture`,`communeid`,`montantfcp`,`montanteuro`,`restearegler` FROM `".DB."`.`factures_cantine` WHERE `idfacture` = $id";
                
                $result = $mysqli->query($query);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $output[0] = "<b>Facture $type ".$row['communeid']." du ".standarddateformat($row['datefacture'])." montant ".trispace($row['montantfcp'])."FCP (".$row['montanteuro']."&euro;)</b><br/>".
                "Reste &agrave; r&eacute;gler ".trispace($row['restearegler'])." FCP<input type='hidden' id='montantmax' value='".$row['restearegler']."'/><input type='hidden' id='montanttotal' value='".$row['montantfcp']."'/>";
                $output[1] = strtoupper($type);
            break;
            case "etal":
                $query = "SELECT `datefacture`,`communeid`,`montantfcp`,`montanteuro`,`restearegler` FROM `".DB."`.`factures_etal` WHERE `idfacture` = $id";
        
                $result = $mysqli->query($query);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $output[0] = "<b>Facture Place et Etal ".$row['communeid']." du ".standarddateformat($row['datefacture'])." montant ".trispace($row['montantfcp'])."FCP (".$row['montanteuro']."&euro;)</b><br/>".
                "Reste &agrave; r&eacute;gler ".trispace($row['restearegler'])." FCP<input type='hidden' id='montantmax' value='".$row['restearegler']."'/><input type='hidden' id='montanttotal' value='".$row['montantfcp']."'/>";
                $output[1] = "PLACE ET ETAL";
            break;
	    case "amarrage":
                $query = "SELECT `datefacture`,`communeid`,`montantfcp`,`montanteuro`,`restearegler` FROM `".DB."`.`factures_amarrage` WHERE `idfacture` = $id";
        
                $result = $mysqli->query($query);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $output[0] = "<b>Facture Amarrage ".$row['communeid']." du ".standarddateformat($row['datefacture'])." montant ".trispace($row['montantfcp'])."FCP (".$row['montanteuro']."&euro;)</b><br/>".
                "Reste &agrave; r&eacute;gler ".trispace($row['restearegler'])." FCP<input type='hidden' id='montantmax' value='".$row['restearegler']."'/><input type='hidden' id='montanttotal' value='".$row['montantfcp']."'/>";
                $output[1] = "AMARRAGE";
            break;
        }

		$mysqli->close();
		return $output;
}

function getAmount() {
		$id = $_GET['id'];
        $type = $_GET['type'];
		$mysqli = new Mysqli(DBSERVER, DBUSER, DBPWD, DB);
        
        switch($type){
            case "cantine":
            case "repas":   
                $query = "SELECT `montantfcp`,`montanteuro`,`restearegler` FROM `".DB."`.`factures_cantine` WHERE `idfacture` = $id";
        
                $result = $mysqli->query($query);
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                        //$output = "&montantcfp=".$row['montantfcp']."&montanteuro=".$row['montanteuro']."&restearegler=".$row['restearegler'];
                        $output = $row;
                }
            break;
        
            case "etal":
                $query = "SELECT `montantfcp`,`montanteuro`,`restearegler` FROM `".DB."`.`factures_etal` WHERE `idfacture` = $id";
        
                $result = $mysqli->query($query);
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                        //$output = "&montantcfp=".$row['montantfcp']."&montanteuro=".$row['montanteuro']."&restearegler=".$row['restearegler'];
                        $output = $row;
                }
            break;
	    case "amarrage":
                $query = "SELECT `montantfcp`,`montanteuro`,`restearegler` FROM `".DB."`.`factures_amarrage` WHERE `idfacture` = $id";
        
                $result = $mysqli->query($query);
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                        //$output = "&montantcfp=".$row['montantfcp']."&montanteuro=".$row['montanteuro']."&restearegler=".$row['restearegler'];
                        $output = $row;
                }
            break;
        }
        
		$mysqli->close();
		return $output;
}

function standarddateformat($input){
		$arr = explode('-', $input);
		return $arr[2].'-'.$arr[1].'-'.$arr[0];
}
?>
