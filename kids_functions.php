<?php
////////////////////////////////////////////////////////////////////////////////
//Page : kids_functions.php
//Auteur : Reno Wong
////////////////////////////////////////////////////////////////////////////////
session_start();
include_once('config.php');

$type = $_POST["query"];

switch($type){
    case "select":
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $childid = $_POST["id"];
        $query = "SELECT * FROM `".DB."`.`enfants` WHERE `enfantid`='$childid'";
        $result = $mysqli->query($query);
        $row = $result->fetch_array(MYSQLI_ASSOC);
        
        $output ='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $output .='<child id="'.$row['enfantid'].'">';
        $output .='<nom>'.$row['nom'].'</nom>';
        $output .='<prenom>'.utf8_encode($row['prenom']).'</prenom>';
        $output .='<dn>'.$row['dn'].'</dn>';
        $output .='<cps>'.$row['cps'].'</cps>';
        $output .='<sexe>'.$row['sexe'].'</sexe>';
        $output .='<ecole>'.$row['ecole'].'</ecole>';
        $output .='<classe>'.$row['classe'].'</classe>';
        $output .='<status>'.$row['status'].'</status>';
        $output .='<active>'.$row['active'].'</active>';
        $output .='<destinataire>'.$row['destinataire'].'</destinataire>';
        $output .='</child>';

	$mysqli->close();
	print $output;
    break;
}

?>