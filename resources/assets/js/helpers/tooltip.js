var validationTooltip = function(init){
    var selector = init.selector;
    var side = init.side ? init.side : 'top';

    this.initialize = function(){
        if (this.isInitialized()) return;

        $(selector).tooltipster({
            theme: 'tooltipster-light',
            trigger: 'custom',
            side: side
        });
    };
    this.isInitialized = function(){
        return $(selector).hasClass('tooltipstered');
    };
    this.open = function(){
        this.initialize();
        $(selector).tooltipster('content') ? $(selector).tooltipster('open') : null;
    };
    this.text = function(text){
        $(selector).tooltipster('content', text);
    };
    this.option = function(option, value){
        this.initialize();
        $(selector).tooltipster('option', option, value);
    };
    this.close = function(){
        this.isInitialized() ? $(selector).tooltipster('close') : null;
    };
    this.destroy = function(){
        this.isInitialized() ? $(selector).tooltipster('destroy') : null;
    };

    this.initialize();
};

