//var doubleNameRegex = '^[А-ЯЁ][а-яё]+(\-{1}(?:[А-ЯЁ]{1}(?:[а-яё]+)))?$';
$(document).ready(function(){

    ko.validation.init({
        messagesOnModified: true,
        insertMessages: false
    });

    var registerViewModel = function(){
        return new function(){
            var self = this;

            self.errors = modals('errors');
            self.validation = {};
            self.events = new validationEvents(self.validation);

            self.user = ko.validatedObservable({
                lastname: ko.observable().extend({
                    required: true,
                    pattern: '^[А-ЯЁ][а-яё]+(\-{1}(?:[А-ЯЁ]{1}(?:[а-яё]+)))?$',
                    maxLength: 80
                }),
                firstname:  ko.observable().extend({
                    required: true,
                    pattern: '^[А-ЯЁ][а-яё]+(\-{1}(?:[А-ЯЁ]{1}(?:[а-яё]+)))?$',
                    maxLength: 80
                }),
                patronymic: ko.observable().extend({
                    pattern: '^[А-ЯЁ][а-яё]+(\-{1}(?:[А-ЯЁ]{1}(?:[а-яё]+)))?$',
                    maxLength: 80
                }),
                email: ko.observable().extend({
                    required: true,
                    email: true
                }),
                password: ko.observable().extend({
                    required: true,
                    minLength: 6,
                    maxLength: 16
                }),
                group: ko.observable().extend({
                    required: true
                })
            });
            self.groups = ko.observableArray([]);
            self.registerResult = {
                success: ko.observable(),
                message: ko.observable('')
            };

            self.register = function(){
                var user = ko.mapping.toJS(self.user);
                delete user.group;

                var json = JSON.stringify({
                    user: user,
                    groupId: self.user().group().id()
                });

                $ajaxpost({
                    url: '/register',
                    data: json,
                    errors: self.errors,
                    successCallback: function(data){
                        self.registerResult
                            .success(true)
                            .message(data());
                        commonHelper.modal.open('#register-info');
                    },
                    errorCallback: function(data){
                        self.registerResult
                            .success(false)
                            .message(data());
                    }
                });
            };

            self.check = {
                user: function(){
                    if (!self.user.isValid()){
                        self.events.showTooltip.overall();
                        if (!self.user().group.isValid()){
                            self.events.showTooltip.group();
                            return false;
                        }
                        return false;
                    }
                    self.events.closeTooltip.overall();
                    return true;
                }
            };
            self.get = {
                groups: function(){
                    $ajaxget({
                        url: 'api/groups/',
                        errors: self.errors,
                        successCallback: function(data){
                            self.groups(data());
                        }
                    });
                }
            };

            self.acceptInformation = function(){
                commonHelper.modal.close('register-info');
                if (self.registerResult.success()){
                    window.location.href = '/login';
                }
            };

            self.get.groups();
            commonHelper.buildValidationList(self.validation);

            return {
                user: self.user,
                register: self.register,
                registerResult: self.registerResult,
                acceptInformation: self.acceptInformation,
                groups: self.groups,
                events: self.events,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(registerViewModel());
});
//# sourceMappingURL=register.js.map
