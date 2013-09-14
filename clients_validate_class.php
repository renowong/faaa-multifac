<?php
////////////////////////////////////////////////////////////////////////////////
//Page : clients_validate_class.php
//Auteur : Reno Wong
////////////////////////////////////////////////////////////////////////////////
require_once ('config.php');
require_once ('decode_utf8.php');

class Validate {
	private $mMysqli;

	function __construct(){
		$this->mMysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	}

	function __destruct(){
		$this->mMysqli->close();
	}

	public function ValidateAJAX($inputValue, $fieldID){
		switch($fieldID){
			case 'box_Civilite':
			return $this->validateCivilite($inputValue);
			break;

			case 'txt_Nom':
			return $this->validateNom($inputValue);
			break;

			case 'txt_NomMarital':
			return $this->validateNomMarital($inputValue);
			break;

			case 'txt_Prenom':
			return $this->validatePrenom($inputValue);
			break;

			case 'txt_Prenom2':
			return $this->validatePrenom2($inputValue);
			break;

			case 'txt_DateNaissance':
			return $this->validateDateNaissance($inputValue);
			break;

			case 'txt_LieuNaissance':
			return $this->validateLieuNaissance($inputValue);
			break;

			case 'txt_IDTresor':
			return $this->validateIDTresor($inputValue);
			break;

			case 'txt_Email':
			return $this->validateEmail($inputValue);
			break;

			case 'txt_CPS':
			return $this->validateCPS($inputValue);
			break;

			case 'txt_Telephone':
			return $this->validateTelephone($inputValue);
			break;

			case 'txt_Fax':
			return $this->validateFax($inputValue);
			break;

			case 'txt_BP':
			return $this->validateBP($inputValue);
			break;

			case 'txt_CP':
			return $this->validateCP($inputValue);
			break;

			case 'txt_Ville':
			return $this->validateVille($inputValue);
			break;

			case 'txt_Commune':
			return $this->validateCommune($inputValue);
			break;

			case 'txt_Pays':
			return $this->validatePays($inputValue);
			break;
		
			case 'txt_Aroa':
			return $this->validateAroa($inputValue);
			break;
		
			case 'txt_Quartier':
			return $this->validateQuartier($inputValue);
			break;
			
			case 'txt_RIB':
			return $this->validateRIB($inputValue);
			break;
		}
	}

