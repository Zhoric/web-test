/**
 * Created by nyanjii on 14.12.16.
 */
ko.observable.fn.copy = function(data){
    this(ko.mapping.fromJS(ko.mapping.toJS(data)));
};