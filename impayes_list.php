<?php
require_once('config.php');
require_once('global_functions.php');

$type = $_GET['type'];

if(isset($_GET['client'])){
     $client = " AND `idclient`='".$_GET['client']."'";
}

        $output = "<div style='display: inline-block; height:inherit; overflow:auto;margin: 0px auto;'><table><tr><th>Type de Facture</th><th>Client</th><th width=500px>Facture</th><th>Retard</th></tr>";
        switch($type){
                case "cantine":
                        $output .= getcantinelist($client);
                break;
                case "etal":
                        $output .= getetallist($client);
                break;
                case "amarrage":
                        $output .= getamarragelist($client);
                break;
                default:
                        $output .= getcantinelist($client);
                        $output .= getetallist($client);
                        $output .= getamarragelist($client);
                break;
        }  
        $output .= "</table></div>";      

print $output;


####################################functions######################################

function getetallist($client){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `factures_etal` INNER JOIN `mandataires` ON `factures_etal`.`idclient` = `mandataires`.`mandataireid` WHERE `factures_etal`.`validation` = '1' AND `acceptation` = '1' AND `reglement` = '0' AND `datefacture` < DATE_SUB(NOW(), INTERVAL 30 DAY)$client";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $type='etal';
                $output .= "<tr><td>place et &eacute;tal</td><td><a href='mandataires.php?edit=".$row["mandataireid"]."&hideerrors=1'>".$row["mandataireprefix"]." ".$row["mandataireRS"]." / ".htmlentities($row["mandatairenom"])." ".htmlentities($row["mandataireprenom"])."</a></td>".
                "<td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</a></td><td class='center'>jours de retard</td></tr>";
        }
        return $output;
}


function getamarragelist($client){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `factures_amarrage` INNER JOIN `clients` ON `factures_amarrage`.`idclient` = `clients`.`clientid` WHERE `factures_amarrage`.`validation` = '1' AND `acceptation` = '1' AND `reglement` = '0' AND `datefacture` < DATE_SUB(NOW(), INTERVAL 30 DAY)$client";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $type='amarrage';
                $output .= "<tr><td>$type<br/>".$row["navire"]."</td><td><a href='clients.php?edit=".$row["clientid"]."&hideerrors=1'>".$row["clientcivilite"]." ".strtoupper(htmlentities($row["clientnom"]))." ".strtoupper(htmlentities($row["clientprenom"]))." ".strtoupper(htmlentities($row["clientprenom2"]))."</a></td>".
                "<td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</a></td><td class='center'>jours de retard</td></tr>";
        }
        return $output;
}

function getcantinelist($client){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `factures_cantine` INNER JOIN `clients` ON `factures_cantine`.`idclient` = `clients`.`clientid` WHERE `factures_cantine`.`validation` = '1' AND `acceptation` = '1' AND `reglement` = '0' AND `datefacture` < DATE_SUB(NOW(), INTERVAL 30 DAY)$client";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                
                $datef = $row["datefacture"];
                $dayslate = strtotime("-1 month") - strtotime($datef);
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