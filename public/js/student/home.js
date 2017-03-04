$(document).ready(function(){
    var homeViewModel = function(){
        return new function(){
            var self = this;
            initializeViewModel.call(self, {page: menu.student.main});

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
            return returnStandart.call(self);
        };
    };

    ko.applyBindings(homeViewModel());
});