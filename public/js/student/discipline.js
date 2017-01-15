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
                    url = '/api/disciplines/' + id;

                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            self.current.discipline.id(result.Data.id());
                            self.current.discipline.name(result.Data.name());
                            self.get.tests();
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                tests: function(){
                    var url = '/api/tests/showForStudent?discipline=' + self.current.discipline.id();
                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            console.log(result);
                            self.current.tests(result.Data());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
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