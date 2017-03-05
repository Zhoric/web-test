var modals = function(type){
    switch(type){
        case 'errors':
            return new errors();
            break;
        case 'confirm':
            return new confirm();
            break;
        case 'info':
            return new info();
            break;
        default:
            return new info();
    }
};

function errors(){
    var self = this;
    var defaultErrorMessage = 'Произошла ошибка на сервере';
    this.message = ko.observable(defaultErrorMessage);

    this.show = function(message){
        if (!message.match("SQL") || !message.match("sql")) self.message(message);
        commonHelper.modal.open('#errors-modal');
    };
    this.accept = function(){
        commonHelper.modal.close('#errors-modal');
        setTimeout(function(){self.message(defaultErrorMessage);}, 500);
    };
}

function confirm() {
    var self = this;
    var defaultConfirmMessage = 'Вы действительно хотите выполнить действие?';
    var approveCallback = function(){};
    var cancelCallback = function(){};
    this.additionalText = ko.observable(null);
    this.additionalHtml = ko.observable(null);
    this.message = ko.observable(defaultConfirmMessage);


    this.show = function(settings){
        self.message(settings.message);
        if (settings.approve) approveCallback = settings.approve;
        if (settings.cancel) cancelCallback = settings.cancel;
        if (settings.additionalText) self.additionalText(settings.additionalText);
        if (settings.additionalHtml) self.additionalHtml(settings.additionalHtml);
        commonHelper.modal.open('#confirmation-modal');
    };
    this.approve = function(){
        approveCallback();
        commonHelper.modal.open('#confirmation-modal');
        return true;
    };
    this.cancel = function(){
        cancelCallback();
        commonHelper.modal.close('#confirmation-modal');
        setTimeout(function(){self.message(defaultConfirmMessage);}, 500);
        return false;
    }
}

function info(){
    var self = this;
    var callback = function(){};
    this.message = ko.observable(" ");
    this.additionalText = ko.observable(null);
    this.additionalHtml = ko.observable(null);

    this.show = function(settings){
        self.message(settings.message);
        if (settings.callback) callback = settings.callback;
        if (settings.additionalText) self.additionalText(settings.additionalText);
        if (settings.additionalHtml) self.additionalHtml(settings.additionalHtml);
        commonHelper.modal.open('#information-modal');
    };
    this.approve = function(){
        callback();
        commonHelper.modal.close('#information-modal');
        setTimeout(function(){
            self.message("");
            self.additionalText(null);
            self.additionalHtml(null);
        }, 500);
        return true;
    };
}