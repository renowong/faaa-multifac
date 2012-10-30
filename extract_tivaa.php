<?php
include_once('config.php');

$db = $_GET["db"];
$df = $_GET["df"];
$sql_db = $_GET["sql_db"];
$sql_df = $_GET["sql_df"];

$data = get_all($sql_db,$sql_df);

$file = "extract/temp.xml";

$fh = fopen($file, 'w') or die("can't open file");
$stringData = '<?xml version="1.0" encoding="UTF-8"?>';
fwrite($fh, $stringData);
$stringData = '<?mso-application progid="Excel.Sheet"?>';
fwrite($fh, $stringData);
$stringData = '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
fwrite($fh, $stringData);
$stringData = '<OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office"><Colors><Color><Index>3</Index><RGB>#c0c0c0</RGB></Color></Colors></OfficeDocumentSettings>';
fwrite($fh, $stringData);
$stringData = '<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel"><WindowHeight>9000</WindowHeight><WindowWidth>13860</WindowWidth><WindowTopX>240</WindowTopX><WindowTopY>75</WindowTopY><ProtectStructure>False</ProtectStructure><ProtectWindows>False</ProtectWindows></ExcelWorkbook>';
fwrite($fh, $stringData);
$stringData = '<Styles><Style ss:ID="Default" ss:Name="Default"/><Style ss:ID="Result" ss:Name="Result"><Font ss:Bold="1" ss:Italic="1" ss:Underline="Single"/></Style><Style ss:ID="Result2" ss:Name="Result2"><Font ss:Bold="1" ss:Italic="1" ss:Underline="Single"/><NumberFormat ss:Format="Currency"/></Style><Style ss:ID="Heading" ss:Name="Heading"><Font ss:Bold="1" ss:Italic="1" ss:Size="16"/></Style><Style ss:ID="Heading1" ss:Name="Heading1"><Font ss:Bold="1" ss:Italic="1" ss:Size="16"/></Style><Style ss:ID="co2"/><Style ss:ID="co3"/><Style ss:ID="co4"/><Style ss:ID="co5"/><Style ss:ID="co6"/><Style ss:ID="co7"/><Style ss:ID="ta1"/><Style ss:ID="ce1"><Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style><Style ss:ID="ce2"><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/></Borders></Style><Style ss:ID="ce3"><Alignment ss:Horizontal="Left" ss:Vertical="Center" ss:Indent="0"/></Style></Styles>';
fwrite($fh, $stringData);
$stringData = '<ss:Worksheet ss:Name="Sheet1">';
fwrite($fh, $stringData);

#begin table
$stringData = '<Table ss:StyleID="ta1">';
fwrite($fh, $stringData);
$stringData = '<Column ss:Width="64.26"/><Column ss:Width="57.4064"/><Column ss:Width="150.396"/><Column ss:Width="55.052"/><Column ss:Width="95.2992"/><Column ss:Span="2" ss:Width="64.26"/>';
fwrite($fh, $stringData);

#commune de faaa
$stringData = '<Row ss:Height="12.8952"><Cell ss:MergeAcross="11" ss:StyleID="ce1"><Data ss:Type="String">COMMUNE DE FAA\'A</Data></Cell></Row>';
fwrite($fh, $stringData);

#separation
$stringData = '<Row ss:Height="12.8952"><Cell /></Row>';
fwrite($fh, $stringData);

#title
$stringData = '<Row ss:Height="12.8952"><Cell ss:MergeAcross="11" ss:StyleID="ce1"><Data ss:Type="String">';
fwrite($fh, $stringData);
$stringData = 'ETAT NOMINATIF DES RECETTES PERCUES par imputation – PERIODE DU '.$db.' au '.$df;
fwrite($fh, $stringData);
$stringData = '</Data></Cell></Row>';
fwrite($fh, $stringData);

#separation
$stringData = '<Row ss:Height="12.8952"><Cell /></Row>';
fwrite($fh, $stringData);

