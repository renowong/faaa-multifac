<?php
////////////////////////////////////////////////////////////////////////////////
//Logiciel : Multifac V.2.1
//Auteur : Reno Wong
//Dernière date de modification : 26/02/2009
////////////////////////////////////////////////////////////////////////////////
require_once ('config.php');

class Check {
	private $mMysqli;

	function __construct() {
		$this->mMysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	}

	function __destruct() {
		$this->mMysqli->close();
	}


	public function existance($nomvalue, $prenomvalue) {
		$query = 'SELECT * FROM `mandataires` WHERE `mandataires`.`mandatairenom` LIKE "' . $nomvalue . '" AND `mandataires`.`mandataireprenom` LIKE "' . $prenomvalue . '"';
		$result = $this->mMysqli->query($query);
		if ($this->mMysqli->affected_rows > 0) {
			return 1;
		} else {
			return 0;
		}
	}
}
?>