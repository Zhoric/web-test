/**
 * Created by nyanjii on 01.10.16.
 */
$(document).ready(function(){

    ko.validation.init();


    var registerViewModel = function(){
        return new function(){
            var self = this;
            self.name = ko.observable('').extend({
                pattern: {
                    message: 'ФИО введены неправильно',
                }
            });
            self.login = ko.observable('').extend({
                required: {
                    message: 'Поле должо быть заполнено',
                    params: true
                },
                pattern: {
                    message: 'Логин должен содержать хотя бы одну цифру и одну букву',
                    params: '^(?=.*\d).{6,20}$'
                }
            });
            self.password = ko.observable('').extend({
                required: {
                    message: 'Поле должо быть заполнено',
                    params: true
                },
                pattern: {
                    message: 'Пароль должен содержать хотя бы одну цифру и одну букву',
                    params: '^(?=.*\d).{6,20}$'
                }
            });
            self.admissionYear = ko.observable('').extend({
                required: {
                    message: 'Поле должо быть заполнено',
                    params: true
                },
                digit: {
                    message: 'Как ты себе такой год представляешь?',
                    params: true
                },
                minLength: {
                    message: 'Год состоит из 4 цифр, абитура тупая',
                    params: 4
                },
                maxLength: {
                    message: 'Год состоит из 4 цифр, абитура тупая',
                    params: 4
                }
            });
            self.groupId = ko.observable(0);
            self.groups = ko.observableArray([]);
            self.group = ko.observable('');


            self.getGroups = function(){
                $.get('', {profileId: self.profileId()}, function(data){
                    var res = ko.mapping.fromJSON(data);
                    self.groups(res());
                });
            };

            self.register = function(){

            };
            self.sendRegisterRequest = function(){

            };

            self.checkIfValid = function(){
                console.log(self.isValid());
            };

            //self.getProfiles();
            return {
                name: self.name,
                login: self.login,
                password: self.password,
                admissionYear: self.admissionYear,
                profiles: self.profiles,
                groups: self.groups,
                profile: self.profile,
                group: self.group,
                register: self.register,
                checkIfValid: self.checkIfValid
            };
        };
    };

    ko.applyBindings(registerViewModel());
});