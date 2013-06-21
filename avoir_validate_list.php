<?php
require_once('config.php');
require_once('global_functions.php');


$output = "<div style='display: inline-block; height:inherit; overflow:auto;margin: 0px auto;'><table><tr><th>Montant</th><th>Reste</th><th>Liaison</th><th>PDF</th><th>Date</th><th>Obs</th><th>Validation/Rejet</th></tr>";
$output .= getavoirlist();
$output .= "</table></div>";


print $output;


####################################functions######################################


function getavoirlist(){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT `avoirs`.`idavoir`,`avoirs`.`montant`,`avoirs`.`reste`,`avoirs`.`validation`,`avoirs`.`idfacture`,`avoirs`.`date`,".
        "`avoirs`.`obs`,`factures_cantine`.`datefacture`,`factures_cantine`.`datefacture`,`factures_cantine`.`montantfcp`,".
        "`factures_cantine`.`montanteuro`,`factures_cantine`.`duplicata` FROM `avoirs` INNER JOIN `factures_cantine` ON `avoirs`.`idfacture`=`factures_cantine`.`idfacture`".
        "WHERE `avoirs`.`validation` = '0' ORDER BY date ASC, `idavoir`";
        $type = "cantine";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                if($row["duplicata"]=='0'){$pdf="<img src=\"img/opdf.png\" alt=\"original\" class=\"ico\">";}else{$pdf="<img src=\"img/dpdf.png\" alt=\"duplicata\" class=\"ico\">";}
                $output .= "<tr><td>".trispace($row["montant"])." FCP</td><td>".trispace($row["reste"])." FCP</td>".
                "<td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</td><td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'>$pdf</a></td><td>".french_date($row['date'])."</td>".
                "<td>".$row['obs']."</td><td style=\"text-align:center\"><a href=\"javascript:validate('".$row["idavoir"]."',true)\">".
                "<img src=\"img/checked.png\" alt=\"checked\" height=\"32\" style=\"border:0px\"></a> / <a href=\"javascript:validate('".$row["idavoir"]."',false)\">".
                "<img src=\"img/close.png\" alt=\"close\" height=\"32\" style=\"border:0px\"></a></td></tr>";
        }
        return $output;
}

?>