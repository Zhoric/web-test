var user = function(){
    var self = this;
    this.id = ko.observable();
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
    this.password = {
        new: ko.observable().extend({
            required: true,
            minLength: 6,
            maxLength: 16
        }),
        repeat: ko.observable().extend({
            required: {
                options: true,
                message: 'Пожалуйста, продублируйте новый пароль'
            },
            minLength: 6,
            maxLength: 16
        }),
        change: function(){
            commonHelper.modal.open('#change-user-password-modal');
            if (this.mode) this.mode(state.none);
            commonHelper.buildValidationList(this.validation);
        },
        approve: function(){
            var vm = this;
            if (self.password.new() !== self.password.repeat()){
                vm.validation[$('[accept-validation]').attr('id')].open();
                return;
            }
            $ajaxpost({
                url: '/api/user/setPassword',
                data: JSON.stringify({
                    userId: self.id(),
                    password: self.password.new()
                }),
                errors: vm.errors,
                successCallback: function(){
                    self.password.cancel();
                    vm.inform.show({
                        message: 'Пароль успешно изменен'
                    });
                },
                errorCallback: function(){
                    self.password.cancel();
                }
            });
        },
        cancel: function(){
            self.password.new(null).repeat(null);
            commonHelper.modal.close('#change-user-password-modal');
        }
    };

    this.read = function(errors){
        $ajaxget({
            url: '/api/user/current',
            errors: errors,
            successCallback: function(data){
                self.id(data.id());
                self.email(data.email());
                self.lastname(data.lastname());
                self.firstname(data.firstname());
                self.patronymic(data.patronymic());
                self.role(data.role());
            }
        });
    };
};



var initializeViewModel = function(init){
    if (init.page) this.page = ko.observable(init.page);
    this.errors = modals('errors');
    this.confirm = modals('confirm');
    this.inform = modals();
    this.validation = {};
    this.events = new validationEvents(this.validation);
    this.user = new user();
    this.user.read(this.errors);
    if (init.multiselect) this.multiselect = new multiselect(init.multiselect);
    if (init.mode) this.mode = ko.observable(state.none);
    if (init.pagination){
        this.pagination = pagination();
        this.pagination.pageSize(init.pagination);
    }
};

var returnStandart = function(){
    var self = this;
    var vm  = {
        page: self.page,
        errors: self.errors,
        confirm: self.confirm,
        inform: self.inform,
        validation: self.validation,
        events: self.events,
        user: self.user,
        current: self.current,
        actions: self.actions
    };
    if (self.alter) vm.alter = self.alter;
    if (self.filter) vm.filter = self.filter;
    if (self.pagination) vm.pagination = self.pagination;
    if (self.multiselect) vm.multiselect = self.multiselect;
    if (self.mode) vm.mode = self.mode;
    if (self.code) vm.code = self.code;
    if (self.initial) vm.initial = self.initial;
    if (self.allowTimer) vm.timer = self.allowTimer;

    return vm;
};