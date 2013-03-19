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

        $output = "<div style='display: inline-block; height:inherit; overflow:auto;margin: 0px auto;'><table><tr><th>Type de Facture</th><th>Client</th><th width=500px>Facture</th><th>Retard</th></tr>";
        switch($type){
                case "cantine":
                        $output .= getcantinelist($client,$range);
                break;
                case "etal":
                        $output .= getetallist($client,$range);
                break;
                case "amarrage":
                        $output .= getamarragelist($client,$range);
                break;
                default:
                        $output .= getcantinelist($client,$range);
                        $output .= getetallist($client,$range);
                        $output .= getamarragelist($client,$range);
                break;
        }  
        $output .= "</table></div>";      

print $output;


####################################functions######################################

function getetallist($client,$range){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `factures_etal` INNER JOIN `mandataires` ON `factures_etal`.`idclient` = `mandataires`.`mandataireid` WHERE `factures_etal`.`validation` = '1' AND `acceptation` = '1' AND `reglement` = '0' $range$client";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            
                $datef = $row["datefacture"];
                $dayslate = strtotime("now") - strtotime($datef);
                $dayslate = floor($dayslate/86400);
                
                $type='etal';
                $output .= "<tr><td>place et &eacute;tal</td><td><a href='mandataires.php?edit=".$row["mandataireid"]."&hideerrors=1'>".$row["mandataireprefix"]." ".$row["mandataireRS"]." / ".htmlentities($row["mandatairenom"])." ".htmlentities($row["mandataireprenom"])."</a></td>".
                "<td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</a></td><td class='center'>$dayslate jours</td></tr>";
        }
        return $output;
}


function getamarragelist($client,$range){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `factures_amarrage` INNER JOIN `clients` ON `factures_amarrage`.`idclient` = `clients`.`clientid` WHERE `factures_amarrage`.`validation` = '1' AND `acceptation` = '1' AND `reglement` = '0' $range$client";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            
                $datef = $row["datefacture"];
                $dayslate = strtotime("now") - strtotime($datef);
                $dayslate = floor($dayslate/86400);
            
                $type='amarrage';
                $output .= "<tr><td>$type<br/>".$row["navire"]."</td><td><a href='clients.php?edit=".$row["clientid"]."&hideerrors=1'>".$row["clientcivilite"]." ".strtoupper(htmlentities($row["clientnom"]))." ".strtoupper(htmlentities($row["clientprenom"]))." ".strtoupper(htmlentities($row["clientprenom2"]))."</a></td>".
                "<td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</a></td><td class='center'>$dayslate jours</td></tr>";
        }
        return $output;
}

function getcantinelist($client,$range){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `factures_cantine` INNER JOIN `clients` ON `factures_cantine`.`idclient` = `clients`.`clientid` WHERE `factures_cantine`.`validation` = '1' AND `acceptation` = '1' AND `reglement` = '0' $range$client";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                
                $datef = $row["datefacture"];
                $dayslate = strtotime("now") - strtotime($datef);
                $dayslate = floor($dayslate/86400);
                
                $type='cantine';
                $enfant_prenom = "<br/>".getEnfantPrenom($row['idfacture']);
                $output .= "<tr><td>$type$enfant_prenom</td><td><a href='clients.php?edit=".$row["clientid"]."&hideerrors=1'>".$row["clientcivilite"]." ".strtoupper(htmlentities($row["clientnom"]))." ".strtoupper(htmlentities($row["clientprenom"]))." ".strtoupper(htmlentities($row["clientprenom2"]))."</a></td>".
                "<td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</a></td>".
                "<td class='center'>$dayslate jours</td></tr>";
        }
        return $output;
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

?>