<?php
$today = date("Ymd\_H.i.s");
$dir = "zippdf";
$ar_names = scandir($dir);
$haspdf = false;

foreach($ar_names as &$name){
    if(substr($name,-3)=="pdf"){
        $haspdf = true;
    }
}

if($haspdf){
    /////zipping the files////////
    $zip = new ZipArchive();
    $filename = $dir."/".$today.".zip";
    
    if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
        exit("cannot open <$filename>\n");
    }
    
    foreach($ar_names as &$name){
        if(substr($name,-3)=="pdf"){
            
            $zip->addFile($dir."/".$name,$name);
        }
    }
    
    //echo "numfiles: " . $zip->numFiles . "\n";
    //echo "status:" . $zip->status . "\n";
    $zip->close();
    
    
    //////removing pdfs///////
    foreach($ar_names as &$name){
        if(substr($name,-3)=="pdf"){
           unlink($dir."/".$name);
        }
    }
    
    //print $filename;
    header("Content-type: application/zip"); 
    header("Content-Disposition: attachment; filename=$filename"); 
    readFile($filename);
}else{
    header("Location:ecole_global_search.php?pdf=0");
}

?>