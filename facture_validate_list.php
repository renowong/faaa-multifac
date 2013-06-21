<?php
require_once('config.php');
require_once('global_functions.php');

$list = $_GET['validlist'];
$type = $_GET['type'];

if(isset($_GET['client'])){
     $client = " AND `idclient`='".$_GET['client']."'";
}

if($list){
        $output = "<div style='display: inline-block; height:inherit; overflow:auto;margin: 0px auto;'><table><tr><th>Type de Facture</th><th>Client</th><th width=500px>Facture</th><th>PDF</th><th>D&eacute;validation</th></tr>";
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
}else{
        $output = "<div style='display: inline-block; height:inherit; overflow:auto;margin: 0px auto;'><table><tr><th>Type de Facture</th><th>Client</th><th width=500px>Devis</th><th>PDF</th><th>Validation/Rejet</th></tr>";
        switch($type){
                case "cantine":
                        $output .= getcantinevalidate($client);
                break;
                case "etal":
                        $output .= getetalvalidate($client);
                break;
                case "amarrage":
                        $output .= getamarragevalidate($client);
                break;
                default:
                        $output .= getcantinevalidate($client);
                        $output .= getetalvalidate($client);
                        $output .= getamarragevalidate($client);
                break;
        }    
        $output .= "</table></div>";
}

print $output;


####################################functions######################################

function getetalvalidate($client){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `".DB."`.`factures_etal` INNER JOIN `mandataires` ON `factures_etal`.`idclient` = `mandataires`.`mandataireid` WHERE `factures_etal`.`validation` = '0'$client";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $output .= "<tr><td>place et &eacute;tal</td><td><a href='mandataires.php?edit=".$row["mandataireid"]."&hideerrors=1'>".$row["mandataireprefix"]." ".$row["mandataireRS"]." / ".htmlentities($row["mandatairenom"])." ".htmlentities($row["mandataireprenom"])."</a></td>".
                "<td>Devis ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</td><td style=\"text-align:center\"><a href='createpdf.php?idfacture=".$row['idfacture']."&type=etal' target='_blank'><img src=\"img/pdf.png\" alt=\"pdf\" class=\"ico\"></a></td>".
                "<td style=\"text-align:center\"><a href=\"javascript:validate('etal','".$row["idfacture"]."',true)\">".
                "<img src=\"img/checked.png\" alt=\"checked\" height=\"32\" style=\"border:0px\"></a> / <a href=\"javascript:validate('etal','".$row["idfacture"]."',false)\">".
                "<img src=\"img/close.png\" alt=\"close\" height=\"32\" style=\"border:0px\"></a></td></tr>";
        }
        return $output;
}

function getetallist($client){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `".DB."`.`factures_etal` INNER JOIN `mandataires` ON `factures_etal`.`idclient` = `mandataires`.`mandataireid` WHERE `factures_etal`.`validation` = '1' AND `acceptation` = '1' AND `reglement` = '0'$client";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                if($row["duplicata"]=='0'){$pdf="<img src=\"img/opdf.png\" alt=\"original\" class=\"ico\">";}else{$pdf="<img src=\"img/dpdf.png\" alt=\"duplicata\" class=\"ico\">";}
                $type='etal';
                $output .= "<tr><td>place et &eacute;tal</td><td><a href='mandataires.php?edit=".$row["mandataireid"]."&hideerrors=1'>".$row["mandataireprefix"]." ".$row["mandataireRS"]." / ".htmlentities($row["mandatairenom"])." ".htmlentities($row["mandataireprenom"])."</a></td>".
                "<td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</td><td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'>$pdf</a></td><td class='center'><a href=\"javascript:devalidate('$type','".$row["idfacture"]."')\"><img src=\"img/close.png\" alt=\"close\" height=\"32\" style=\"border:0px\"></a></td></tr>";
        }
        return $output;
}

function getamarragevalidate($client){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `".DB."`.`factures_amarrage` INNER JOIN `clients` ON `factures_amarrage`.`idclient` = `clients`.`clientid` WHERE `factures_amarrage`.`validation` = '0' AND `factures_amarrage`.`type_client` = 'C'$client";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $type='amarrage';
                $output .= "<tr><td>$type<br/>".$row["navire"]."</td><td><a href='clients.php?edit=".$row["clientid"]."&hideerrors=1'>".$row["clientcivilite"]." ".strtoupper(htmlentities($row["clientnom"]))." ".strtoupper(htmlentities($row["clientprenom"]))." ".strtoupper(htmlentities($row["clientprenom2"]))."</a></td>".
                "<td>Devis ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</td><td style=\"text-align:center\"><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'><img src=\"img/pdf.png\" alt=\"pdf\" class=\"ico\"></a></td>".
                "<td style=\"text-align:center\"><a href=\"javascript:validate('$type','".$row["idfacture"]."',true)\">".
                "<img src=\"img/checked.png\" alt=\"checked\" height=\"32\" style=\"border:0px\"></a> / <a href=\"javascript:validate('$type','".$row["idfacture"]."',false)\">".
                "<img src=\"img/close.png\" alt=\"close\" height=\"32\" style=\"border:0px\"></a></td></tr>";
        }
        
        $query = "SELECT * FROM `".DB."`.`factures_amarrage` INNER JOIN `mandataires` ON `factures_amarrage`.`idclient` = `mandataires`.`mandataireid` WHERE `factures_amarrage`.`validation` = '0' AND `factures_amarrage`.`type_client` = 'M'$client";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $type='amarrage';
                $output .= "<tr><td>$type</td><td><a href='mandataires.php?edit=".$row["mandataireid"]."&hideerrors=1'>".$row["mandataireprefix"]." ".$row["mandataireRS"]." ".strtoupper(htmlentities($row["mandatairenom"]))." ".strtoupper(htmlentities($row["mandataireprenom"]))."</a></td>".
                "<td>Devis ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</td><td style=\"text-align:center\"><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'><img src=\"img/pdf.png\" alt=\"pdf\" class=\"ico\"></a></td>".
                "<td style=\"text-align:center\"><a href=\"javascript:validate('$type','".$row["idfacture"]."',true)\">".
                "<img src=\"img/checked.png\" alt=\"checked\" height=\"32\" style=\"border:0px\"></a> / <a href=\"javascript:validate('$type','".$row["idfacture"]."',false)\">".
                "<img src=\"img/close.png\" alt=\"close\" height=\"32\" style=\"border:0px\"></a></td></tr>";
        }
        
        return $output;
}

