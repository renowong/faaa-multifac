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

function getInfo(){
		$id = $_GET['id'];
        $type = $_GET['type'];
		$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        
        switch($type){
            case "cantine":
            case "repas":   
                $query = "SELECT `datefacture`,`communeid`,`montantfcp`,`montanteuro`,`restearegler` FROM `".DB."`.`factures_cantine` WHERE `idfacture` = $id";
                
                $result = $Mysqli->query($query);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $output[0] = "<b>Facture $type ".$row['communeid']." du ".standarddateformat($row['datefacture'])." montant ".trispace($row['montantfcp'])."FCP (".$row['montanteuro']."&euro;)</b><br/>".
                "Reste &agrave; r&eacute;gler ".trispace($row['restearegler'])." FCP<input type='hidden' id='montantmax' value='".$row['restearegler']."'/>";
                $output[1] = strtoupper($type);
            break;
            case "etal":
                $query = "SELECT `datefacture`,`communeid`,`montantfcp`,`montanteuro`,`restearegler` FROM `".DB."`.`factures_etal` WHERE `idfacture` = $id";
        
                $result = $Mysqli->query($query);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $output[0] = "<b>Facture Place et Etal ".$row['communeid']." du ".standarddateformat($row['datefacture'])." montant ".trispace($row['montantfcp'])."FCP (".$row['montanteuro']."&euro;)</b><br/>".
                "Reste &agrave; r&eacute;gler ".trispace($row['restearegler'])." FCP<input type='hidden' id='montantmax' value='".$row['restearegler']."'/>";
                $output[1] = "PLACE ET ETAL";
            break;
        }

		$Mysqli->close();
		return $output;
}

function getAmount() {
		$id = $_GET['id'];
        $type = $_GET['type'];
		$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        
        switch($type){
            case "cantine":
            case "repas":   
                $query = "SELECT `montantfcp`,`montanteuro`,`restearegler` FROM `".DB."`.`factures_cantine` WHERE `idfacture` = $id";
        
                $result = $Mysqli->query($query);
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                        //$output = "&montantcfp=".$row['montantfcp']."&montanteuro=".$row['montanteuro']."&restearegler=".$row['restearegler'];
                        $output = $row;
                }
            break;
        
            case "etal":
                $query = "SELECT `montantfcp`,`montanteuro`,`restearegler` FROM `".DB."`.`factures_etal` WHERE `idfacture` = $id";
        
                $result = $Mysqli->query($query);
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                        //$output = "&montantcfp=".$row['montantfcp']."&montanteuro=".$row['montanteuro']."&restearegler=".$row['restearegler'];
                        $output = $row;
                }
            break;
        }
        
		$Mysqli->close();
		return $output;
}

function standarddateformat($input){
		$arr = explode('-', $input);
		return $arr[2].'-'.$arr[1].'-'.$arr[0];
}
?>
