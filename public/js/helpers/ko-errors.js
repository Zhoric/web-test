function errors(){
    var self = this;

    self.message = ko.observable('');
    self.show = function(message){
        self.message(message);
        commonHelper.modal.open('#errors-modal');
    };
    self.accept = function(){
        commonHelper.modal.close('#errors-modal');
    };

    return {
        message: self.message,
        show: self.show,
        accept: self.accept
    };
}