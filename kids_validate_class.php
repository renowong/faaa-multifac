<?php
////////////////////////////////////////////////////////////////////////////////
//Page : enfants_validate_class.php
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
			case 'txt_nom_enfant':
			return $this->validateNom($inputValue);
			$_SESSION['errors']['enfantNom'] = true;
			break;
		
			case 'txt_prenom_enfant':
			return $this->validatePrenom($inputValue);
			$_SESSION['errors']['enfantPrenom'] = true;
			break;

			case 'txt_dn_enfant':
			return $this->validateDateNaissance($inputValue);
			$_SESSION['errors']['enfantDN'] = true;
			break;

			case 'txt_cps_enfant':
			return $this->validateCPS($inputValue);
			$_SESSION['errors']['enfantCPS'] = true;
			break;

			//case 'txt_classe_enfant':
			//return $this->validateClasse($inputValue);
			//break;
		}
	}

	public function ValidatePHP(){
		$errorExist = 0;
	
		if(!$this->validateNom($_POST['txt_nom_enfant'])){
			$errorExist = 1;
			$_SESSION['errors']['enfantNom'] = true;
		}
	
		if(!$this->validatePrenom($_POST['txt_prenom_enfant'])){
			$errorExist = 1;
			$_SESSION['errors']['enfantPrenom'] = true;
		}
	
		if(!$this->validateDateNaissance($_POST['txt_dn_enfant'])){
			$errorExist = 1;
			$_SESSION['errors']['enfantDN'] = true;
		}
	
		if(!$this->validateCPS($_POST['txt_cps_enfant'])){
			$errorExist = 1;
			$_SESSION['errors']['enfantCPS'] = true;
		}
		
		//if(!$this->validateClasse($_POST['txt_classe_enfant'])){
		//	$errorExist = 1;
		//}
	
		if($errorExist == 0){
			return true;
		} else {
			return false;
		}
	}


	private function validateNom($value) {
		$value = decode_utf8($value);
		//return (!preg_match('/^[A-Za-z]{1,}[\-\ ]?[A-Za-z]{1,}[\-\ ]?[A-Za-z]{1,}$/i', $value)) ? 0 : 1;
		return (!preg_match('/^[A-Za-z]{1,}[\-\ ]?[A-Za-z]*[\-\ ]?[A-Za-z]*$/i', $value)) ? 0 : 1;
	}

	private function validatePrenom($value) {
		$value = decode_utf8($value);
		return (!preg_match("/^[A-Za-z]{1,}[\-\ \']?[A-Za-z]{1,}[\-\ \']?[A-Za-z]{1,}$/i", $value)) ? 0 : 1;
	}


	private function validateDateNaissance($value) {
		if ($value==""){return 0;}else{return 1;}
		//$date = explode("/", $value);
		//return (checkdate($date[1], $date[0], $date[2])) ? 1 : 0;
	}

	private function validateCPS($value) {
		//if ($value=='') return 0;
		return (!preg_match('/^([0-9]{4}|[0-9]{7})$/', $value)) ? 0 : 1;
	}

	//private function validateClasse($value) {
	//	//if ($value=='') return 0;
	//	return (!preg_match('/^[a-z0-9]{1,}$/i', $value)) ? 0 : 1;
	//}

}
?>
