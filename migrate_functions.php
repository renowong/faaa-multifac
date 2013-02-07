<?php
require_once('config.php');

$type = $_POST["type"];

switch($type){
    case 'load':
        $ecole = $_POST["ecole"];
        $classe = $_POST["classe"];
        print load($ecole,$classe);
    break;
    case 'migrate':
        $ids = $_POST["ids"];
        $ecole = $_POST["ecole"];
        $classe = $_POST["classe"];
        $fecole = $_POST["fecole"];
        $fclasse = $_POST["fclasse"];
        //$ar_ids = explode(",",$ids);
        foreach ($ids as &$value){
            $where .= "`enfantid`='$value' OR ";
        }
        $where = substr($where,0,-4);
        print migrate($ecole,$classe,$where);
        update_history($fecole,$fclasse,$ecole,$classe);
    break;
}


///////////////functions////////////////

function update_history($fecole,$fclasse,$tecole,$tclasse){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "INSERT INTO `migrate_history` (`idmigrate`,`from_ecole`,`from_classe`,`to_ecole`,`to_classe`,`date`) VALUES (NULL,'$fecole','$fclasse','$tecole','$tclasse',CURRENT_TIMESTAMP)";
    $mysqli->query($query);
    $mysqli->close();
}

function migrate($ecole,$classe,$where){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "UPDATE `enfants` SET `ecole`='$ecole',`classe`='$classe' WHERE $where";
    $mysqli->query($query);
    $mysqli->close();
    return $query;   
}

function load($ecole,$classe){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "SELECT `nom`,`prenom`,`enfantid` FROM `enfants` WHERE `ecole`='$ecole' AND `classe`='$classe' AND `active`='1' ORDER BY `nom`";
    $result = $mysqli->query($query);
        $output = "<table id=\"tbl_results\"><tr>";
        $i = 0;
        $j = 4; //max columns
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $output .= "<td class=\"tdcheck\" id=\"td".$row["enfantid"]."\"><input type=\"checkbox\" name=\"".$row["enfantid"]."\" id=\"".$row["enfantid"]."\"/>".htmlentities($row["nom"])." ".htmlentities($row["prenom"])."</td>";
                $i++;
                if($i==$j) {$output .= "</tr><tr>";$i=0;}
            }
        if($i==$j) {$output .= "</table>";}else{$output .= "</td></tr></table>";}
    $mysqli->close();
    return $output;
}

?>