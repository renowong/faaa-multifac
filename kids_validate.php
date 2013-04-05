<?php
session_start();
//require_once ('error_handler.php');
require_once ('kids_validate_class.php');

$validator = new Validate();


$_SESSION['values']['txt_enfantNom'] = $_POST['txt_nom_enfant'];
$_SESSION['values']['txt_enfantPrenom'] = $_POST['txt_prenom_enfant'];
$_SESSION['values']['txt_enfantDN'] = $_POST['txt_dn_enfant'];
$_SESSION['values']['txt_enfant_entree'] = $_POST['txt_entree_enfant'];
$_SESSION['values']['txt_enfant_sortie'] = $_POST['txt_sortie_enfant'];
$_SESSION['values']['txt_enfantCPS'] = $_POST['txt_cps_enfant'];
$_SESSION['values']['slt_enfantStatus'] = $_POST['slt_status_enfant'];
$_SESSION['values']['slt_enfantStatus_Periode'] = $_POST['slt_status_periode'];
$_SESSION['values']['slt_enfantEcole'] = $_POST['slt_ecole_enfant'];
$_SESSION['values']['slt_enfantClasse'] = $_POST['slt_classe_enfant'];
$_SESSION['values']['slt_enfantSexe'] = $_POST['slt_sexe_enfant'];
$_SESSION['values']['hid_enfantid'] = $_POST['id_enfant'];
//$_SESSION['values']['chk_enfantDest'] = $_POST['chk_dest_enfant'];
//if($_SESSION['values']['chk_enfantDest']=="on"){$_SESSION['values']['chk_enfantDest']=" checked";}else{$_SESSION['values']['chk_enfantDest']="";}

if (isset($_GET['validationType'])) {
	$validationType = $_GET['validationType'];
}

if ($validationType=="php") {
	if($validator->ValidatePHP()){
		//ok
                //print $_POST['txt_nom_enfant'];
                submit_data();
	} else {
		//not ok
                $clientid = $_POST["id_client_enfant"];
		header("Location:clients.php?edit=$clientid&success=0");
	}
} else {
	$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
			'<response>'.
				'<result>'.$validator->ValidateAJAX($_POST['inputValue'], $_POST['fieldID']).'</result>'.
				'<fieldid>'.$_POST['fieldID'].'</fieldid>'.
			'</response>';
	//if(ob_get_length()) ob_clean();
	//header('Content-Type: text/xml');
	echo $response;
}

function submit_data(){
        $clientid = $_POST["id_client_enfant"];
        $enfantid = $_POST["id_enfant"];
        if($enfantid==''){$type="insert";}else{$type="update";}
        $nom = strtoupper($_POST["txt_nom_enfant"]);
        $prenom = htmlentities(strtoupper($_POST["txt_prenom_enfant"]), ENT_QUOTES, "UTF-8");
        $ecole = $_POST["slt_ecole_enfant"];
        $classe = $_POST["slt_classe_enfant"];
        $dnvalue = $_POST["txt_dn_enfant"];
        $dnvalue = explode("/", $dnvalue);
        $dn = $dnvalue[2]."-".$dnvalue[1]."-".$dnvalue[0];
	
	$entreevalue = $_POST["txt_entree_enfant"];
        $entreevalue = explode("/", $entreevalue);
        $entree = $entreevalue[2]."-".$entreevalue[1]."-".$entreevalue[0];
	$sortievalue = $_POST["txt_sortie_enfant"];
        $sortievalue = explode("/", $sortievalue);
        $sortie = $sortievalue[2]."-".$sortievalue[1]."-".$sortievalue[0];
	
        $cps = $_POST["txt_cps_enfant"];
        $sexe = $_POST["slt_sexe_enfant"];
        $status = $_POST["slt_status_enfant"];
	$status_periode = $_POST["slt_status_periode"];
        $active = $_POST["chk_actif_enfant"];
        $dest = "off";  //deprecated
        if($active=="on"){$active="1";}else{$active="0";}
        if($dest=="on"){$dest="1";}else{$dest="0";} //deprecated
	
	//determine date status expires depending on periode
	$thismonth = date("n");
	
	switch($status_periode){
		case 1:
			$status_expires = date("Y")."-12-31";
		break;
		case 2:
			if($thismonth>3){$nextyear=1;}else{$nextyear=0;}
			$status_expires = date("Y")+$nextyear."-03-31";
		break;
		case 3:
			if($thismonth>6){$nextyear=1;}else{$nextyear=0;}
			$status_expires = date("Y")."-06-30";
		break;
		default:
		$status_expires = "1997-01-01";
	}
        
    
    switch($type){
    case "insert":
	if(!check_enf_exist($cps)){
		$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
		if($dest=='1'){uncheckdest($clientid);}else{
		    if(!checkdest_exist($clientid)){$dest = '1';}
		}
		$query = "INSERT INTO `enfants` (`enfantid`,`clientid`,`nom`,`prenom`,`ecole`,`classe`,`entree`,`sortie`,`active`,`dn`,`cps`,`sexe`,`status`,`status_expires`,`status_periode`,`destinataire`) VALUES (NULL,'$clientid','$nom','$prenom','$ecole','$classe','$entree','$sortie','1','$dn','$cps','$sexe','$status','$status_expires','$status_periode','$dest')";
		$mysqli->query($query);
		$success = $mysqli->affected_rows;
		$mysqli->close();
		print $query;
	}else{
		$exist = 1;
	}
    break;
    case "update":
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        if($dest=='1'){uncheckdest($clientid);}else{
            if(!checkdest_exist($clientid,$enfantid)){$dest = '1';}
        }
        $query = "UPDATE  `enfants` SET `nom`='$nom',`prenom`='$prenom',`dn`='$dn',`entree`='$entree',`sortie`='$sortie',`cps`='$cps',`sexe`='$sexe',`status`='$status',`status_expires`='$status_expires',`status_periode`='$status_periode',`ecole`='$ecole',`classe`='$classe',`active`='$active',`destinataire`='$dest' WHERE `enfants`.`enfantid`='$enfantid'";
        $mysqli->query($query);
        $success = $mysqli->affected_rows;
        $mysqli->close();
        print $query;
    break;
    }
    
    	if ($success > 0){
		//resetenfantvalues();
		header("Location:clients.php?edit=$clientid&success=1&reset=1");
	} else {
		header("Location:clients.php?edit=$clientid&success=0&exist=$exist");
	}
}

function check_enf_exist($cps){
$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "SELECT `enfantid` FROM `enfants` WHERE `cps`='$cps'";
    $mysqli->query($query);
    $count = $mysqli->affected_rows;
    $mysqli->close();
    if($count>0){return true;}else{return false;}
}

function uncheckdest($clientid){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "UPDATE `enfants` SET `destinataire`='0' WHERE `clientid`='$clientid'";
    $mysqli->query($query);
    $mysqli->close();
    //print $query;
}

function checkdest_exist($clientid,$enfantid){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "SELECT `enfantid` FROM `enfants` WHERE `clientid`='$clientid' AND `destinataire`='1' AND NOT `enfantid`='$enfantid'";
    $mysqli->query($query);
    $count = $mysqli->affected_rows;
    $mysqli->close();
    if($count>0){return true;}else{return false;}

}
?>