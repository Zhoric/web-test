$(document).ready(function(){
    var homeViewModel = function(){
        return new function(){
            var self = this;
            initializeViewModel.call(self, {page: menu.student.main});

            self.initial = {
                disciplines: ko.observableArray([])
            };
            self.filter = {
                name: ko.observable(''),
                type: ko.observable('all-disciplines'),
                clear: function(){
                    self.filter.name('')
                        .type('all-disciplines');
                }
            };
            self.current = {
                user: ko.observable(),
                disciplines: ko.computed(function() {
                    var initial = self.initial.disciplines();
                    var name = self.filter.name().toLowerCase();
                    var type = self.filter.type () === 'all-disciplines';
                    return ko.utils.arrayFilter(initial, function(item) {
                        return (!type ? (item.testsCount() - item.testsPassed()) > 0: true) &&
                            (item.discipline.name().toLowerCase().includes(name) ||
                            item.discipline.abbreviation().toLowerCase().includes(name));
                    });
                })
            };

            self.actions = {
                details: function(data){
                    window.location.href = '/discipline/' + data.discipline.id();
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
                            self.initial.disciplines(data());
                        }
                    });
                }
            };
            self.get.disciplines();

            return returnStandart.call(self);
        };
    };

    ko.applyBindings(homeViewModel());
});