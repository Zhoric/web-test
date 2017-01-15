/**
 * Created by nyanjii on 10.11.16.
 */
$(document).ready(function(){

    var loginViewModel = function(){
        return new function(){
            var self = this;

            self.errors = errors();

            self.user = {
                email: ko.observable(''),
                password: ko.observable('')
            };

            self.login = function(){
                var json = ko.mapping.toJSON(self.user);
                $post('/login', json, self.errors, function(data){
                    var location = '/login';
                    switch (data()){
                        case role.student.name:
                            location = role.student.location;
                            break;
                        case role.admin.name:
                            location = role.admin.location;
                            break;
                        case role.lecturer.name:
                            location = role.lecturer.location;
                            break;
                    }
                    window.location.href = location;
                })();
            };

            return {
                user: self.user,
                login: self.login,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(loginViewModel());
});