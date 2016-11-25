/**
 * Created by nyanjii on 10.11.16.
 */
$(document).ready(function(){

    var loginViewModel = function(){
        return new function(){
            var self = this;

            self.errors = {
                message: ko.observable(),
                show: function(message){
                    self.errors.message(message);
                    self.toggleModal('#errors-modal', '');
                },
                accept: function(){
                    self.toggleModal('#errors-modal', 'close');
                }
            };

            self.user = {
                email: ko.observable(''),
                password: ko.observable('')
            };
            self.loginResult = ko.observable();

            self.stringify = function(){
                var user = {
                    email: self.user.email(),
                    password: self.user.password()
                };
                return JSON.stringify(user);
            };
            self.login = function(){
                 var url = '/login';
                 var json = self.stringify();
                $.post(url, json, function(response){
                    self.loginResult(ko.mapping.fromJSON(response));
                    self.loginResult().success() ? window.location.href = '/home' : self.modal('#login-info', '');
                });
            };
            self.acceptInformation = function(){
                self.modal('#login-info', 'close');
            };
            self.modal = function(selector, action){
                $(selector).arcticmodal(action);
            };

            return {
                user: self.user,
                login: self.login,
                loginResult: self.loginResult,
                acceptInformation: self.acceptInformation
            };
        };
    };

    ko.applyBindings(loginViewModel());
});