#columns
$stringData = '<Row ss:Height="12.8952">';
fwrite($fh, $stringData);
$stringData = '<Cell ss:StyleID="ce2"><Data ss:Type="String">QUITTANCE</Data></Cell>';
fwrite($fh, $stringData);
$stringData = '<Cell ss:StyleID="ce2"><Data ss:Type="String">DATE</Data></Cell>';
fwrite($fh, $stringData);
$stringData = '<Cell ss:StyleID="ce2"><Data ss:Type="String">NOM DU REDEVABLE</Data></Cell>';
fwrite($fh, $stringData);
$stringData = '<Cell ss:StyleID="ce2"><Data ss:Type="String">N. ID.ART</Data></Cell>';
fwrite($fh, $stringData);
$stringData = '<Cell ss:StyleID="ce2"><Data ss:Type="String">EXERC</Data></Cell>';
fwrite($fh, $stringData);
$stringData = '<Cell ss:StyleID="ce2"><Data ss:Type="String">NATURE DU PRODUIT</Data></Cell>';
fwrite($fh, $stringData);
$stringData = '<Cell ss:StyleID="ce2"><Data ss:Type="String">NOM DU PAYEUR</Data></Cell>';
fwrite($fh, $stringData);
$stringData = '<Cell ss:StyleID="ce2"><Data ss:Type="String">MONTANT</Data></Cell>';
fwrite($fh, $stringData);
$stringData = '<Cell ss:StyleID="ce2"><Data ss:Type="String">M.REGT</Data></Cell>';
fwrite($fh, $stringData);
$stringData = '<Cell ss:StyleID="ce2"><Data ss:Type="String">N. CHEQUE</Data></Cell>';
fwrite($fh, $stringData);
$stringData = '<Cell ss:StyleID="ce2"><Data ss:Type="String">OBS.</Data></Cell>';
fwrite($fh, $stringData);
$stringData = '<Cell ss:StyleID="ce2"><Data ss:Type="String">ROL</Data></Cell>';
fwrite($fh, $stringData);
$stringData = '</Row>';
fwrite($fh, $stringData);

#separation
$stringData = '<Row ss:Height="12.8952"><Cell /></Row>';
fwrite($fh, $stringData);

#data
$stringData = $data[0];
fwrite($fh, $stringData);

#separation
$stringData = '<Row ss:Height="12.8952"><Cell /></Row>';
fwrite($fh, $stringData);

#total
$stringData = '<Row ss:Height="12.8952"><Cell><Data ss:Type="String">TOTAL GENERAL :</Data></Cell><Cell><Data ss:Type="Number">'.$data[1].'</Data></Cell><Cell /></Row>';
fwrite($fh, $stringData);

#separation
$stringData = '<Row ss:Height="12.8952"><Cell /></Row>';
fwrite($fh, $stringData);

#ARRETE LE PRESENT
$stringData = '<Row ss:Height="12.8952"><Cell ss:MergeAcross="2" ss:StyleID="ce3"><Data ss:Type="String">ARRETE LE PRESENT ETAT A LA SOMME DE : '.$data[1].' fcp</Data></Cell><Cell /></Row>';
fwrite($fh, $stringData);

#separation
$stringData = '<Row ss:Height="12.8952"><Cell /></Row>';
fwrite($fh, $stringData);

#signature
$stringData = '<Row ss:Height="12.8952"><Cell><Data ss:Type="String">Le Maire</Data></Cell><Cell><Data ss:Type="String">Le Régisseur</Data></Cell><Cell /></Row>';
fwrite($fh, $stringData);

#end table
$stringData = '</Table>';
fwrite($fh, $stringData);

#end worksheet
$stringData = '<x:WorksheetOptions/>';
fwrite($fh, $stringData);
$stringData = '</ss:Worksheet>';
fwrite($fh, $stringData);
$stringData = '</Workbook>';
fwrite($fh, $stringData);

fclose($fh);


output_file($file,"extract.xml","text/xml");

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

