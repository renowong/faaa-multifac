<?php

require_once ('../config.php');

	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT `clientcode` FROM `clients`";

	$result = $Mysqli->query($query);
    while($row = $result->fetch_array(MYSQLI_ASSOC)){
        $rows[] = $row['clientcode'];
    }
    
    foreach($rows as $code){
        $query = "SELECT `clientcode`,`clientnom`,`clientprenom` FROM `clients` WHERE `clientcode`='$code'";
        $result = $Mysqli->query($query);
        $num_rows = mysqli_num_rows($result);

        if($num_rows>1){
            $row = $result->fetch_array(MYSQLI_ASSOC);
            print $row['clientcode']." ".$row['clientnom']." ".$row['clientprenom']."<br/>";
        }
    }
    
    
    
	$Mysqli->close();


?>