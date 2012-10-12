<?php
////////////////////////////////////////////////////////////////////////////////
//Logiciel : Multifac V.2.1
//Auteur : Reno Wong
//Dernière date de modification : 04/03/2009
////////////////////////////////////////////////////////////////////////////////
require_once ('config.php');

class Validate {
	/*private $mMysqli;

	function __construct(){
		$this->mMysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	}

	function __destruct(){
		$this->mMysqli->close();
	}*/

	public function ValidateAJAX($inputValue, $fieldID){
		switch($fieldID){
			case 'box_Proprietaire':
			return $this->validateBox($inputValue);
			break;

			case 'box_Facturer':
			return $this->validateBox($inputValue);
			break;

			case 'box_Categorie':
			return $this->validateBox($inputValue);
			break;

			case 'box_Servitude':
			return $this->validateBox($inputValue);
			break;

			case 'box_Quartier':
			return $this->validateBox($inputValue);
			break;

			case 'txt_Nomlieu':
			return $this->validateNomlieu($inputValue);
			break;

			case 'txt_Surface':
			return $this->validateSurface($inputValue);
			break;

			case 'txt_Nmaison':
			return $this->validateNmaison($inputValue);
			break;
		}
	}

	public function ValidatePHP($edit){
		$errorExist = 0;
		if (isset($_SESSION['errors'])) unset($_SESSION['errors']);

		$_SESSION['errors']['Proprietaire'] = 'hidden';
		$_SESSION['errors']['Mandataire'] = 'hidden';
		$_SESSION['errors']['Locataire'] = 'hidden';
		$_SESSION['errors']['Categorie'] = 'hidden';
		$_SESSION['errors']['Nomlieu'] = 'hidden';
		$_SESSION['errors']['Surface'] = 'hidden';
		$_SESSION['errors']['Nmaison'] = 'hidden';
		$_SESSION['errors']['Servitude'] = 'hidden';
		$_SESSION['errors']['Quartier'] = 'hidden';
		$_SESSION['errors']['Facturer'] = 'hidden';
		$_SESSION['errors']['EDT'] = 'hidden';
		$_SESSION['errors']['Compteur'] = 'hidden';
		$_SESSION['errors']['Observations'] = 'hidden';

		if(!$this->validateBox($_POST['box_Proprietaire'])){
			$errorExist = 1;
			$_SESSION['values']['box_Proprietaire'] = 0;
			$_SESSION['errors']['Proprietaire'] = 'error';
		} else {
			$_SESSION['values']['box_Proprietaire'] = $_POST['box_Proprietaire'];
		}

		if(!$this->validateBox($_POST['box_Facturer'])){
			$errorExist = 1;
			$_SESSION['values']['box_Facturer'] = 0;
			$_SESSION['errors']['Facturer'] = 'error';
		} else {
			$_SESSION['values']['box_Facturer'] = $_POST['box_Facturer'];
		}

		if(!$this->validateBox($_POST['box_Categorie'])){
			$errorExist = 1;
			$_SESSION['values']['box_Categorie'] = 0;
			$_SESSION['errors']['Categorie'] = 'error';
		} else {
			$_SESSION['values']['box_Categorie'] = $_POST['box_Categorie'];
		}

		if(!$this->validateBox($_POST['box_Servitude'])){
			$errorExist = 1;
			$_SESSION['values']['box_Servitude'] = 0;
			$_SESSION['errors']['Servitude'] = 'error';
		} else {
			$_SESSION['values']['box_Servitude'] = $_POST['box_Servitude'];
		}

		if(!$this->validateBox($_POST['box_Quartier'])){
			$errorExist = 1;
			$_SESSION['values']['box_Quartier'] = 0;
			$_SESSION['errors']['Quartier'] = 'error';
		} else {
			$_SESSION['values']['box_Quartier'] = $_POST['box_Quartier'];
		}

		if(!$this->validateNomlieu($_POST['txt_Nomlieu'])){
			$errorExist = 1;
			$_SESSION['errors']['Nomlieu'] = 'error';
		}

		if(!$this->validateSurface($_POST['txt_Surface'])){
			$errorExist = 1;
			$_SESSION['errors']['Surface'] = 'error';
		}

		if(!$this->validateNmaison($_POST['txt_Nmaison'])){
			$errorExist = 1;
			$_SESSION['errors']['Nmaison'] = 'error';
		}

		if($errorExist == 0){
			return true;
		} else {
			foreach ($_POST as $key => $value){
			//echo $key;
			$_SESSION['values'][$key] = $_POST[$key];
		}
			return false;
			return false;
		}
	}

	private function validateBox($value) {
		return ($value == '0' || $value == '') ? 0 : 1;
	}

	private function validateNomlieu($value) {
		$value = str_replace(Chr(32),"_", $value);
		$value = strtr($value,utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
		return (!preg_match('/^[A-Za-z_-]*$/', $value)) ? 0 : 1;
	}

	private function validateSurface($value) {
		if ($value=='') return 1;
		return (!preg_match('/^[0-9]*$/', $value)) ? 0 : 1;
	}

	private function validateNmaison($value) {
		if ($value=='') return 1;
		return (!preg_match('/^[0-9]*$/', $value)) ? 0 : 1;
	}
}
?>
