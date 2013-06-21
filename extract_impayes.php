<?php
include_once('config.php');

$ids_cantine = $_POST["ids_cantine"];
$ids_etal = $_POST["ids_etal"];
$ids_amarrage = $_POST["ids_amarrage"];
if($ids_amarrage!==''){
    $ids_amarrage_clients = separate_clients_mandataires($ids_amarrage,"C","factures_amarrage");
    $ids_amarrage_mandataires = separate_clients_mandataires($ids_amarrage,"M","factures_amarrage");
}


writetocsv($ids_cantine,$ids_etal,$ids_amarrage_clients,$ids_amarrage_mandataires);

function writetocsv($ids_cantine,$ids_etal,$ids_amarrage_clients,$ids_amarrage_mandataires){
    $fw = fopen('extract/impayes.csv', 'w');

    if(strlen($ids_cantine)>0) $ar_cantine_details = get_details_cantine($ids_cantine,'factures_cantine');
    if(strlen($ids_etal)>0) $ar_etal_details = get_details_mandataire($ids_etal,'factures_etal');
    //print ($ids_amarrage_clients);
    if(strlen($ids_amarrage_clients)>0) $ar_amarrage_clients_details = get_details_client($ids_amarrage_clients,'factures_amarrage');
    if(strlen($ids_amarrage_mandataires)>0) $ar_amarrage_mandataires_details = get_details_mandataire($ids_amarrage_mandataires,'factures_amarrage');

    fwrite($fw, "type;communeid;datefacture;montant;restearegler;periode/obs;nom;prenom;telephone;telephone2;bp;cp;ville;commune;rib;obs;enfantnom;enfantprenom;ecole;classe;\n");

    $ar_types_clients = array("ar_cantine_details","ar_amarrage_clients_details");
    $ar_types_mandataires = array("ar_etal_details","ar_amarrage_mandataires_details");
    
    foreach($ar_types_clients as &$type){
        foreach(${$type} as &$ar){
            fwrite($fw, $ar["0"].";");
            fwrite($fw, $ar["communeid"].";");
            fwrite($fw, $ar["datefacture"].";");
            fwrite($fw, $ar["montantfcp"].";");
            fwrite($fw, $ar["restearegler"].";");
            fwrite($fw, html_entity_decode($ar['obs'].";",ENT_QUOTES, "ISO-8859-1"));
            fwrite($fw, $ar["clientnom"].";");
            fwrite($fw, $ar["clientprenom"].";");
            fwrite($fw, $ar["clienttelephone"].";");
            fwrite($fw, $ar["clientfax"].";");
            fwrite($fw, $ar["clientbp"].";");
            fwrite($fw, $ar["clientcp"].";");
            fwrite($fw, $ar["clientville"].";");
            fwrite($fw, $ar["clientcommune"].";");
            fwrite($fw, $ar["clientrib"].";");
            $clientobs = preg_replace("/\r\n/", ' ', $ar["clientobs"]);
            fwrite($fw, $clientobs.";");
                        
            if($type=="ar_cantine_details"){
                fwrite($fw, $ar["enfantnom"].";");
                fwrite($fw, $ar["enfantprenom"].";");
                fwrite($fw, $ar["nomecole"].";");
                fwrite($fw, $ar["classe"].";"); 
            }
            
            fwrite($fw, "\n");
        }
    }
    
    foreach($ar_types_mandataires as &$type){
        foreach(${$type} as &$ar){
            fwrite($fw, $ar["0"].";");
            fwrite($fw, $ar["communeid"].";");
            fwrite($fw, $ar["datefacture"].";");
            fwrite($fw, $ar["montantfcp"].";");
            fwrite($fw, $ar["restearegler"].";");
            fwrite($fw, html_entity_decode($ar['obs'].";",ENT_QUOTES, "ISO-8859-1"));
            fwrite($fw, $ar["mandatairenom"].";");
            fwrite($fw, $ar["mandataireprenom"].";");
            fwrite($fw, $ar["mandatairetelephone"].";");
            fwrite($fw, $ar["mandatairetelephone2"].";");
            fwrite($fw, $ar["mandatairebp"].";");
            fwrite($fw, $ar["mandatairecp"].";");
            fwrite($fw, $ar["mandataireville"].";");
            fwrite($fw, $ar["mandatairecommune"].";");
            fwrite($fw, $ar["mandatairerib"].";");
            $mandataireobs = preg_replace("/\r\n/", ' ', $ar["mandataireobs"]);
            fwrite($fw, $mandataireobs.";");
            fwrite($fw, "\n");
        }
    }
    
    
    fclose($fw);
    
    
    output_file('extract/impayes.csv','impayes.csv','csv');
}
  
