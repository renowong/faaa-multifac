<?php

function getCompteDisplay(){
	$dom = new domDocument;
	$arr = array(utf8_decode('é') => "\\351", utf8_decode('è') => "\\350",  utf8_decode('ç') => "\\347", utf8_decode('à') => "\\340", utf8_decode('ù') => "\\371");
	$clientdata = strtr($_SESSION['client'], $arr);
	$dom->loadXML($clientdata);
	if (!$dom) {
		echo "Error while parsing the document\n";
		exit;
	}
	$s = simplexml_import_dom($dom);

return strtoupper($s->nom) . ' ' . strtoupper($s->prenom) . ',' . $s->clientid . ',' . $s->type;

}

function trispace($n){
	return number_format($n, 0, ',', ' ');
}

function reset_status_cantine(){
	$today = date("Y-m-d");
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);      
        $query = "UPDATE `enfants` SET `status`='6', `status_expires`='1997-01-01' ".
        "WHERE `status_expires`<'$today'";
        $mysqli->query($query);
	$mysqli->close();
}

?>