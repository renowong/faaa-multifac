<?php
require_once('config.php');

	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = 'SELECT m.from_classe, m.to_classe, fromD.nomecole AS fromEcole, toD.nomecole AS toEcole, m.from_ecole, m.to_ecole FROM migrate_history AS m LEFT JOIN `ecoles_faaa` AS toD ON toD.ecoleid = m.to_ecole LEFT JOIN `ecoles_faaa` AS fromD ON fromD.ecoleid = m.from_ecole ORDER BY date DESC LIMIT 5';
	$result = $mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		$list .= "<a href='javascript:undo(\"".$row["from_ecole"]."\",\"".$row["from_classe"]."\",\"".$row["to_ecole"]."\",\"".$row["to_classe"]."\");'>".$row["fromEcole"]."[".$row["from_classe"]."] -> ".$row["toEcole"]."[".$row["to_classe"]."]</option><br/>";
	}
	$mysqli->close();
	print $list;
        
?>