function get_details_cantine($ids,$table){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
                
        $query = "SELECT `factures_cantine`.`datefacture`,`factures_cantine`.`communeid`,`factures_cantine`.`montantfcp`,`factures_cantine`.`restearegler`,`factures_cantine`.`obs`,".
        " `clients`.`clientnom`,`clients`.`clientprenom`,`clients`.`clienttelephone`,`clients`.`clientfax`,`clients`.`clientbp`,`clients`.`clientcp`,`clients`.`clientville`,`clients`.`clientcommune`,`clients`.`clientrib`,`clients`.`obs` as `clientobs`,".
        " `enfants`.`nom` as `enfantnom`,`enfants`.`prenom` as `enfantprenom`,`classe`,`ecoles_faaa`.`nomecole`".
        " FROM `factures_cantine`".
        " RIGHT JOIN `clients` ON `factures_cantine`.`idclient`=`clients`.`clientid`".
        " INNER JOIN `factures_cantine_details` ON `factures_cantine`.`idfacture`=`factures_cantine_details`.`idfacture`".
        " RIGHT JOIN `enfants` ON `factures_cantine_details`.`idenfant`=`enfants`.`enfantid`".
        " RIGHT JOIN `ecoles_faaa` ON `enfants`.`ecole`=`ecoles_faaa`.`ecoleid`".
        " WHERE `factures_cantine`.`idfacture` IN ($ids)";
        
        $result = $mysqli->query($query);

        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                array_unshift($row,$table);
                $result_array[] = $row;
	}
        
	$mysqli->close();
	return $result_array;
}

function get_details_client($ids,$table){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
                
        $query = "SELECT `$table`.`datefacture`,`$table`.`communeid`,`$table`.`montantfcp`,`$table`.`restearegler`,`$table`.`obs`,".
        " `clients`.`clientnom`,`clients`.`clientprenom`,`clients`.`clienttelephone`,`clients`.`clientfax`,`clients`.`clientbp`,`clients`.`clientcp`,`clients`.`clientville`,`clients`.`clientcommune`,`clients`.`clientrib`,`clients`.`obs` as `clientobs`".
        " FROM `$table`".
        " RIGHT JOIN `clients` ON `$table`.`idclient`=`clients`.`clientid`".
        " WHERE `$table`.`idfacture` IN ($ids)";
        
        $result = $mysqli->query($query);

        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                array_unshift($row,$table);
                $result_array[] = $row;
	}
        
	$mysqli->close();
        //print($query);
	return $result_array;
}

function get_details_mandataire($ids,$table){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
                
        $query = "SELECT `$table`.`datefacture`,`$table`.`communeid`,`$table`.`montantfcp`,`$table`.`restearegler`,`$table`.`obs`,".
        " `mandataires`.`mandatairenom`,`mandataires`.`mandataireprenom`,`mandataires`.`mandatairetelephone`,`mandataires`.`mandatairetelephone2`,`mandataires`.`mandatairebp`,`mandataires`.`mandatairecp`,`mandataires`.`mandataireville`,`mandataires`.`mandatairecommune`,`mandataires`.`mandatairerib`,`mandataires`.`obs` as `mandataireobs`".
        " FROM `$table`".
        " RIGHT JOIN `mandataires` ON `$table`.`idclient`=`mandataires`.`mandataireid`".
        " WHERE `$table`.`idfacture` IN ($ids)";
        
        $result = $mysqli->query($query);

        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                array_unshift($row,$table);
                $result_array[] = $row;
	}
        
	$mysqli->close();
        //print($query);
	return $result_array;
}

