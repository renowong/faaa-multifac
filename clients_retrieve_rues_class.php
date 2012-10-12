<?php
////////////////////////////////////////////////////////////////////////////////
//Logiciel : Multifac V.2.1
//Auteur : Reno Wong
//Dernière date de modification : 24/02/2009
////////////////////////////////////////////////////////////////////////////////
require_once ('config.php');

class Retriever {
	private $mMysqli;
	private $ville;
	private $commune;
	private $pays;
	private $query;
	private $result;

	function __construct($value) {
		$this->mMysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DBRUES);
		$this->query = 'SELECT * FROM `codepostaux` WHERE `Code` = "' . $value . '"';
		$this->result = $this->mMysqli->query($this->query);
		if ($this->result->num_rows) {
			while ($row = $this->result->fetch_array(MYSQLI_ASSOC)) {
				$this->ville = $row['Ville'];
				$this->commune = $row['Ville'];
				$this->pays = $row['Ile'];
			}
			$this->result->close();
		} else {
			$this->result->close();
			return "";
		}
	}

	function __destruct() {
		$this->mMysqli->close();
	}


	public function ville() {
		return $this->ville;
	}


	public function commune() {
		return $this->commune;
	}

	public function pays() {
		return $this->pays;
	}
}
?>
