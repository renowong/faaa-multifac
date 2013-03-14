<?php
require_once ('config.php');

class retrieveid {
	private $mMysqli;

	function __construct() {
		$this->mMysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	}

	function __destruct() {
		$this->mMysqli->close();
	}

	public function id($code,$table) {


		switch ($table) {
		case "clients":
		case "enfants":	
		// fix for accented names//
		//$dn = explode("-",$value[2]);
		/*$query = 'SELECT `clients`.`clientid` FROM `'.DB.'`.`clients` WHERE `clients`.`clientnom` = "' . $value[0] .
				 '" AND `clients`.`clientprenom` = "' . $value[1] .
				 '" AND `clients`.`clientdatenaissance` = "' . $dn[2] . "-" . $dn[1] . "-" . $dn[0] . '"';
		*/
		$query = "SELECT `clientid` FROM `clients` WHERE `clientid` = '".$code."'";
		break;

		case "mandataires":
		/*$query = 'SELECT `mandataires`.`mandataireid` FROM `'.DB.'`.`mandataires` WHERE `mandataires`.`mandatairenom` = "' . $value[0] .
				 '" AND `mandataires`.`mandataireprenom` = "' . $value[1] .
				 '" AND `mandataires`.`mandatairetelephone` = "' . $value[2] . '"';
		*/
		$query = "SELECT `mandataireid` FROM `mandataires` WHERE `mandataireid` = '".$code."'";
		break;
		}
		//echo $query;

		$result = $this->mMysqli->query($query);
		if ($this->mMysqli->affected_rows > 0) {
			$row = mysqli_fetch_row($result);
			return $row[0];
		} else {
			return 0;
		}
		$Mysqli->close();
	}
}
?>
