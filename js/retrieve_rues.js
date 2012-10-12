function retrieve_rue(inputValue){
    $.post("clients_retrieve_rues.php",{inputValue:inputValue},
           function(data){
            readResponse_rue(data);
           },"xml");

}

function readResponse_rue(data){

    responseXml = data;
    xmlDoc = responseXml.documentElement;
    try {villevalue = xmlDoc.getElementsByTagName("ville")[0].firstChild.data;} catch (e) {villevalue = ''}
    try {communevalue = xmlDoc.getElementsByTagName("commune")[0].firstChild.data;} catch (e) {communevalue = ''}
    try {paysvalue = xmlDoc.getElementsByTagName("pays")[0].firstChild.data;} catch (e) {paysvalue = ''}

    $("#txt_Ville").val(villevalue.replace(/^\s+|\s+$/g,""));
    $("#txt_VilleFailed").removeClass("error");
    $("#txt_Commune").val(communevalue.replace(/^\s+|\s+$/g,""));
    $("#txt_CommuneFailed").removeClass("error");
    $("#txt_Pays").val(paysvalue.replace(/^\s+|\s+$/g,""));
    $("#txt_PaysFailed").removeClass("error");

    setTimeout("validate();", 500);
}



