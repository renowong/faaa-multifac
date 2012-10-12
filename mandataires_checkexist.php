<?php
session_start();
//require_once ('error_handler.php');
require_once ('mandataires_checkexist_class.php');

$nomvalue = $_POST['inputNom'];
$prenomvalue = $_POST['inputPrenom'];

$check = new Check();

$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
'<response>'.
	'<exist>'.
		$check->existance($nomvalue, $prenomvalue).
	'</exist>'.
'</response>';

if(ob_get_length()) ob_clean();
header('Content-Type: text/xml');

echo $response;

?>