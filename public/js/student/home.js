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
                type: ko.observable(testStatus.all),
                clear: function(){
                    self.filter.name('')
                        .type(testStatus.all);
                }
            };
            self.current = {
                user: ko.observable(),
                disciplines: ko.computed(function() {
                    var initial = self.initial.disciplines();
                    var name = self.filter.name().toLowerCase();
                    return ko.utils.arrayFilter(initial, function(item) {
                        var type = false;
                        switch(self.filter.type()){
                            case testStatus.all:
                                type = true;
                                break;
                            case testStatus.left:
                                type = (item.testsCount() - item.testsPassed()) > 0;
                                break;
                            case testStatus.none:
                                type = (item.testsCount() - item.testsPassed()) === 0;
                        }
                        return type &&
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