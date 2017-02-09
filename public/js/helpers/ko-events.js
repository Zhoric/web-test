var validationEvents = function(validObj){

    var validation = validObj;

    this.focusout = function(data, e){
        var selector = $(e.target).attr('id');

        validation[selector]
            ? validation[selector].destroy()
            : validation[selector] = new validationTooltip({
                selector: '#' + selector
            });

        validation[selector].open();
    };

    this.focusin = function(data, e){
        var selector = $(e.target).attr('id');
        validation[selector] ? validation[selector].close() : null;
    };
};