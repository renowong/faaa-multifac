function validate(inputValue, fieldID){
    $.post("lieux_validate.php",{inputValue:inputValue,fieldID:fieldID},
           function(data){
                readResponse(data)
           },"xml");
}

function readResponse(data){
    responseXml = data;
    xmlDoc = responseXml.documentElement;
    result = xmlDoc.getElementsByTagName("result")[0].firstChild.data;
    fieldID = xmlDoc.getElementsByTagName("fieldid")[0].firstChild.data;

    if(result == "0"){
        $("#"+fieldID + "Failed").addClass("error");
    }else{
        $("#"+fieldID + "Failed").removeClass("error");
    }

    setTimeout("validate();", 500);

}