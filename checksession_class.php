<?php
////////////////////////////////////////////////////////////////////////////////
//Logiciel : Multifac V.2.1
//Auteur : Reno Wong
////////////////////////////////////////////////////////////////////////////////
require_once ('config.php');

class User {
	private $mMysqli;
	private $userid;
	private $userfirstname;
	private $userlastname;
	private $userlogin;
	private $userpassword;
	private $userlastlogin;
	private $userservice;
	private $userisadmin;
	private $userisactive;
    private $userisvalidator;

/*function __construct($userid){
$this->mMysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
$this->getdata($userid);
}*/

/*function __destruct(){
$this->mMysqli->close();
}*/

	function getdata($userid){
		$this->mMysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
		$query = 'SELECT * FROM `'.DB.'`.`user` WHERE `user`.`userid` = ' . $userid;
		$result = $this->mMysqli->query($query);
		if ($this->mMysqli->affected_rows > 0) {
			$row = mysqli_fetch_array($result);
			$this->userid = $row['userid'];
			$this->userfirstname = $row['userfirstname'];
			$this->userlastname = $row['userlastname'];
			$this->userlogin = $row['userlogin'];
			$this->userpassword = $row['userpassword'];
			$this->userlastlogin = $row['userlastlogin'];
			$this->userservice = $row['userservice'];
			$this->userisadmin = $row['userisadmin'];
			$this->userisactive = $row['userisactive'];
            $this->userisvalidator = $row['userisvalidator'];
		}
		$this->mMysqli->close();
	}

	function userid() {
		return $this->userid;
	}

	function userfirstname() {
		return $this->userfirstname;
	}

	function userlastname() {
		return $this->userlastname;
	}

	function userlogin() {
		return $this->userlogin;
	}

	function userpassword() {
		return $this->userpassword;
	}

	function userlastlogin() {
		return $this->userlastlogin;
	}

	function userservice() {
		return $this->userservice;
	}

	function userisadmin() {
		return $this->userisadmin;
	}

	function userisactive() {
		return $this->userisactive;
	}
    
    function userisvalidator() {
		return $this->userisvalidator;
	}
    
}
?>
