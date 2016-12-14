/**
 * Created by nyanjii on 14.12.16.
 */
ko.observable.fn.copy = function(){
    return ko.observable(ko.mapping.fromJS(ko.mapping.toJS(this)));
};