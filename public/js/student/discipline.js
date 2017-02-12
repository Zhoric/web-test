$(document).ready(function(){

    var disciplineTestsViewModel = function(){
        return new function(){
            var self = this;

            self.page = ko.observable(menu.student.main);
            self.errors = errors();

            self.current = {
                discipline: {
                    id: ko.observable(),
                    name: ko.observable()
                },
                tests: ko.observableArray([])
            };

            self.filter = {
                test: ko.observable(''),
                clear: function(){}
            };

            self.actions = {
                start: function(data){
                    window.location.href = '/test/'
                        + types.test.name(data.test.type()) + '/'
                        + data.test.id();
                }
            };

            self.get = {
                discipline: function(){
                    var url = window.location.href;
                    var id = +url.substr(url.lastIndexOf('/')+1);

                    $ajaxget({
                        url: '/api/disciplines/' + id,
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.discipline.id(data.id());
                            self.current.discipline.name(data.name());
                            self.get.tests();
                        }
                    });
                },
                tests: function(){
                    $ajaxget({
                        url: '/api/tests/showForStudent?discipline=' + self.current.discipline.id(),
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.tests(data());
                        }
                    });
                }
            };
            self.get.discipline();

            return {
                page: self.page,
                current: self.current,
                actions: self.actions,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(disciplineTestsViewModel());
});