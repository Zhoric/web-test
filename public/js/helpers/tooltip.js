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
        this.text();
        $(selector).tooltipster('open');
    };
    this.text = function(){
        $(selector).tooltipster('content', init.text());
    };
    this.close = function(){
        if (!this.isInitialized()) return;
        $(selector).tooltipster('close');
    };

    this.initialize();
};
