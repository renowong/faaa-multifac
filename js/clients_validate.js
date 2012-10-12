function validate(inputValue, fieldID){
    $.post("clients_validate.php",{inputValue:inputValue,fieldID:fieldID},
           function(data){
            readResponse(data)
            },"xml");
}

function readResponse(data){
    responseXml = data;
    xmlDoc = responseXml.documentElement;
    result = xmlDoc.getElementsByTagName("result")[0].firstChild.data;
    fieldID = xmlDoc.getElementsByTagName("fieldid")[0].firstChild.data;
    message = $("#"+fieldID + "Failed");
    if(result == "0"){
        message.addClass("error");
        message.show();
    }else{
        message.removeClass("error");
        message.hide();
    }
    setTimeout("validate();", 500);

}





