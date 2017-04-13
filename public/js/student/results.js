$(document).ready(function(){

    var resultsViewModel = function(){
        return new function(){
            var self = this;
            initializeViewModel.call(self, {page: menu.student.results});

            self.initial = {
                results: ko.observableArray([]),
                disciplines: ko.observableArray([])
            };

            self.filter = {
                discipline: ko.observable(),
                state: ko.observable(testCheckedStatus.all),

                clear: function(){
                    self.filter.discipline(null).state(testCheckedStatus.all);
                }
            };

            self.current = {
                results: ko.computed(function() {
                    var initial = self.initial.results();
                    return ko.utils.arrayFilter(initial, function(item) {
                        switch(self.filter.state()){
                            case testCheckedStatus.all:
                                return true;
                                break;
                            case testCheckedStatus.done:
                                return item.mark() !== null;
                                break;
                            case testCheckedStatus.not:
                                return item.mark() === null;
                                break;
                            default:
                                return true;
                        }
                    });
                }),
                id: ko.observable(0),
                result: ko.observable(),
                details: ko.observable(null),
                question: ko.observable(null),
                markScale: ko.observable(100)
            };



            self.actions = {
                show: {
                    result: function(data) {
                        var id = self.current.id;
                        self.current.details(null);

                        if (data.id() === id()) {
                            id(0);
                            return;
                        }

                        self.current.result(data);
                        id(data.id());
                        self.get.result();
                    },
                    question: function(data){
                        self.current.question(data);
                        commonHelper.modal.open('#question-details-modal');
                    }
                }
            };

            self.get = {
                disciplines: function(){
                    $ajaxget({
                        url: '/api/disciplines/testresults',
                        errors: self.errors,
                        successCallback: function(data){
                            self.initial.disciplines(data());
                        }
                    });
                },
                results: function(){
                    $ajaxget({
                        url: '/api/results/discipline/' + self.filter.discipline().id(),
                        errors: self.errors,
                        successCallback: function(data){
                            self.initial.results(data());
                        }
                    });
                },
                result: function(){
                    $ajaxget({
                        url: '/api/results/show/' + self.current.id(),
                        errors: self.errors,
                        successCallback: function(data){
                            if (data.test.type() === types.test.study.id){
                                self.current.details(data);
                            }
                        }
                    });
                },
                markScale: function(){
                    $ajaxget({
                        url: '/api/settings/get/maxMarkValue',
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.markScale(data.value());
                        }
                    });
                }
            };
            self.get.disciplines();
            self.get.markScale();

            self.filter.discipline.subscribe(function(value){
                if (value){
                    self.get.results();
                }
                else{
                    self.initial.results([]);
                }
            });

            return returnStandart.call(self);
        };
    };

    ko.applyBindings(resultsViewModel());
});