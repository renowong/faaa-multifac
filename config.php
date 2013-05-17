<?php

//Config to change by the user
date_default_timezone_set("Pacific/Tahiti");
$titre="Multifac";
$dbserver="localhost";
$db="multifac";
$dbuser="multifac";
$dbpwd="multifac";
$dbport="3306"; //port 3306 par défaut

$dbrues="rues";
//$apppath=substr(strrchr($_SERVER['HTTP_REFERER'], "\/"), 1);
$version="3.0 build ".build();


$ar_f_m = array(
           0 => array(
                "table" => "factures_etal",
                "title" => "place et &eacute;tal",
                "link" => "etal"
              ),
           1 => array(
                "table" => "factures_amarrage",
                "title" => "amarrage",
                "link" => "amarrage"
              )
           );

$ar_f_c = array(
           0 => array(
                "table" => "factures_cantine",
                "title" => "cantine",
                "link" => "cantine"
              ),
           1 => array(
                "table" => "factures_amarrage",
                "title" => "amarrage",
                "link" => "amarrage"
              )
           
           );

// declare Constants
define("TITRE", $titre);
define("DBSERVER", $dbserver);
define("DB", $db);
define("DBRUES", $dbrues);
define("DBUSER", $dbuser);
define("DBPWD", $dbpwd);
define("DBPORT", $dbport);


//define("APPPATH", $apppath);
define("VERSION", $version);

/*function getbuild(){  ///// deprecated
	$file = ".bzr/branch/last-revision";
	$fh = fopen($file, 'r');
	$builddata = fread($fh, filesize($file));
	fclose($fh);
	$build = preg_split("/\ /",$builddata);
	return $build[0];
}*/

function build(){
    $url = dirname(__FILE__);
    $output = `svn info $url | grep 'Revision'`;
    $ar_output = explode(" ",$output);
    return $ar_output[1];
}

?>
