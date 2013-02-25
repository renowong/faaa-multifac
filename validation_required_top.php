<?php
require_once('checksession.php'); //already includes config.php

//###############################procedures#####################################
if (!empty($_SESSION['client'])) {
                $arCompte = getCompteDisplay();
                $arCompte = preg_split("/,/", $arCompte);
        }


//###############################variables######################################


//#################################building forms################################


//#################################functions#####################################

function buildOptionsPersonnes() {
	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    

            $query = "SELECT `clientid`, `clientnom`, `clientprenom`, `clientdatenaissance` FROM `".DB."`.`clients` ORDER BY clientnom";
            $result = $Mysqli->query($query);
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                echo '<option style="text-align:left" value="'.$row['clientid'].'">'.strtoupper($row['clientnom']).", ".strtoupper($row['clientprenom']).", ".date("d-m-Y",strtotime($row[clientdatenaissance])).", (".$row[clientid].')</option>';
            }

            $query = "SELECT `mandatairenom`, `mandataireprenom`, `mandataireid`, `mandataireidtresor` FROM `mandataires`";
            $result = $Mysqli->query($query);
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                echo '<option style="text-align:left" value="'.$row['mandataireid'].'">'.strtoupper($row['mandatairenom']).", ".strtoupper($row['mandataireprenom']).", ID Tr&eacute;sor : ".$row['mandataireidtresor'].'</option>';
            }

	$Mysqli->close();
}

?>
