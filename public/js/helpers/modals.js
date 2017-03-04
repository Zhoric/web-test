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
    this.message = ko.observable(defaultConfirmMessage);

    this.show = function(message){
        self.message(message);
        commonHelper.modal.open('#confirmation-modal');
    };
    this.accept = function(callback){
        if (typeof callback !== 'undefined') callback();
        commonHelper.modal.open('#confirmation-modal');
        return true;
    };
    this.cancel = function(){
        if (typeof callback !== 'undefined') callback();
        commonHelper.modal.close('#confirmation-modal');
        setTimeout(function(){self.message(defaultConfirmMessage);}, 500);
        return false;
    }
}

function info(){
    var self = this;
    this.message = ko.observable(" ");

    this.show = function(message){
        self.message(message);
        commonHelper.modal.open('#information-modal');
    };
    this.accept = function(){
        if (typeof callback !== 'undefined') callback();
        commonHelper.modal.close('#information-modal');
        setTimeout(function(){self.message("");}, 500);
        return true;
    };
}