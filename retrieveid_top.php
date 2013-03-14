<?php
require_once('checksession.php');
require_once ('config.php');

if (isset($_GET['closeaccount']) && $_GET['closeaccount'] == 1) $_SESSION['client'] = '';
if (!empty($_SESSION['client'])) {
		$arCompte = getCompteDisplay();
		$arCompte = preg_split("/,/", $arCompte);
	}

?>
