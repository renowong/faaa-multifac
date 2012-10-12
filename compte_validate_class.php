<?php
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
            case 'txt_Nom':
                return $this->validateNom($inputValue);
                break;

            case 'txt_Prenom':
                return $this->validatePrenom($inputValue);
                break;

            case 'txt_Login':
                return $this->validateLogin($inputValue);
                break;

            case 'box_Service':
                return $this->validateService($inputValue);
                break;

            case 'txt_Password':
                return $this->validatePassword($inputValue);
                break;
        }
    }

    public function ValidatePHP(){
        $errorExist = 0;
        if (isset($_SESSION['errors'])) unset($_SESSION['errors']);

        $_SESSION['errors']['Nom'] = 'hidden';
        $_SESSION['errors']['Prenom'] = 'hidden';
        $_SESSION['errors']['Login'] = 'hidden';
        $_SESSION['errors']['Service'] = 'hidden';
        $_SESSION['errors']['Password'] = 'hidden';
        $_SESSION['errors']['Password2'] = 'hidden';


        if(!$this->validateNom($_POST['txt_Nom'])){
                $errorExist = 1;
                $_SESSION['errors']['Nom'] = 'error';
        }


        if(!$this->validatePrenom($_POST['txt_Prenom'])){
                $errorExist = 1;
                $_SESSION['errors']['Prenom'] = 'error';
        }

        if(!$this->validateLogin($_POST['txt_Login'])){
                $errorExist = 1;
                $_SESSION['errors']['Login'] = 'error';
        }

        if(!$this->validateService($_POST['box_Service'])){
                $errorExist = 1;
                $_SESSION['errors']['Service'] = 'error';
        }

        if(!$this->validatePassword($_POST['txt_Password'])){
                $errorExist = 1;
                $_SESSION['errors']['Password'] = 'error';
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

    private function validateNom($value) {
        $value = str_replace(Chr(32),"_", $value);
        #$value = strtr($value,utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
        return (!preg_match('/^[a-z_-]{1,}$/i', $value)) ? 0 : 1;
    }

    private function validatePrenom($value) {
        $value = str_replace(Chr(32),"_", $value);
        $value = strtr($value,utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
        return (!preg_match('/^[a-z_-àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ]{1,}$/i', $value)) ? 0  : 1;
    }

    private function validateLogin($value) {
        return (!preg_match('/^[a-z0-9_-]{4,}$/', $value)) ? 0 : 1;
    }


    private function validateService($value) {
        return ($value == '0') ? 0 : 1;
    }

    private function validatePassword($value) {
        return (!preg_match('/^[a-z0-9_-]{4,}$/', $value)) ? 0 : 1;
    }


}
?>
