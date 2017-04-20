/**
 * Created by kirill on 11.11.16.
 */


function sendCode(){

    var editor = ace.edit("editor");
    var code = editor.getValue();

    $("#button").prop("disabled",true);
    $.post('/receiveCode', {code: code} , function(msg){
        $("#button").prop("disabled",false);
       alert(msg);
    });

}