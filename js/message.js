function message(text){
    $("#message").html(text);
    $("#message").slideDown();
    setTimeout("erasemessage();",5000);
}

function erasemessage(){
    $("#message").slideUp();
    $("#message").html('');
}