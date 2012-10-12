
function checkexist(inputNom, inputPrenom){
    var edit = gup('edit');
    if (edit > 0) {return;}
    
    $.post("mandataires_checkexist.php",{inputNom:inputNom,inputPrenom:inputPrenom},
           function(data){
            readResponse_checkexist(data);
            },"xml");
}


function readResponse_checkexist(data){
    responseXml = data;
    xmlDoc = responseXml.documentElement;
    result = xmlDoc.getElementsByTagName("exist")[0].firstChild.data;
    //alert(result);
    if (result == 1) {
        message("ATTENTION: Cette personne existe d&eacute;j&agrave; dans la base");
    }
    
    setTimeout("CheckExist();", 500);

}


