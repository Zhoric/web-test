var validationEvents = function(validObj){

    var validation = validObj;

    this.focusout = function(data, e){
        var selector = $(e.target).attr('id');
        var text = $(e.target).attr('title');

        validation[selector].text(text);
        validation[selector].open();
    };

    this.focusin = function(data, e){
        var selector = $(e.target).attr('id');
        validation[selector] ? validation[selector].close() : null;
    };
};