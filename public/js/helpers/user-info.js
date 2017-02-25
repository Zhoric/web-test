var user = function(){
    var self = this;
    this.email = ko.observable();
    this.lastname = ko.observable();
    this.firstname = ko.observable();
    this.patronymic = ko.observable();
    this.role = ko.observable();
    this.name = ko.computed(function(){
        var lastName = self.lastname() ? self.lastname() + ' ' : '';
        var firstLetter = self.firstname() ? self.firstname().substr(0, 1) + '.': '';
        var secondLetter = self.patronymic() ? self.patronymic().substr(0, 1) + '.' : '';
        var result = lastName + firstLetter + secondLetter;
        
        return result ? result : 'Аноним';
    });

    this.read = function(errors){
        $ajaxget({
            url: '/api/user/current',
            errors: errors,
            successCallback: function(data){
                self.email(data.email());
                self.lastname(data.lastname());
                self.firstname(data.firstname());
                self.patronymic(data.patronymic());
                self.role(data.role());
                console.log(data);
            }
        });
    };
};