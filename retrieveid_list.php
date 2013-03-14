<?php
require_once ('config.php');


    $form = $_POST['form'];
    $active = $_POST['active'];
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    
    switch($form){
        case "clients":
            $query = "SELECT `clientid`, `clientnom`, `clientprenom`, `clientdatenaissance` FROM `clients` WHERE `clientstatus`='$active' ORDER BY clientnom";
            $result = $mysqli->query($query);
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                print '<option style="text-align:left" value="'.$row['clientid'].'">'.strtoupper($row['clientnom']).", ".strtoupper($row['clientprenom']).", ".date("d-m-Y",strtotime($row[clientdatenaissance])).", (".$row[clientid].')</option>';
            }
        break;
        
        case "mandataires":
            $query = "SELECT `mandatairenom`, `mandataireprenom`, `mandataireid`, `mandataireidtresor` FROM `mandataires` WHERE `mandatairestatus`='$active' ORDER BY `mandatairenom`";
            $result = $mysqli->query($query);
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                print '<option style="text-align:left" value="'.$row['mandataireid'].'">'.strtoupper($row['mandatairenom']).", ".strtoupper($row['mandataireprenom']).", ID Tr&eacute;sor : ".$row['mandataireidtresor'].'</option>';
            }
        break;
    
        case "enfants":
            $query = "SELECT `enfants`.`nom`, `enfants`.`prenom`, `enfants`.`dn`, `enfants`.`classe`, `ecoles_faaa`.`nomecole`, `enfants`.`clientid` FROM `enfants` INNER JOIN `ecoles_faaa` ON `enfants`.`ecole`=`ecoles_faaa`.`ecoleid` WHERE `enfants`.`active`='$active' ORDER BY `enfants`.`nom`";
            $result = $mysqli->query($query);
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                print '<option style="text-align:left" value="'.$row['clientid'].'">'.strtoupper($row['nom']).", ".strtoupper($row['prenom']).", ".date("d-m-Y",strtotime($row['dn'])).", ".$row['nomecole'].", ".$row['classe'].'</option>';
            }
        break;
    }

    $mysqli->close();

?>
