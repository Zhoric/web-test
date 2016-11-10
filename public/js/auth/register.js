/**
 * Created by nyanjii on 01.10.16.
 */
$(document).ready(function(){

    ko.validation.init({
        insertMessages: false
    });

    var registerViewModel = function(){
        return new function(){
            var self = this;

            self.user = {
                name : ko.observable().extend({}),
                email: ko.observable().extend({}),
                password: ko.observable().extend({}),
                admissionYear: ko.observable().extend({}),
                group: ko.observable()
            };
            self.groups = ko.observableArray([]);
            self.registerResult = ko.observable();

            self.stringify = function(){

                var names = self.user.name().split(' ');
                var user = {};

                user.lastname = names[0];
                user.firstname = names[1];
                user.patronymic = names[2];
                user.email = self.user.email();
                user.password = self.user.password();


                return JSON.stringify({
                    user: user,
                    groupId: self.user.group().id()
                });
            };
            self.register = function(){
                var url = '/register';
                var json = self.stringify();
                $.post(url, json, function(response){
                    self.registerResult(ko.mapping.fromJSON(response));
                    self.modal('#register-info', '');
                });
            };

            self.check = {
                email: function(){
                    //TODO:[UI] добавить проверку занят ли такой email
                },
                //TODO:[UI] добавить валидацию с тултипами
            };
            self.get = {
                groups: function(){
                    $.get('api/groups/', function(response){
                        self.groups(ko.mapping.fromJSON(response)());
                    });
                }
            };
            self.modal = function(selector, action){
                $(selector).arcticmodal(action);
            };
            self.acceptInformation = function(){
                self.modal('register-info', 'close');
                if (self.registerResult().success()){
                    window.location.href = '/login';
                }
            };

            self.get.groups();

            return {
                user: self.user,
                register: self.register,
                registerResult: self.registerResult,
                acceptInformation: self.acceptInformation,
                groups: self.groups
            };
        };
    };

    ko.applyBindings(registerViewModel());
});