	public function ValidatePHP($edit){
		$errorExist = 0;
		if (isset($_SESSION['errors'])) unset($_SESSION['errors']);

		$_SESSION['errors']['Civilite'] = 'hidden';
		$_SESSION['errors']['Nom'] = 'hidden';
		$_SESSION['errors']['NomMarital'] = 'hidden';
		$_SESSION['errors']['Prenom'] = 'hidden';
		$_SESSION['errors']['Prenom2'] = 'hidden';
		$_SESSION['errors']['DateNaissance'] = 'hidden';
		$_SESSION['errors']['LieuNaissance'] = 'hidden';
		$_SESSION['errors']['IDTresor'] = 'hidden';
		$_SESSION['errors']['Email'] = 'hidden';
		$_SESSION['errors']['CPS'] = 'hidden';
		$_SESSION['errors']['Telephone'] = 'hidden';
		$_SESSION['errors']['Fax'] = 'hidden';
		$_SESSION['errors']['BP'] = 'hidden';
		$_SESSION['errors']['CP'] = 'hidden';
		$_SESSION['errors']['Ville'] = 'hidden';
		$_SESSION['errors']['Commune'] = 'hidden';
		$_SESSION['errors']['Pays'] = 'hidden';
		$_SESSION['errors']['Aroa'] = 'hidden';
		$_SESSION['errors']['Quartier'] = 'hidden';
		$_SESSION['errors']['RIB'] = 'hidden';

		if(!$this->validateCivilite($_POST['box_Civilite'])){
			$errorExist = 1;
			$_SESSION['values']['box_Civilite'] = 0;
			$_SESSION['errors']['Civilite'] = 'error';
		} else {
			$_SESSION['values']['box_Civilite'] = $_POST['box_Civilite'];
		}

		if(!$this->validateNom($_POST['txt_Nom'])){
			$errorExist = 1;
			$_SESSION['errors']['Nom'] = 'error';
		}

		if(!$this->validateNomMarital($_POST['txt_NomMarital'])){
			$errorExist = 1;
			$_SESSION['errors']['NomMarital'] = 'error';
		}

		if(!$this->validatePrenom($_POST['txt_Prenom'])){
			$errorExist = 1;
			$_SESSION['errors']['Prenom'] = 'error';
		}
	

		if(!$this->validatePrenom2($_POST['txt_Prenom2'])){
			$errorExist = 1;
			$_SESSION['errors']['Prenom2'] = 'error';
		}

		if(!$this->validateDateNaissance($_POST['txt_DateNaissance'])){
			$errorExist = 1;
			$_SESSION['errors']['DateNaissance'] = 'error';
		}

		if(!$this->validateLieuNaissance($_POST['txt_LieuNaissance'])){
			$errorExist = 1;
			$_SESSION['errors']['LieuNaissance'] = 'error';
		}

		if(!$this->validateIDTresor($_POST['txt_IDTresor'])){
			$errorExist = 1;
			$_SESSION['errors']['IDTresor'] = 'error';
		}

		if(!$this->validateEmail($_POST['txt_Email'])){
			$errorExist = 1;
			$_SESSION['errors']['Email'] = 'error';
		}

		if(!$this->validateCPS($_POST['txt_CPS'])){
			$errorExist = 1;
			$_SESSION['errors']['CPS'] = 'error';
		}

		if(!$this->validateTelephone($_POST['txt_Telephone'])){
			$errorExist = 1;
			$_SESSION['errors']['Telephone'] = 'error';
		}

		if(!$this->validateFax($_POST['txt_Fax'])){
			$errorExist = 1;
			$_SESSION['errors']['Fax'] = 'error';
		}

		if(!$this->validateBP($_POST['txt_BP'])){
			$errorExist = 1;
			$_SESSION['errors']['BP'] = 'error';
		}

		if(!$this->validateCP($_POST['txt_CP'])){
			$errorExist = 1;
			$_SESSION['errors']['CP'] = 'error';
		}

		if(!$this->validateVille($_POST['txt_Ville'])){
			$errorExist = 1;
			$_SESSION['errors']['Ville'] = 'error';
		}

		if(!$this->validateCommune($_POST['txt_Commune'])){
			$errorExist = 1;
			$_SESSION['errors']['Commune'] = 'error';
		}

		if(!$this->validatePays($_POST['txt_Pays'])){
			$errorExist = 1;
			$_SESSION['errors']['Pays'] = 'error';
		}
		
		if(!$this->validateAroa($_POST['txt_Aroa'])){
			$errorExist = 1;
			$_SESSION['errors']['Aroa'] = 'error';
		}
		
		if(!$this->validateQuartier($_POST['txt_Quartier'])){
			$errorExist = 1;
			$_SESSION['errors']['Quartier'] = 'error';
		}

		if(!$this->validateRIB($_POST['txt_RIB'])){
			$errorExist = 1;
			$_SESSION['errors']['RIB'] = 'error';
		}

		if(!$edit>0){
			if(!$this->validateExistance($_POST['txt_Nom'], $_POST['txt_Prenom'], $_POST['txt_DateNaissance'])) {
				$errorExist = 1;
			}
		}

		if($errorExist == 0){
			return true;
		} else {
			foreach ($_POST as $key => $value){
				//echo $key;
				$_SESSION['values'][$key] = $_POST[$key];
			}
		return false;
		}
	}

	private function validateCivilite($value) {
		return ($value == '0') ? 0 : 1;
	}

	private function validateNom($value) {
		$value = decode_utf8($value);
		return (!preg_match('/^[A-Za-z]{1,}[\-\ ]?[A-Za-z]*[\-\ ]?[A-Za-z]*[\-\ ]?[A-Za-z]*$/i', $value)) ? 0 : 1;
	}

	private function validateNomMarital($value) {
		if ($value=='') return 1;
		$value = decode_utf8($value);
		return (!preg_match('/^[A-Za-z]{1,}[\-\ ]?[A-Za-z]*[\-\ ]?[A-Za-z]*$/i', $value)) ? 0 : 1;
	}

