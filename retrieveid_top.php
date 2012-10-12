<?php
require_once('checksession.php');
require_once ('config.php');

if (isset($_GET['closeaccount']) && $_GET['closeaccount'] == 1) $_SESSION['client'] = '';
if (!empty($_SESSION['client'])) {
		$arCompte = getCompteDisplay();
		$arCompte = preg_split("/,/", $arCompte);
	}

//#################################functions#####################################

function buildOptionsPersonnes($form) {
	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    
    switch($form){
        case "clients":
            $query = "SELECT `clientid`, `clientnom`, `clientprenom`, `clientdatenaissance` FROM `".DB."`.`clients` ORDER BY clientnom";
            $result = $Mysqli->query($query);
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                echo '<option style="text-align:left" value="'.$row['clientid'].'">'.strtoupper($row['clientnom']).", ".strtoupper($row['clientprenom']).", ".date("d-m-Y",strtotime($row[clientdatenaissance])).'</option>';
            }
        break;
        
        case "mandataires":
            $query = "SELECT `mandatairenom`, `mandataireprenom`, `mandataireid`, `mandataireidtresor` FROM `mandataires`";
            $result = $Mysqli->query($query);
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                echo '<option style="text-align:left" value="'.$row['mandataireid'].'">'.strtoupper($row['mandatairenom']).", ".strtoupper($row['mandataireprenom']).", ID Tr&eacute;sor : ".$row['mandataireidtresor'].'</option>';
            }
        break;
    
        case "enfants":
            $query = "SELECT `enfants`.`nom`, `enfants`.`prenom`, `enfants`.`dn`, `enfants`.`classe`, `ecoles_faaa`.`nomecole`, `enfants`.`clientid` FROM `enfants` INNER JOIN `ecoles_faaa` ON `enfants`.`ecole`=`ecoles_faaa`.`ecoleid`";
            $result = $Mysqli->query($query);
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                echo '<option style="text-align:left" value="'.$row['clientid'].'">'.strtoupper($row['nom']).", ".strtoupper($row['prenom']).", ".date("d-m-Y",strtotime($row['dn'])).", ".$row['nomecole'].", ".$row['classe'].'</option>';
            }
        break;
    }

	$Mysqli->close();
}
?>