function getamarragelist($client){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `".DB."`.`factures_amarrage` INNER JOIN `clients` ON `factures_amarrage`.`idclient` = `clients`.`clientid` WHERE `factures_amarrage`.`validation` = '1' AND `acceptation` = '1' AND `reglement` = '0'$client";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                if($row["duplicata"]=='0'){$pdf="<img src=\"img/opdf.png\" alt=\"original\" class=\"ico\">";}else{$pdf="<img src=\"img/dpdf.png\" alt=\"duplicata\" class=\"ico\">";}
                $type='amarrage';
                $output .= "<tr><td>$type<br/>".$row["navire"]."</td><td><a href='clients.php?edit=".$row["clientid"]."&hideerrors=1'>".$row["clientcivilite"]." ".strtoupper(htmlentities($row["clientnom"]))." ".strtoupper(htmlentities($row["clientprenom"]))." ".strtoupper(htmlentities($row["clientprenom2"]))."</a></td>".
                "<td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</td><td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'>$pdf</a></td><td class='center'><a href=\"javascript:devalidate('$type','".$row["idfacture"]."')\"><img src=\"img/close.png\" alt=\"close\" height=\"32\" style=\"border:0px\"></a></td></tr>";
        }
        return $output;
}

function getcantinevalidate($client){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `".DB."`.`factures_cantine` INNER JOIN `clients` ON `factures_cantine`.`idclient` = `clients`.`clientid` WHERE `factures_cantine`.`validation` = '0'$client";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $type='cantine';
                $enfant_prenom = "<br/>".getEnfantPrenom($row['idfacture']);
                $output .= "<tr><td>$type$enfant_prenom</td><td><a href='clients.php?edit=".$row["clientid"]."&hideerrors=1'>".$row["clientcivilite"]." ".strtoupper(htmlentities($row["clientnom"]))." ".strtoupper(htmlentities($row["clientprenom"]))." ".strtoupper(htmlentities($row["clientprenom2"]))."</a></td>".
                "<td>Devis ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</td><td style=\"text-align:center\"><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'><img src=\"img/pdf.png\" alt=\"pdf\" class=\"ico\"></a></td>".
                "<td style=\"text-align:center\"><a href=\"javascript:validate('$type','".$row["idfacture"]."',true)\">".
                "<img src=\"img/checked.png\" alt=\"checked\" height=\"32\" style=\"border:0px\"></a> / <a href=\"javascript:validate('$type','".$row["idfacture"]."',false)\">".
                "<img src=\"img/close.png\" alt=\"close\" height=\"32\" style=\"border:0px\"></a></td></tr>";
        }
        return $output;
}

function getcantinelist($client){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `".DB."`.`factures_cantine` INNER JOIN `clients` ON `factures_cantine`.`idclient` = `clients`.`clientid` WHERE `factures_cantine`.`validation` = '1' AND `acceptation` = '1' AND `reglement` = '0'$client";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                if($row["duplicata"]=='0'){$pdf="<img src=\"img/opdf.png\" alt=\"original\" class=\"ico\">";}else{$pdf="<img src=\"img/dpdf.png\" alt=\"duplicata\" class=\"ico\">";}
                $type='cantine';
                $enfant_prenom = "<br/>".getEnfantPrenom($row['idfacture']);
                $output .= "<tr><td>$type$enfant_prenom</td><td><a href='clients.php?edit=".$row["clientid"]."&hideerrors=1'>".$row["clientcivilite"]." ".strtoupper(htmlentities($row["clientnom"]))." ".strtoupper(htmlentities($row["clientprenom"]))." ".strtoupper(htmlentities($row["clientprenom2"]))."</td>".
                "<td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</a></td><td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'>$pdf</a></td>".
                "<td class='center'><a href=\"javascript:devalidate('$type','".$row["idfacture"]."')\"><img src=\"img/close.png\" alt=\"close\" height=\"32\" style=\"border:0px\"></a></td></tr>";
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