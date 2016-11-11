/**
 * Created by kirill on 11.11.16.
 */


function sendCode(){

    var editor = ace.edit("editor");
    var code = editor.getValue();

    $.post('/receiveCode', {code: code} , function(){
       alert('success');
    });

}