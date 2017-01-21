/**
 * Created by nyanjii on 14.12.16.
 */

$get = function(url, successCallback, errors, data){
    if (typeof data === 'undefined'){
        return function(){
            $.get(url, function(response){
                var result = ko.mapping.fromJSON(response);
                if (result.Success()){
                    successCallback(result.Data);
                    return;
                }
                errors.show(result.Message());
            });
        }
    }
    return function(){
        $.get(url, data, function(response){
            var result = ko.mapping.fromJSON(response);
            if (result.Success()){
                successCallback(result.Data);
                return;
            }
            errors.show(result.Message());
        });
    }
};



// request = {string url, object errors, func successCallback, object ?data}

$ajaxget = function(request){
    var $getCallback = function(response){
        var result = ko.mapping.fromJSON(response);
        if (result.Success()){
            request.successCallback(result.Data);
            return;
        }
        request.errors.show(result.Message());
    };

    if (typeof request.data === 'undefined'){
        $.get(request.url, function(response){
            $getCallback(response);
        });
    }
    else{
        $.get(request.url, request.data, function(response){
            $getCallback(response);
        });
    }
};

$ajaxpost = function(request){
    $.post(request.url, request.data, function(response){
        var result = ko.mapping.fromJSON(response);
        if (result.Success()){
            if (typeof request.successCallback !== 'undefined')
                request.successCallback(result.Data);
            return;
        }
        request.errors.show(result.Message());
    });
};

$post = function(url, data, errors, successCallback){
    return function(){
        $.post(url, data, function(response){
            var result = ko.mapping.fromJSON(response);
            if (result.Success()){
                if (typeof successCallback !== 'undefined') successCallback(result.Data);
                return;
            }
            errors.show(result.Message());
        });
    };
};