function separate_clients_mandataires($ids,$type,$table){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
                
        $query = "SELECT `idfacture` FROM `$table` WHERE `idfacture` IN ($ids) AND `type_client`='$type'";
        
        $result = $mysqli->query($query);

        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $result_array[] = $row;
	}
        
	$mysqli->close();
        //print($query);
    
        foreach($result_array as &$id){
            $output .= $id["idfacture"].",";
        }
        $output = substr($output,0,strlen($output)-1);
        return $output;
}

function output_file($file, $name, $mime_type='')
{
 /*
 This function takes a path to a file to output ($file), 
 the filename that the browser will see ($name) and 
 the MIME type of the file ($mime_type, optional).
 
 If you want to do something on download abort/finish,
 register_shutdown_function('function_name');
 */
 if(!is_readable($file)) die('File not found or inaccessible!');
 
 $size = filesize($file);
 $name = rawurldecode($name);
 
 /* Figure out the MIME type (if not specified) */
 $known_mime_types=array(
 	"pdf" => "application/pdf",
 	"txt" => "text/plain",
 	"html" => "text/html",
 	"htm" => "text/html",
	"exe" => "application/octet-stream",
	"zip" => "application/zip",
	"doc" => "application/msword",
	"xls" => "application/vnd.ms-excel",
	"ppt" => "application/vnd.ms-powerpoint",
	"gif" => "image/gif",
	"png" => "image/png",
	"jpeg"=> "image/jpg",
	"jpg" =>  "image/jpg",
	"php" => "text/plain",
        "xml" => "text/xml",
        "csv" => "text/csv"
 );
 
 if($mime_type==''){
	 $file_extension = strtolower(substr(strrchr($file,"."),1));
	 if(array_key_exists($file_extension, $known_mime_types)){
		$mime_type=$known_mime_types[$file_extension];
	 } else {
		$mime_type="application/force-download";
	 };
 };
 
 @ob_end_clean(); //turn off output buffering to decrease cpu usage
 
 // required for IE, otherwise Content-Disposition may be ignored
 if(ini_get('zlib.output_compression'))
  ini_set('zlib.output_compression', 'Off');
 
 header('Content-Type: ' . $mime_type);
 header('Content-Disposition: attachment; filename="'.$name.'"');
 header("Content-Transfer-Encoding: binary");
 header('Accept-Ranges: bytes');
 
 /* The three lines below basically make the 
    download non-cacheable */
 header("Cache-control: private");
 header('Pragma: private');
 header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
 
 // multipart-download and download resuming support
 if(isset($_SERVER['HTTP_RANGE']))
 {
	list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
	list($range) = explode(",",$range,2);
	list($range, $range_end) = explode("-", $range);
	$range=intval($range);
	if(!$range_end) {
		$range_end=$size-1;
	} else {
		$range_end=intval($range_end);
	}
 
	$new_length = $range_end-$range+1;
	header("HTTP/1.1 206 Partial Content");
	header("Content-Length: $new_length");
	header("Content-Range: bytes $range-$range_end/$size");
 } else {
	$new_length=$size;
	header("Content-Length: ".$size);
 }
 
 /* output the file itself */
 $chunksize = 1*(1024*1024); //you may want to change this
 $bytes_send = 0;
 if ($file = fopen($file, 'r'))
 {
	if(isset($_SERVER['HTTP_RANGE']))
	fseek($file, $range);
 
	while(!feof($file) && 
		(!connection_aborted()) && 
		($bytes_send<$new_length)
	      )
	{
		$buffer = fread($file, $chunksize);
		print($buffer); ////echo($buffer); // is also possible
		flush();
		$bytes_send += strlen($buffer);
	}
 fclose($file);
 } else die('Error - can not open file.');
 
die();
}
?>