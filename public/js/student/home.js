$(document).ready(function(){
    var homeViewModel = function(){
        return new function(){
            var self = this;
            self.page = ko.observable(menu.student.main);
            self.errors = errors();
            self.current = {
                user: ko.observable(),
                disciplines: ko.observableArray([])
            };
            self.filter = {
                name: ko.observable(''),
                clear: function(){
                    self.filter.name('');
                }
            };
            self.actions = {
                details: function(data){
                    window.location.href = '/discipline/' + data.discipline.id();
                },
                logout: function(){
                    window.location.href = '/logout';
                },
                percentage: function(data){
                    var percent = data.testsPassed() /  data.testsCount() * 100;
                    return ko.observable(percent);
                }
            };
            self.get = {
                disciplines: function(){
                    $ajaxget({
                        url: '/api/disciplines/actual',
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.disciplines(data());
                        }
                    });
                }
            };
            self.get.disciplines();

            self.filter.name.subscribe(function(){
                //self.get.disciplines();
            });
            // TODO: подумать о реализации фильтра
            return {
                page: self.page,
                current: self.current,
                filter: self.filter,
                actions: self.actions,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(homeViewModel());
});