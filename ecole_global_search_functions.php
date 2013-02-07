<?php
require_once('config.php');
require_once ('chrono.php'); //chrono update

$type = $_POST["type"];
$print0 = $_POST["print0"];
$period = $_POST["period"];

switch($type){
    case 'load':
        $ecole = $_POST["ecole"];
        $classe = $_POST["classe"];
        print load($ecole,$classe);
    break;
    case 'gfacture':
        $ids = $_POST["ids"];

        foreach ($ids as &$value){
            $rawdata = get_child_data($value);
            $ar_data = explode("/",$rawdata);
            $fdata = $ar_data[0];
            $clientid = $ar_data[1];
            //print $fdata;
            $factureids .= enterdata($fdata,$clientid,$print0,$period).",";
        }
        
        $factureids = substr($factureids,0,-1);
        print $factureids;

    break;
}


///////////////functions////////////////

//////function copy from facture_cantine_submit.php///////////
function enterdata($fdata,$clientid,$print0,$period){
$totalfcp = 0;
$detail = explode("#",$fdata);
$totalfcp = ($detail[1]*$detail[2]);
$totaleuro = $totalfcp/120;
$totaleuro = round($totaleuro, 2);
$today = date("Y-m-d");

	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
		$query = "SELECT `chrono` FROM `".DB."`.`chrono` ORDER BY `id` DESC LIMIT 1;";
		$result = $mysqli->query($query);
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$chrono = ($row["chrono"])+1;
		
                
		$query = "INSERT INTO `".DB."`.`chrono` (`chrono`) VALUES ('".$chrono."')";
		$mysqli->query($query);
	
		$query = "INSERT INTO `".DB."`.`factures_cantine` (`idfacture`, `idclient`,".
				 " `datefacture`, `communeid`, `montantfcp`, `montanteuro`, `restearegler`,`validation`,`date_validation`,`acceptation`,`obs`)".
				 " VALUES (NULL, '".$clientid."', '".$today."', '".$chrono."', '".$totalfcp."', '".$totaleuro."', '".$totalfcp."', '1', '".$today."', '1', '".$period."')";
	//return $query;
	$mysqli->query($query);
	$lastid = $mysqli->insert_id; //use it to insert the details.

// insert details now
		$query = "INSERT INTO `".DB."`.`factures_cantine_details` (`iddetail`, `idfacture`,".
				" `idtarif`, `quant`, `idenfant`)".
				" VALUES (NULL, '".$lastid."', '".$detail[0]."', '".$detail[1]."', '".$detail[3]."')";
		$mysqli->query($query);

//if total 0 then set facture paid

        if($totalfcp=="0"){
            $query = "UPDATE `".DB."`.`factures_cantine` SET `reglement`='1', `datereglement`='$today' WHERE `idfacture`='$lastid'";
            $mysqli->query($query);
        }
        
        $mysqli->close();
	if($print0=="true" || $totalfcp>0) return $lastid;
}


function get_child_data($id){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "SELECT `enfants`.`enfantid`,`enfants`.`clientid`,`enfants`.`status`,`status_cantine`.`MontantFCP` FROM `enfants` ".
            "INNER JOIN `status_cantine` ON `enfants`.`status`=`status_cantine`.`idstatus` WHERE `enfants`.`enfantid`='$id'";
    $result = $mysqli->query($query);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $output = $row["status"]."#1#".$row["MontantFCP"]."#".$row["enfantid"]."/".$row["clientid"];
    $mysqli->close();
    return $output;
}


function load($ecole,$classe){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "SELECT `nom`,`prenom`,`enfantid`,`clientid` FROM `enfants` WHERE `ecole`='$ecole' AND `classe`='$classe' AND `active`='1' ORDER BY `nom`";
    $result = $mysqli->query($query);
        $output = "<table id=\"tbl_results\"><tr>";
        $i = 0;
        $j = 4; //max columns
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $output .= "<td class=\"tdcheck\" id=\"td".$row["enfantid"]."\"><input type=\"checkbox\" name=\"".$row["enfantid"]."\" id=\"".$row["enfantid"]."\"/>".
                "<a href='clients.php?edit=".$row["clientid"]."&hideerrors=1'>".strtoupper(htmlentities($row["nom"]))." ".ucwords(htmlentities($row["prenom"]))."</a>".
                "</td>";
                $i++;
                if($i==$j) {$output .= "</tr><tr>";$i=0;}
            }
        if($i==$j) {$output .= "</table>";}else{$output .= "</td></tr></table>";}
    $mysqli->close();
    return $output;
}

?>