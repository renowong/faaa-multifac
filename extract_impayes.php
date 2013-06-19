<?php
include_once('config.php');

$ids_cantine = $_GET["ids_cantine"];
$ids_etal = $_GET["ids_etal"];
$ids_amarrage = $_GET["ids_amarrage"];

writetocsv($ids_cantine,$ids_etal,$ids_amarrage);

function writetocsv($ids_cantine,$ids_etal,$ids_amarrage){
    $fw = fopen('extract/impayes.csv', 'w');

    $ar_cantine_details = get_details($ids_cantine,'factures_cantine');

    fwrite($fw, "communeid;datefacture;montant;restearegler;periode/obs;nom;prenom;prenom2;telephone;telephone2;bp;cp;ville;commune;rib;obs;enfantnom;enfantprenom;ecole;classe;\n");



    foreach($ar_cantine_details as &$ar){
        fwrite($fw, $ar["communeid"].";");
        fwrite($fw, $ar["datefacture"].";");
        fwrite($fw, $ar["montantfcp"].";");
        fwrite($fw, $ar["restearegler"].";");
        fwrite($fw, html_entity_decode($ar['obs'].";",ENT_QUOTES, "ISO-8859-1"));
        fwrite($fw, $ar["clientnom"].";");
        fwrite($fw, $ar["clientprenom"].";");
        fwrite($fw, $ar["clientprenom2"].";");
        fwrite($fw, $ar["clienttelephone"].";");
        fwrite($fw, $ar["clientfax"].";");
        fwrite($fw, $ar["clientbp"].";");
        fwrite($fw, $ar["clientcp"].";");
        fwrite($fw, $ar["clientville"].";");
        fwrite($fw, $ar["clientcommune"].";");
        fwrite($fw, $ar["clientrib"].";");
        $clientobs = preg_replace("/\r\n/", ' ', $ar["clientobs"]);
        fwrite($fw, $clientobs.";");
        
        fwrite($fw, $ar["enfantnom"].";");
        fwrite($fw, $ar["enfantprenom"].";");
        fwrite($fw, $ar["ecole"].";");
        fwrite($fw, $ar["classe"].";");
        //fwrite($fw, $ar[""].";");
        //fwrite($fw, $ar[""].";");
        //fwrite($fw, $ar[""].";");
        //fwrite($fw, $ar[""].";");
        //fwrite($fw, $ar[""].";");
        //fwrite($fw, $ar[""].";");
        //fwrite($fw, $ar[""].";");
        
        fwrite($fw, "\n");
    }
    
    fclose($fw);
    
    
    output_file('extract/impayes.csv','impayes.csv','csv');
}
  
function get_details($ids,$table){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
                
        $query = "SELECT `factures_cantine`.`datefacture`,`factures_cantine`.`communeid`,`factures_cantine`.`montantfcp`,`factures_cantine`.`restearegler`,`factures_cantine`.`obs`,".
        " `clients`.`clientnom`,`clients`.`clientprenom`,`clients`.`clientprenom2`,`clients`.`clienttelephone`,`clients`.`clientfax`,`clients`.`clientbp`,`clients`.`clientcp`,`clients`.`clientville`,`clients`.`clientcommune`,`clients`.`clientrib`,`clients`.`obs` as `clientobs`,".
        " `enfants`.`nom` as `enfantnom`,`enfants`.`prenom` as `enfantprenom`,`enfants`.`ecole`,`classe`".
        " FROM `factures_cantine`".
        " RIGHT JOIN `clients` ON `factures_cantine`.`idclient`=`clients`.`clientid`".
        " INNER JOIN `factures_cantine_details` ON `factures_cantine`.`idfacture`=`factures_cantine_details`.`idfacture`".
        " RIGHT JOIN `enfants` ON `factures_cantine_details`.`idenfant`=`enfants`.`enfantid`".
        " WHERE `factures_cantine`.`idfacture` IN ($ids)";
        
        $result = $mysqli->query($query);

        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $result_array[] = $row;
	}
        
	$mysqli->close();
       
	return $result_array;
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