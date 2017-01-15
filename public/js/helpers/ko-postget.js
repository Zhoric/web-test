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