function get_all($sql_db,$sql_df){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);      
        $query = "SELECT DISTINCT * ".
        "FROM `paiements` ".
        "WHERE `paiements`.`date_paiement` ".
        "BETWEEN '$sql_db' AND '$sql_df'";
        
	$output = '';
        $result = $mysqli->query($query);

        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $result_array[]=$row;
	}
        
	$mysqli->close();
        
        $total = 0;
        foreach($result_array as $value){
            switch($value["type"]){
                case "CANTINE":
                    $numrol = get_rol($value["idfacture"],'factures_cantine');
                    $redevable = get_enfants($value["idfacture"]);
                break;
                case "AMARRAGE":
                    $numrol = get_rol($value["idfacture"],'factures_amarrage');
                    $redevable = get_client($value["idfacture"],'factures_amarrage');
                break;
                case "PLACE ET ETAL":
                    $numrol = get_rol($value["idfacture"],'factures_etal');
                    $redevable = get_mandataire($value["idfacture"],'factures_etal');
                break;
            }
            
            
                $year = substr($value["date_paiement"],0,4);
                if($value["mode"]=='anl'){$value["montantcfp"]=0;}
                $output .= '<Row ss:Height="12.8952">';
                $output .= '<Cell><Data ss:Type="String">'.$value["idpaiement"].'</Data></Cell>';
		$output .= '<Cell><Data ss:Type="String">'.reversedate($value["date_paiement"]).'</Data></Cell>';
                $output .= '<Cell><Data ss:Type="String">'.htmlentities($redevable,ENT_QUOTES, "UTF-8").'</Data></Cell>';
                $output .= '<Cell><Data ss:Type="String">-</Data></Cell>';
                $output .= '<Cell><Data ss:Type="String">'.$year.'</Data></Cell>';
                $output .= '<Cell><Data ss:Type="String">'.$value["type"].'</Data></Cell>';
                $output .= '<Cell><Data ss:Type="String">'.$value["payeur"].'</Data></Cell>';
                $output .= '<Cell><Data ss:Type="String">'.$value["montantcfp"].'</Data></Cell>';
                $output .= '<Cell><Data ss:Type="String">'.$value["mode"].'</Data></Cell>';
                $output .= '<Cell><Data ss:Type="String">'.$value["numero_cheque"].'</Data></Cell>';
                $output .= '<Cell><Data ss:Type="String">'.htmlentities($value["obs"],ENT_QUOTES, "UTF-8").'</Data></Cell>';
                $output .= '<Cell><Data ss:Type="String">'.$numrol.'</Data></Cell>';
                $output .= '</Row>';
                $total += $value["montantcfp"];
        }
        //print_r($result_array);
       
	return array($output,$total);
}

function get_rol($idfacture,$table){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT `rol`.`numrol` FROM `rol` JOIN `$table` ON `$table`.`rol`=`rol`.`idrol` WHERE `$table`.`idfacture`='$idfacture'";
        $result = $mysqli->query($query);

        $row = $result->fetch_array(MYSQLI_ASSOC);
        $output = $row['numrol'];
        $mysqli->close();

        return $output;
}

function get_mandataire($idfacture,$table){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT `mandataires`.`mandatairenom`,`mandataires`.`mandataireprenom` ".
        "FROM `mandataires` JOIN `$table` ON `$table`.`idclient`=`mandataires`.`mandataireid` WHERE `$table`.`idfacture`='$idfacture'";
        $result = $mysqli->query($query);

        $row = $result->fetch_array(MYSQLI_ASSOC);
        $output = $row['mandatairenom']." ".$row['mandataireprenom'];
        $mysqli->close();

        return $output;
}

function get_client($idfacture,$table){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT `clients`.`clientnom`,`clients`.`clientprenom` ".
        "FROM `clients` JOIN `$table` ON `$table`.`idclient`=`clients`.`clientid` WHERE `$table`.`idfacture`='$idfacture'";
        $result = $mysqli->query($query);

        $row = $result->fetch_array(MYSQLI_ASSOC);
        $output = $row['clientnom']." ".$row['clientprenom'];
        $mysqli->close();

        return $output;
}

function get_enfants($idfacture){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT DISTINCT `paiements`.`idfacture`,".
        "(SELECT concat (`enfants`.`nom`, ' ',`enfants`.`prenom`, ' ',`enfants`.`classe`, ' ',`ecoles_faaa`.`nomecole`) AS `enfant`) FROM `paiements` ".
        "RIGHT JOIN `factures_cantine_details` ON `paiements`.`idfacture` = `factures_cantine_details`.`idfacture` ".
        "LEFT JOIN `enfants` ON `factures_cantine_details`.`idenfant` = `enfants`.`enfantid` ".
        "LEFT JOIN `ecoles_faaa` ON `enfants`.`ecole` = `ecoles_faaa`.`ecoleid` ".
        "WHERE `paiements`.`idfacture`='$idfacture'";
        
        $output = '';
        $result = $mysqli->query($query);

        while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $output .= $row['(SELECT concat (`enfants`.`nom`, \' \',`enfants`.`prenom`, \' \',`enfants`.`classe`, \' \',`ecoles_faaa`.`nomecole`) AS `enfant`)']. " \ ";
	}
        
        $mysqli->close();
        
        $output = substr($output,0,-3);
        return $output;
    }
    
function reversedate($d){
        $d = explode("-",$d);
        return $d[2]."-".$d[1]."-".$d[0];
        }
?>