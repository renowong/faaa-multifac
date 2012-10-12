<?php
require_once('config.php');
require_once('global_functions.php');

$list = $_GET['validlist'];
$type = $_GET['type'];

if($list){
        $output = "<div style='display: inline-block; height:inherit; overflow:auto;margin: 0px auto;'><table><tr><th>Type de Facture</th><th>Client</th><th width=500px>Facture</th></tr>";
        switch($type){
                case "cantine":
                        $output .= getcantinelist();
                break;
                case "etal":
                        $output .= getetallist();
                break;
                default:
                        $output .= getcantinelist();
                        $output .= getetallist();
                break;
        }  
        $output .= "</table></div>";      
}else{
        $output = "<div style='display: inline-block; height:inherit; overflow:auto;margin: 0px auto;'><table><tr><th>Type de Facture</th><th>Client</th><th width=500px>Facture</th><th>Validation/Rejet</th></tr>";
        switch($type){
                case "cantine":
                        $output .= getcantinevalidate();
                break;
                case "etal":
                        $output .= getetalvalidate();
                break;
                default:
                        $output .= getcantinevalidate();
                        $output .= getetalvalidate(); 
                break;
        }    
        $output .= "</table></div>";
}

print $output;


####################################functions######################################

function getetalvalidate(){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `".DB."`.`factures_etal` INNER JOIN `mandataires` ON `factures_etal`.`idclient` = `mandataires`.`mandataireid` WHERE `factures_etal`.`validation` = '0'";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $output .= "<tr><td>place et &eacute;tal</td><td><a href='mandataires.php?edit=".$row["mandataireid"]."&hideerrors=1'>".$row["mandataireprefix"]." ".$row["mandataireRS"]." / ".htmlentities($row["mandatairenom"])." ".htmlentities($row["mandataireprenom"])."</a></td>".
                "<td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=etal' target='_blank'>Devis ".$row["communeid"]." du ".$row["datefacture"]." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</a></td>".
                "<td style=\"text-align:center\"><a href=\"javascript:validate('etal','".$row["idfacture"]."',true)\">".
                "<img src=\"img/checked.png\" height=\"32\" style=\"border:0px\"></a> / <a href=\"javascript:validate('etal','".$row["idfacture"]."',false)\">".
                "<img src=\"img/close.png\" height=\"32\" style=\"border:0px\"></a></td></tr>";
        }
        return $output;
}

function getcantinevalidate(){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `".DB."`.`factures_cantine` INNER JOIN `clients` ON `factures_cantine`.`idclient` = `clients`.`clientid` WHERE `factures_cantine`.`validation` = '0'";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                if($row['repas']=='1'){$type='repas';}else{$type='cantine';}
                $output .= "<tr><td>$type</td><td><a href='clients.php?edit=".$row["clientid"]."&hideerrors=1'>".$row["clientcivilite"]." ".strtoupper(htmlentities($row["clientnom"]))." ".strtoupper(htmlentities($row["clientprenom"]))." ".strtoupper(htmlentities($row["clientprenom2"]))."</a></td>".
                "<td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'>Devis ".$row["communeid"]." du ".$row["datefacture"]." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</a></td>".
                "<td style=\"text-align:center\"><a href=\"javascript:validate('$type','".$row["idfacture"]."',true)\">".
                "<img src=\"img/checked.png\" height=\"32\" style=\"border:0px\"></a> / <a href=\"javascript:validate('$type','".$row["idfacture"]."',false)\">".
                "<img src=\"img/close.png\" height=\"32\" style=\"border:0px\"></a></td></tr>";
        }
        return $output;
}

function getcantinelist(){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `".DB."`.`factures_cantine` INNER JOIN `clients` ON `factures_cantine`.`idclient` = `clients`.`clientid` WHERE `factures_cantine`.`validation` = '1' AND `acceptation` = '1' AND `reglement` = '0'";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                if($row['repas']=='1'){$type='repas';}else{$type='cantine';}
                $output .= "<tr><td>$type</td><td><a href='clients.php?edit=".$row["clientid"]."&hideerrors=1'>".$row["clientcivilite"]." ".strtoupper(htmlentities($row["clientnom"]))." ".strtoupper(htmlentities($row["clientprenom"]))." ".strtoupper(htmlentities($row["clientprenom2"]))."</a></td>".
                "<td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'>Facture ".$row["communeid"]." du ".$row["datefacture"]." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</a></td></tr>";
        }
        return $output;
}

function getetallist(){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT * FROM `".DB."`.`factures_etal` INNER JOIN `mandataires` ON `factures_etal`.`idclient` = `mandataires`.`mandataireid` WHERE `factures_etal`.`validation` = '1' AND `acceptation` = '1' AND `reglement` = '0'";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $output .= "<tr><td>place et &eacute;tal</td><td><a href='mandataires.php?edit=".$row["mandataireid"]."&hideerrors=1'>".$row["mandataireprefix"]." ".$row["mandataireRS"]." / ".htmlentities($row["mandatairenom"])." ".htmlentities($row["mandataireprenom"])."</a></td>".
                "<td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=etal' target='_blank'>Facture ".$row["communeid"]." du ".$row["datefacture"]." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</a></td></tr>";
        }
        return $output;
}

?>