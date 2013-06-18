<?php
include_once('config.php');

$db = $_GET["db"];
$df = $_GET["df"];
$sql_db = $_GET["sql_db"];
$sql_df = $_GET["sql_df"];
$rol = $_GET["rol"];
$today = date("m.d.y");

$file = "extract/rolmre_cant_".$rol."_".$today.".txt";

$data = get_all($sql_db,$sql_df,$rol);
$rol_id = insert_rol("rolmre_cantine",$sql_db,$sql_df,$file,$rol);
update_rol($sql_db,$sql_df,$rol_id);



$fh = fopen($file, 'w') or die("can't open file");


fwrite($fh, $data);

fclose($fh);

//print_r($data);
output_file($file,"rolmre_cant_".$rol."_".$today.".txt","text/txt");

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
        "xml" => "text/xml"
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
		print($buffer); //echo($buffer); // is also possible
		flush();
		$bytes_send += strlen($buffer);
	}
 fclose($file);
 } else die('Error - can not open file.');
 
die();
}

function get_all($sql_db,$sql_df,$rol){

    
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);      
        $query = "SELECT `factures_cantine`.`idclient`,`factures_cantine`.`datefacture`,`factures_cantine`.`restearegler`,".
        "`clients`.`clientnom`,`clients`.`clientprenom`,`clients`.`clientprenom2`,`clients`.`clienttelephone`,".
        "`clients`.`clientfax`,`clients`.`clientbp`,`clients`.`clientcp`,`clients`.`clientville`,`clients`.`clientcommune`,`clients`.`clientpays`,`clients`.`clientrib`,`clients`.`obs` ".
        
        "FROM `factures_cantine` ".
        "RIGHT JOIN `clients` ON `factures_cantine`.`idclient` = `clients`.`clientid` ".
        "WHERE `factures_cantine`.`datefacture` ".
        "BETWEEN '$sql_db' AND '$sql_df' AND `reglement`='0' AND `acceptation`='1' AND `bourse`='0'";
        
	$output = '';
        $result = $mysqli->query($query);

        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $result_array[]=$row;
	}
        
	$mysqli->close();
        
        foreach($result_array as $value){
        
        $ROLMVT='1';
        $ROLCOL='10'; //Code collectivite a remplir champs de 2 caracteres
        $ROLNAT='10'; //Code nature a determiner par la CF champs de 2 caracteres
        $ROLEX='0000'; //idem année en cours champs de 4 caracteres
        $ROLPER='0'; //periode champs de 1 caractere
        $ROLDET='0000000000000'; //idclient (5 max) champs de 13 caracteres
        $ROLCLE1='0';
        $ROLNUL='0';
        $ROLCLE2='0';
        $ROLREC='00';
        $ROLDAT='00000000'; //date d'emission format YYYYMMDD
        $ROLROL='00'; //numéro du role champs de 2 caracteres
        $ROLEAU='000000000000'; //montant EAU sans TVA champs de 12 caracteres
        $ROLASS='000000000000'; //montant ASSAINISSEMENT sans TVA champs de 12 caracteres
        $ROLTVE='000000000000'; //montant TVA sur EAU champs de 12 caracteres
        $ROLTVA='000000000000'; //montant TVA sur ASSAINISSEMENT champs de 12 caracteres
        $ROLTOT='000000000000'; //total champs de 12 caracteres
        $ROLNMAJ='000000000000'; //montant assainissement non majorable
        $ROLNOM='                                '; //nom du débiteur champs de 32 caracteres
        $ROLCNM='                                '; //complement nom du débiteur champs de 32 caracteres
        $ROLDIS='                                '; //mentions complémentaires
        $ROLADR='                                '; //adresse du débiteur champs de 32 caracteres
        $ROLCVI='00000000000000000000000000000000'; //complement ville champs de 32 caracteres
        $ROLCP='00000'; //code postal champs de 5 caracteres
        $ROLLOC='                      FAA\'A'; //localité champs de 27 caracteres
        $ROLORU='00000000000000000000000000000000'; //rue champs de 32 caracteres
        $ROLOVI='00000000000000000000000000000000'; //ville champs de 32 caracteres
        $ROLPRE='0'; //Code prélèvement champs de 1 caractere
        $ROLRET='00000'; //Code établissement champs de 5 caracteres
        $ROLRGU='00000'; //Code guichet champs de 5 caracteres
        $ROLRCO='00000000000'; //numéro du compte champs de 11 caracteres
        $ROLRCL='00'; //clé rib champs de 2 caracteres
        $ROLTIT='000000000000000000000000'; //titulaire du compte champs de 24 caracteres
        $ROLCLI='00000000000000000000'; //Numéro compte client
        $ROLSCH='0000000000'; //Chapitre champs de 10 caracteres
        $ROLART='00000000000000000000'; //article champs de 20 caracteres
        $ROLMONNAIE='F';
        $ROLHOM='0';
        $ROLDEB='00';
        $FILLER='000000000000000000000000000000'; //champs de 30 caracteres
        $ROLTPR='0'; //1=eau/assainissement, 2=redevances LEMA, 0=autres
        $ROLVER='2';
        
        $ROLDET.= $value["idclient"];
        $ROLDET = substr($ROLDET,-13);
        $ROLDAT = convert_date($value["datefacture"]);
        $ROLEX = substr($ROLDAT,0,4);
        $ROLROL .= $rol;
        $ROLROL = substr($ROLROL,-2);
        $ROLEAU .= $value["restearegler"];
        $ROLEAU = substr($ROLEAU,-12);
        $ROLTOT .= $value["restearegler"];
        $ROLTOT = substr($ROLTOT,-12);
        $ROLNOM = $value["clientnom"]." ".$value["clientprenom"].$ROLNOM;
        $ROLNOM = substr($ROLNOM,0,32);
        $ROLCNM = $value["clientprenom2"].$ROLCNM;
        $ROLCNM = substr($ROLCNM,0,32);
        $value["clienttelephone"] = str_replace("-","",$value["clienttelephone"]);
        $value["clientfax"] = str_replace("-","",$value["clientfax"]);
        $ROLDIS = "TEL:".$value["clienttelephone"]."-FAX/VINI:".$value["clientfax"].$ROLDIS;
        $ROLDIS = substr($ROLDIS,0,32);
        if($value["clientbp"]!=''){$value["clientbp"]="BP ".$value["clientbp"];}
        if($value["clientbp"]!='' && $value["clientville"]!=''){$value["clientville"]=" ".$value["clientville"];}
        $ROLADR = $value["clientbp"].$value["clientville"].$ROLADR;
        $ROLADR = substr($ROLADR,0,32);
        $ROLCP .= $value["clientcp"];
        $ROLCP = substr($ROLCP,-5);
        if(date("n")<7){$ROLPER="1";}else{$ROLPER="2";}
        if($value["clientrib"]!=''){
            $ar_rib = explode("-",$value["clientrib"]);
            $ROLRET .= $ar_rib[0];
            $ROLRET = substr($ROLRET,-5);
            $ROLRGU .= $ar_rib[1];
            $ROLRGU = substr($ROLRGU,-5);
            $ROLRCO .= $ar_rib[2];
            $ROLRCO = substr($ROLRCO,-11);
            $ROLRCL .= $ar_rib[3];
            $ROLRCL = substr($ROLRCL,-2);
        }

        $stringData .= $ROLMVT.$ROLCOL.$ROLNAT.$ROLEX.$ROLPER.$ROLDET.$ROLCLE1.$ROLNUL.$ROLCLE2.$ROLREC.$ROLDAT;
        $stringData .= $ROLROL.$ROLEAU.$ROLASS.$ROLTVE.$ROLTVA.$ROLTOT.$ROLNMAJ.$ROLNOM.$ROLCNM.$ROLDIS.$ROLADR;
        $stringData .= $ROLCVI.$ROLCP.$ROLLOC.$ROLORU.$ROLOVI.$ROLPRE.$ROLRET.$ROLRGU.$ROLRCO.$ROLRCL.$ROLTIT;
        $stringData .= $ROLCLI.$ROLSCH.$ROLART.$ROLMONNAIE.$ROLHOM.$ROLDEB.$FILLER.$ROLTPR.$ROLVER."\r\n";
        }
       //return $query;
    

return $stringData;
}

function insert_rol($type,$sql_db,$sql_df,$filename,$numrol){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);      
        
    $query = "INSERT INTO `rol` (`numrol`,`type`,`from`,`to`,`filename`) VALUES ('$numrol','$type','$sql_db','$sql_df','$filename')";
    
    $mysqli->query($query);
    $lastid = $mysqli->insert_id;
    $mysqli->close();
    
    return $lastid;
}

function update_rol($sql_db,$sql_df,$rol){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);      
        
    $query = "UPDATE `factures_cantine` SET `rol`='$rol' WHERE `factures_cantine`.`datefacture` ".
    "BETWEEN '$sql_db' AND '$sql_df' AND `reglement`='0' AND `validation`='1' AND `acceptation`='1'";
    
    $mysqli->query($query);
    $mysqli->close();
}

function convert_date($d){
        $d = explode("-",$d);
        return $d[0].$d[1].$d[2];
        }
?>