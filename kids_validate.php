<?php
session_start();
//require_once ('error_handler.php');
require_once ('kids_validate_class.php');

$validator = new Validate();


$_SESSION['values']['txt_enfantNom'] = $_POST['txt_nom_enfant'];
$_SESSION['values']['txt_enfantPrenom'] = $_POST['txt_prenom_enfant'];
$_SESSION['values']['txt_enfantDN'] = $_POST['txt_dn_enfant'];
$_SESSION['values']['txt_enfantCPS'] = $_POST['txt_cps_enfant'];
$_SESSION['values']['slt_enfantStatus'] = $_POST['slt_status_enfant'];
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
        $cps = $_POST["txt_cps_enfant"];
        $sexe = $_POST["slt_sexe_enfant"];
        $status = $_POST["slt_status_enfant"];
        $active = $_POST["chk_actif_enfant"];
        $dest = "off";  //deprecated
        if($active=="on"){$active="1";}else{$active="0";}
        if($dest=="on"){$dest="1";}else{$dest="0";} //deprecated
        
    
    switch($type){
    case "insert":
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    if($dest=='1'){uncheckdest($clientid);}else{
            if(!checkdest_exist($clientid)){$dest = '1';}
        }
        $query = "INSERT INTO `".DB."`.`enfants` (`enfantid`,`clientid`,`nom`,`prenom`,`ecole`,`classe`,`active`,`dn`,`cps`,`sexe`,`status`,`destinataire`) VALUES (NULL,'$clientid','$nom','$prenom','$ecole','$classe','1','$dn','$cps','$sexe','$status','$dest')";
        $mysqli->query($query);
        $success = $mysqli->affected_rows;
        $mysqli->close();
        print $query;
    break;
    case "update":
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        if($dest=='1'){uncheckdest($clientid);}else{
            if(!checkdest_exist($clientid,$enfantid)){$dest = '1';}
        }
        $query = "UPDATE  `".DB."`.`enfants` SET `nom`='$nom',`prenom`='$prenom',`dn`='$dn',`cps`='$cps',`sexe`='$sexe',`status`='$status',`ecole`='$ecole',`classe`='$classe',`active`='$active',`destinataire`='$dest' WHERE `enfants`.`enfantid` =$enfantid";
        $mysqli->query($query);
        $success = $mysqli->affected_rows;
        $mysqli->close();
        print $query;
    break;
    }
    
    	//if ($success > 0){
		//resetenfantvalues();
		header("Location:clients.php?edit=$clientid&success=1&reset=1");
	//} else {
	//	header("Location:clients.php?edit=$clientid&success=0");
	//}
}



function uncheckdest($clientid){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "UPDATE `".DB."`.`enfants` SET `destinataire`='0' WHERE `clientid`='$clientid'";
    $mysqli->query($query);
    $mysqli->close();
    //print $query;
}

function checkdest_exist($clientid,$enfantid){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "SELECT `enfantid` FROM `".DB."`.`enfants` WHERE `clientid`='$clientid' AND `destinataire`='1' AND NOT `enfantid`='$enfantid'";
    $mysqli->query($query);
    $count = $mysqli->affected_rows;
    $mysqli->close();
    if($count>0){return true;}else{return false;}

}
?>