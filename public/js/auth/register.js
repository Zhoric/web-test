/**
 * Created by nyanjii on 01.10.16.
 */
$(document).ready(function(){

    ko.validation.init();

    var registerViewModel = function(){
        return new function(){
            var self = this;

            self.user = {
                name : ko.observable('').extend({
                }),
                email: ko.observable('').extend({

                }),
                password: ko.observable('').extend({
                }),
                admissionYear: ko.observable('').extend({

                }),
                //TODO: добавить зависимоть на группу
                group: ko.observable('').extend()
            };

            self.stringify = function(){
                var names = self.user.name().split(' ');
                var user = {};
                user.lastname = names[0];
                user.firstname = names[1];
                user.patronymic = names[2];
                user.email = self.user.email();
                user.password = self.user.password();

                return JSON.stringify({user: user});
            };
            self.register = function(){
                var url = '/register';
                var json = self.stringify();
                $.post(url, json, function(response){
                    if (response) console.log(response);
                });
            };
            self.sendRegisterRequest = function(){

            };

            self.check = {
                login: function(){
                    //TODO: добавить проверку занят ли такой логин
                },
            };

            return {
                user: self.user,
                register: self.register
            };
        };
    };

    ko.applyBindings(registerViewModel());
});