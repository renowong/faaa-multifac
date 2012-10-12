function enf_validate(value,field){
    //alert("validating "+field);
    $.post("kids_validate.php", { inputValue: value, fieldID: field },
        function(data) {
        //alert("Data Loaded: " + data);
   
       var xml = data,
       xmlDoc = $.parseXML(xml),
       $xml = $(xmlDoc),
       $response = $xml.find("response");
       
       var result = $response.find("result").text();
       var field = $response.find("fieldid").text();
       var fielderror = field+"Failed";
       
       //alert(fielderror);

       if(result=='0'){
            $("#"+fielderror).addClass("error").removeClass("hidden");
       }else{
            $("#"+fielderror).removeClass("error").addClass("hidden");
       }
    });
}


