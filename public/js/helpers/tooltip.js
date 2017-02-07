var validationTooltip = function(init){
    var selector = init.selector;

    this.initialize = function(){
        if (this.isInitialized()) return;

        $(selector).tooltipster({
            theme: 'tooltipster-light',
            trigger: 'custom'
        });
    };
    this.isInitialized = function(){
        return $(selector).hasClass('tooltipstered');
    };
    this.open = function(){
        this.initialize();
        $(selector).tooltipster('content') ? $(selector).tooltipster('open') : null;
    };
    this.text = function(){
        $(selector).tooltipster('content', init.text());
    };
    this.close = function(){
        this.isInitialized() ? $(selector).tooltipster('close') : null;
    };
    this.destroy = function(){
        this.isInitialized() ? $(selector).tooltipster('destroy') : null;
    };

    this.initialize();
};
