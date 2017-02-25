ko.observable.fn.copy = function(data){
    this(ko.mapping.fromJS(ko.mapping.toJS(data)));
};

ko.observable.fn.parseDate = function(){
    var date = new Date(this());
    var options = {
        timezone: 'UTC',
        hour: 'numeric',
        minute: 'numeric',
        second: 'numeric',
        year: 'numeric',
        month: 'numeric',
        day: 'numeric'
    };
    date = date.toLocaleString("ru", options);
    date = date.replace(',', ' ');

    return date;
};

ko.observable.fn.nameInitials = function(){
    console.log(this());
};