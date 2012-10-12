<?php
include_once('../config.php');

$file = "extract.csv"; 
 $handle = fopen($file, 'r'); 
 while (!feof($handle)) 
 { 
 $data = fgets($handle, 1024);
 $data = removewhitespace($data);
 
 $lastid = addparent($data);
 addenfant($data,$lastid);
  print "<p>";
 } 
 fclose($handle);
 
 function removewhitespace($d){
    $d = str_replace("  "," ",$d);
    $d = str_replace("  "," ",$d);
    $d = str_replace("  "," ",$d);
    $d = str_replace("  "," ",$d);
    $d = str_replace("  "," ",$d);
    $d = str_replace("  "," ",$d);
    $d = str_replace("  "," ",$d);
    $d = str_replace("  "," ",$d);
    return $d;
 }
 
 function addenfant($d,$pid){
    $ardata = explode(";",$d);
    $sexe = $ardata[0];
    $nom = $ardata[1];
    $prenom = utf8_encode($ardata[2]);
    $dn = reversedate($ardata[4]);
    $cps = $ardata[5];
    $classe = $ardata[6];
    $ecole = $ardata[7];
    
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $mysqli->set_charset("utf8");
    $query = "INSERT INTO `".DB."`.`enfants` (`clientid`, `nom`,".
				 " `prenom`, `destinataire`, `dn`, `cps`, `sexe`,".
                                 " `ecole`, `classe`,`status`,`active`)".
				 " VALUES ('$pid', '$nom', '$prenom', '1', '$dn','$cps','$sexe','$ecole','$classe','1','1')";
	$mysqli->query($query);
        $mysqli->close();
        
        return $query;

 }
 
 function addparent($d){
    $ardata = explode(";",$d);
    $nom = $ardata[8];
    $prenom = utf8_encode($ardata[9]);
    $prenom2 = utf8_encode($ardata[10]);
    $dn = reversedate($ardata[11]);
    $obs = $ardata[12];
    $obs2 = $ardata[13];
    $email = $ardata[14];
    $tel = $ardata[15];
    $fax = $ardata[16];
    $codeprenom = str_replace('-','',$prenom);
    $codenom = str_replace('-','',$nom);
    $clientcode = code($dn,$codenom,$codeprenom);
    
    print $prenom;
    
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $mysqli->set_charset("utf8");
    $query = "INSERT INTO `".DB."`.`clients` (`clientcode`, `clientstatus`,".
				 " `clientcivilite`, `clientnom`, `clientprenom`, `clientprenom2`, `clientdatenaissance`,".
                                 " `clientemail`, `clienttelephone`,`clientfax`,`obs`)".
				 " VALUES ('".$clientcode."', '1', 'MR', '".$nom."', '".$prenom."', '".$prenom2."', '".$dn."',".
                                 "'".$email."','".$tel."','".$fax."','".$obs." ".$obs2."')";
	$mysqli->query($query);
	$lastid = $mysqli->insert_id; //use it to insert the details.
        $mysqli->close();

print $query;
	return $lastid;
 }

    function reversedate($d){
        $ar = explode("/",$d);
        return $ar[2]."-".$ar[0]."-".$ar[1];
    }
    
    function code($date,$nom,$prenom){

	$nom = strtr($nom,utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
        $nom = strtoupper($nom);	
        $nom = str_replace(' ','',$nom);
        $prenom = utf8_decode($prenom);
	$prenom = strtr($prenom,utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
	$prenom = strtoupper($prenom);
        $prenom = str_replace(' ','',$prenom);
	$nom .= "000";
	$nom = substr($nom, 0, 3);
	$prenom .= "0000000";
	$prenom = substr($prenom, 0, 7);
	$date = explode("-", $date);
        if ($date[1]<10) $date[1] = "0".$date[1];
	$generatedcode = $date[0].$date[1].$date[2].$nom.$prenom;
        
        print $generatedcode;
        return $generatedcode;
    }
?>