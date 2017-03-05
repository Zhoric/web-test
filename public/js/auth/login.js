$(document).ready(function(){

    var loginViewModel = function(){
        return new function(){
            var self = this;

            self.errors = modals('errors');

            self.user = {
                email: ko.observable(''),
                password: ko.observable('')
            };
            self.enter = function(data, e){
                if (e.which === 13)
                    self.login();
            };
            self.login = function(){
                var json = ko.mapping.toJSON(self.user);

                var requestParams = {
                    url: '/login',
                    data: json,
                    errors: self.errors,
                    successCallback: function(data){
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
                    },
                    errorCallback: function(){
                        self.user.password("");
                    }
                };

                $ajaxpost(requestParams);
            };

            return {
                user: self.user,
                login: self.login,
                enter: self.enter,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(loginViewModel());
});