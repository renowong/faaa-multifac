<?php
include_once('config.php');
$id = $_GET["id"];

        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);

	$query = "SELECT  `enfants`.`enfantid`,`enfants`.`nom`,`enfants`.`prenom`,`enfants`.`ecole`,`enfants`.`classe`,`enfants`.`active`,`ecoles_faaa`.`nomecole` FROM `enfants` RIGHT JOIN `ecoles_faaa` ON `enfants`.`ecole`=`ecoles_faaa`.`ecoleid` WHERE `clientid` ='$id'";
	$output = ''; //defining the variable
        $result = $mysqli->query($query);

        while($row = $result->fetch_array(MYSQLI_ASSOC)){
		($row['active']==0 ? $class="class='crossed'" : $class="");
		$output .= " <a $class href='javascript:editkid(".$row['enfantid'].");' title='Cliquez pour éditer'>".$row['nom']." ".utf8_encode($row['prenom'])." (".utf8_encode($row['nomecole'])." - ".$row['classe'].")</a><br/><br/>";
	}
	$mysqli->close();
	print $output;


?>