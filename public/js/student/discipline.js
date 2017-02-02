/**
 * Created by nyanjii on 05.12.16.
 */
$(document).ready(function(){

    var disciplineTestsViewModel = function(){
        return new function(){
            var self = this;

            self.errors = errors();

            self.current = {
                discipline: {
                    id: ko.observable(),
                    name: ko.observable()
                },
                tests: ko.observableArray([])
            };

            self.filter = {

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

                    var requestParams = {
                        url : '/api/disciplines/' + id,
                        successCallback: function(data){
                            self.current.discipline.id(data.id());
                            self.current.discipline.name(data.name());
                            self.get.tests();
                        }
                    };

                    $ajaxget(requestParams);
                },
                tests: function(){
                    var requestParams = {
                        url: '/api/tests/showForStudent?discipline=' + self.current.discipline.id(),
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.tests(data());
                        }
                    };

                    $ajaxget(requestParams);
                }
            };
            self.get.discipline();

            return {
                current: self.current,
                actions: self.actions,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(disciplineTestsViewModel());
});