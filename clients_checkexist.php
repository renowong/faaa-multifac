<?php
session_start();
//require_once ('error_handler.php');
require_once ('clients_checkexist_class.php');

$nomvalue = $_POST['inputNom'];
$prenomvalue = $_POST['inputPrenom'];
$dnvalue = $_POST['inputDate'];
$nomvalue .= "000";
$nomvalue = substr($nomvalue, 0, 3);
$prenomvalue .= "0000000";
$prenomvalue = substr($prenomvalue, 0, 7);
$date = explode("/", $dnvalue);
$generatedcode = $date[2].$date[1].$date[0].$nomvalue.$prenomvalue;

$check = new Check();

$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
'<response>'.
'	<exist>'.
		$check->existance($generatedcode).
'	</exist>'.
'</response>';

if(ob_get_length()) ob_clean();
header('Content-Type: text/xml');

echo $response;

?>