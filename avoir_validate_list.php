<?php
require_once('config.php');
require_once('global_functions.php');


        $output = "<div style='display: inline-block; height:inherit; overflow:auto;margin: 0px auto;'><table><tr><th>Montant</th><th>Reste</th><th>Liaison</th><th>Date</th><th>Obs</th><th>Validation/Rejet</th></tr>";
        $output .= getavoirlist();
        $output .= "</table></div>";


print $output;


####################################functions######################################


function getavoirlist(){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT `avoirs`.`montant`,`avoirs`.`reste`,`avoirs`.`validation`,`avoirs`.`idfacture`,`avoirs`.`date`,`avoirs`.`obs`,`factures_cantine`.`datefacture`,`factures_cantine`.`datefacture`,`factures_cantine`.`montantfcp`,`factures_cantine`.`montanteuro` FROM `avoirs` INNER JOIN `factures_cantine` ON `avoirs`.`idfacture`=`factures_cantine`.`idfacture` WHERE `avoirs`.`validation` = '0' ORDER BY date ASC, `idavoir`";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $type='cantine';
                $output .= "<tr><td>".trispace($row["montant"])." FCP</td><td>".trispace($row["reste"])." FCP</td>".
                "<td><a href='createpdf.php?idfacture=".$row['idfacture']."&type=$type' target='_blank'>Facture ".$row["communeid"]." du ".$row["datefacture"]." montant de ";
                $output .= trispace($row["montantfcp"]);
                $output .= " FCP (soit ".$row["montanteuro"]."&euro;)</a></td><td>".french_date($row['date'])."</td><td>".$row['obs']."</td><td></td></tr>";
        }
        return $output;
}

function french_date($timestamp){
	return date("d/m/Y",strtotime($timestamp));
}

?>