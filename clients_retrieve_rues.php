<?php
session_start();
//require_once ('error_handler.php');
require_once ('clients_retrieve_rues_class.php');

$retriever = new Retriever($_POST['inputValue']);

$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
			'<response>'.
			'	<ville>'.
					$retriever->ville().
			'	</ville>'.
			'	<commune>'.
					$retriever->commune().
			'	</commune>'.
			'	<pays>'.
					$retriever->pays().
			'	</pays>'.
			'</response>';

if(ob_get_length()) ob_clean();
header('Content-Type: text/xml');

echo $response;

?>