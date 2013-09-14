<?php
////////////////////////////////////////////////////////////////////////////////
//Page : mandataires_validate_class.php
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
		case 'box_Prefix':
		return $this->validatePrefix($inputValue);
		break;

		case 'txt_RS':
                return $this->validateRS($inputValue);
                break;

		case 'txt_Nom':
		return $this->validateNom($inputValue);
		break;

		case 'txt_Prenom':
		return $this->validatePrenom($inputValue);
		break;

		case 'txt_IDTresor':
		return $this->validateIDTresor($inputValue);
		break;

		case 'txt_Email':
		return $this->validateEmail($inputValue);
		break;

		case 'txt_Notahiti':
                return $this->validateNotahiti($inputValue);
                break;
		
		case 'txt_RC':
                return $this->validateRC($inputValue);
                break;

		case 'txt_CPS':
		return $this->validateCPS($inputValue);
		break;

		case 'txt_Telephone':
		return $this->validateTelephone($inputValue);
		break;

		case 'txt_Telephone2':
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

			$_SESSION['errors']['Prefix'] = 'hidden';
			$_SESSION['errors']['RS'] = 'hidden';
			$_SESSION['errors']['Nom'] = 'hidden';
			$_SESSION['errors']['Prenom'] = 'hidden';
			$_SESSION['errors']['IDTresor'] = 'hidden';
			$_SESSION['errors']['Email'] = 'hidden';
			$_SESSION['errors']['Notahiti'] = 'hidden';
			$_SESSION['errors']['RC'] = 'hidden';
			$_SESSION['errors']['CPS'] = 'hidden';
			$_SESSION['errors']['Telephone'] = 'hidden';
			$_SESSION['errors']['Telephone2'] = 'hidden';
			$_SESSION['errors']['Fax'] = 'hidden';
			$_SESSION['errors']['BP'] = 'hidden';
			$_SESSION['errors']['CP'] = 'hidden';
			$_SESSION['errors']['Ville'] = 'hidden';
			$_SESSION['errors']['Commune'] = 'hidden';
			$_SESSION['errors']['Pays'] = 'hidden';
                        $_SESSION['errors']['Aroa'] = 'hidden';
                        $_SESSION['errors']['Quartier'] = 'hidden';
			$_SESSION['errors']['RIB'] = 'hidden';

                if(!$this->validatePrefix($_POST['box_Prefix'])){
                        $errorExist = 1;
                        $_SESSION['values']['box_Prefix'] = 0;
                        $_SESSION['errors']['Prefix'] = 'error';
                } else {
                        $_SESSION['values']['box_Prefix'] = $_POST['box_Prefix'];
                }


		if(!$this->validateRS($_POST['txt_RS'])){
				$errorExist = 1;
                                $_SESSION['errors']['RS'] = 'error';
                }
		if(!$this->validateNom($_POST['txt_Nom'])){
				$errorExist = 1;
				$_SESSION['errors']['Nom'] = 'error';
		}
		if(!$this->validatePrenom($_POST['txt_Prenom'])){
				$errorExist = 1;
				$_SESSION['errors']['Prenom'] = 'error';
		}
		if(!$this->validateIDTresor($_POST['txt_IDTresor'])){
				$errorExist = 1;
				$_SESSION['errors']['IDTresor'] = 'error';
		}
		if(!$this->validateEmail($_POST['txt_Email'])){
				$errorExist = 1;
				$_SESSION['errors']['Email'] = 'error';
		}
		if(!$this->validateNotahiti($_POST['txt_Notahiti'])){
                                $errorExist = 1;
                                $_SESSION['errors']['Notahiti'] = 'error';
                }
		if(!$this->validateRC($_POST['txt_RC'])){
                                $errorExist = 1;
                                $_SESSION['errors']['RC'] = 'error';
                }
		if(!$this->validateTelephone($_POST['txt_Telephone'])){
				$errorExist = 1;
				$_SESSION['errors']['Telephone'] = 'error';
		}
		if(!$this->validateTelephone($_POST['txt_Telephone2'])){
				$errorExist = 1;
				$_SESSION['errors']['Telephone2'] = 'error';
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
			if(!$this->validateExistance($_POST['txt_Nom'], $_POST['txt_Prenom'])) {
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

        private function validatePrefix($value) {
                return ($value == '0') ? 0 : 1;
        }

	private function validateRS($value) {
                $value = decode_utf8($value);
                return (!preg_match('/^[0-9a-z_ ]{1,}$/i', $value)) ? 0 : 1;
        }


	private function validateNom($value) {
		$value = decode_utf8($value);
		return (!preg_match('/^[A-Za-z]{1,}[\-\ ]?[A-Za-z]*[\-\ ]?[A-Za-z]*$/i', $value)) ? 0 : 1;
	}

	private function validatePrenom($value) {
		$value = decode_utf8($value);
		return (!preg_match('/^[A-Za-z]{1,}[\-\ ]?[A-Za-z]*[\-\ ]?[A-Za-z]*$/i', $value)) ? 0 : 1;
	}

	private function validateIDTresor($value) {
                if ($value=='') return 1;
		return (!preg_match('/^[0-9]{4}-*[0-9]{2}$/', $value)) ? 0 : 1;
	}

	private function validateEmail($value) {
		if ($value=='') return 1;
		return (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $value)) ? 0 : 1;
	}
	
	private function validateNotahiti($value) { 
                if ($value=='') return 0;
                return (!preg_match('/^[0-9a-zA-Z]{6}$/', $value)) ? 0 : 1;
        }

	private function validateRC($value) {
		if ($value=='') return 1;
                return (!preg_match('/^[0-9]{5}-[a-zA-Z]{1}$/', $value)) ? 0 : 1;
        }

	private function validateCPS($value) {
		return (!preg_match('/^[0-9]{7}$/', $value)) ? 0 : 1;
	}

	private function validateTelephone($value) { //this validates telephone and telephone2
		if ($value=='') return 1;
		#return (!preg_match('^[0-9]{2}-*[0-9]{2}-*[0-9]{2}$', $value)) ? 0 : 1;
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
		$value = decode_utf8($value);
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


	private function validateExistance($nomvalue, $prenomvalue){
		if ($nomvalue == '' || $prenomvalue == '') return 0;
		$query = 'SELECT * FROM `mandataires` WHERE `mandataires`.`mandatairenom` LIKE "' . $nomvalue . '" AND `mandataires`.`mandataireprenom` LIKE "' . $prenomvalue . '"';

		$result = $this->mMysqli->query($query);
			if ($this->mMysqli->affected_rows > 0){
			return 0;
		} else {
			return 1;
		}
	}

}
?>
