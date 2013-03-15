<?php
include_once('config.php');
require('global_functions.php');
require('chiffreenlettre.php');

$allowedExts = array("csv");
$extension = end(explode(".", $_FILES["file"]["name"]));
if ((($_FILES["file"]["type"] == "text/csv")||($_FILES["file"]["type"] == "application/vnd.ms-excel"))
&& ($_FILES["file"]["size"] < 100000)
&& in_array($extension, $allowedExts))
  {
  if ($_FILES["file"]["error"] > 0)
    {
    //echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
  else
    {
    //echo "Upload: " . $_FILES["file"]["name"] . "<br />";
    //echo "Type: " . $_FILES["file"]["type"] . "<br />";
    //echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
    //echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";

    //if (file_exists("cps/" . $_FILES["file"]["name"]))
    //  {
    //  //echo $_FILES["file"]["name"] . " already exists. ";
    //  }
    //else
    //  {
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "cps/" . $_FILES["file"]["name"]);
      //echo "Stored in: " . "cps/" . $_FILES["file"]["name"] . "<br />------------------------<br /><br />";;
      
      update_data("cps/" . $_FILES["file"]["name"]);
      //}
    }
  }
else
  {
  //echo "Invalid file";
  }
  
  
function update_data($f){
    reset_status_cantine();
    
    $new_f = str_replace(".csv","_FAAA.csv",$f);
    //echo ($new_f)."<br>";
    $fr = fopen($f, 'r');
    $fw = fopen($new_f, 'w');
    $total = 0;
    $touteslettres = "";
    
    while(!feof($fr))
      {
        $l = fgets($fr);
        $ar_l = explode(";",$l);
        if(is_numeric($ar_l[0])){
            //echo "processing line ".$l."<br />";
            $cf = ($ar_l[5])/100;
            $debut = mysqldateformat($ar_l[6]);
            $fin = mysqldateformat($ar_l[7]);
            $days = (strtotime($fin) - strtotime($debut))/86400;
            $months = round($days/30);
            $ar_l[8]="DP";
            $ar_l[9]=$months;
            $ar_l[10]=1600*$cf;
            $ar_l[11]=$months*1600*$cf;
            $ar_l[12]=0;
            $ar_l[13]=$ar_l[11];
            $ar_l[14]=0;
            $ar_l[15]=0;
            $ar_l[16]=$ar_l[11];
            $total += $ar_l[16];
            $touteslettres = chiffre_en_lettre($total);
            $l = join(";",$ar_l);
            fwrite($fw, $l);
	    
	    $cps = $ar_l[1];
	    if($ar_l[5]=="100"){$status=15;}else{$status=16;}
	    
	    update_enfant($cps,$status,$fin);
        } else {
            if(substr($l,0,3)==";;;"){ //end
                $l = str_replace("Total;;","Total;$total;",$l);
                $l = str_replace("(en toutes lettres) :","(en toutes lettres) : $touteslettres",$l);
                fwrite($fw, $l);
            }else{
                //echo "ignoring line ".$l."<br />";
            fwrite($fw, $l);
            }
        }
        unset($ar_l);
      }
      
    fclose($fr);
    fclose($fw);
    
    
    output_file($new_f,substr($new_f,4),"csv");
  }
  
function update_enfant($cps,$status,$date){
  	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);      
        $query = "UPDATE `enfants` SET `status`='$status', `status_expires`='$date' ".
        "WHERE `cps`='$cps'";
        $mysqli->query($query);
	$mysqli->close();
}

function mysqldateformat($input){
		$arr = explode('/', $input);
		return $arr[2].'-'.$arr[1].'-'.$arr[0];
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