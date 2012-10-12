<?php

$all = $_GET["seeall"];
$dir = "zippdf";
$ar_names = scandir($dir, 1);
//unset($ar_names[0]);
//unset($ar_names[1]);
$count = 1;

$output = "<table><tr>";
$i=1;

foreach($ar_names as &$name){
    if(substr($name,-3)=="zip"){
        if($count<=6&&$count!=0||$all=='1'){
            $output .= "<td><a href='$dir/$name'>$name</a></td>";$count++;
            if($i==2){$output .= "</tr><tr>";$i=1;}else{$i++;}
        }else{
            if($count!=0){$output .= "<tr><td><button onClick='javascript:seemore();'>Voir plus...</button></td></tr>";}
            $count=0;
        }
    }
}
$output .= "</tr></table>";

if($all=='1'){$output .= "<p><button onClick='javascript:close_seemore();'>Fermer</button></p>";}

print $output;

?>