	private function validatePrenom($value) {
		$value = decode_utf8($value);
		return (!preg_match('/^[A-Za-z]{1,}[\-\ ]?[A-Za-z]*[\-\ ]?[A-Za-z]*$/i', $value)) ? 0 : 1;
	}

	private function validatePrenom2($value) {
		if ($value=='') return 1;
		$value = decode_utf8($value);
		return (!preg_match('/^[A-Za-z]{1,}[\-\ ]?[A-Za-z]*[\-\ ]?[A-Za-z]*$/i', $value)) ? 0 : 1;
	}


	private function validateDateNaissance($value) {
		if (!preg_match('/^[0-9]{2}\/*[0-9]{2}\/*[0-9]{4}$/', $value)) return 0;
		$date = explode("/", $value);
		return (checkdate($date[1], $date[0], $date[2])) ? 1 : 0;
	}

	private function validateLieuNaissance($value) {
		if ($value=='') return 1;
		return (!preg_match('/^[a-z_]{1,}$/i', $value)) ? 0 : 1;
	}

	private function validateIDTresor($value) {
		if ($value=='') return 1;
		return (!preg_match('/^[0-9]{4}-*[0-9]{2}$/', $value)) ? 0 : 1;
	}

	private function validateEmail($value) {
		if ($value=='') return 1;
		return (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $value)) ? 0 : 1;
	}

	private function validateCPS($value) {
		if ($value=='') return 1;
		return (!preg_match('/^([0-9]{4}|[0-9]{7})$/', $value)) ? 0 : 1;
	}

	private function validateTelephone($value) {
		//return (!preg_match('^[0-9]{2}-*[0-9]{2}-*[0-9]{2}$', $value)) ? 0 : 1;
		if ($value=='') return 1;
		return (!preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{2}(-[0-9]{2})?$/', $value)) ? 0 : 1;
	}

	private function validateFax($value) {
		if ($value=='') return 1;
		return (!preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{2}(-[0-9]{2})?$/', $value)) ? 0 : 1;
	}

	private function validateBP($value) {
		if ($value=='') return 1;
		return (!preg_match('/^[0-9]*$/', $value)) ? 0 : 1;
	}

	private function validateCP($value) {
		if ($value=='') return 1;
		return (!preg_match('/^[0-9]{5}$/', $value)) ? 0 : 1;
	}

	private function validateVille($value) {
		if ($value=='') return 1;
		$value = decode_utf8($value);
		return (!preg_match('/^[a-z\'\-_\s]{1,}$/i', $value)) ? 0 : 1;
	}

	private function validateCommune($value) {
		if ($value=='') return 1;
		$value = decode_utf8($value);
		return (!preg_match('/^[a-z\'\-_\s]{1,}$/i', $value)) ? 0 : 1;
	}

	private function validatePays($value) {
		if ($value=='') return 1;
		return (!preg_match('/^[a-z_]{1,}$/i', $value)) ? 0 : 1;
	}
	
	private function validateAroa($value) {
		return ($value == '') ? 0 : 1;
	}
	
	private function validateQuartier($value) {
		return ($value == '') ? 0 : 1;
	}

	private function validateRIB($value) {
		if ($value=='') return 1;
		return (!preg_match('/^[0-9]{5}-[0-9]{5}-[0-9A-Za-z]{11}(-[0-9]{2}){0,3}$/', $value)) ? 0 : 1;
	}

	private function validateExistance($nomvalue, $prenomvalue, $dnvalue){
		if ($nomvalue == '' || $prenomvalue == '' || $dnvalue == '') return 0;
		$nomvalue .= "000";
		$nomvalue = substr($nomvalue,0,3);
		$prenomvalue .= "0000000";
		$prenomvalue = substr($prenomvalue,0,7);
		$date = explode("/", $dnvalue);
		$generatedcode = $date[2].$date[1].$date[0].$nomvalue.$prenomvalue;

		$query = 'SELECT * FROM `clients` WHERE `clients`.`clientcode` LIKE "' . $generatedcode . '"';

		$result = $this->mMysqli->query($query);

		if ($this->mMysqli->affected_rows > 0){
			return 0;
		} else {
			return 1;
		}
	}

}
?>
