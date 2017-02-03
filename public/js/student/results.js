$(document).ready(function(){

    var resultsViewModel = function(){
        return new function(){
            var self = this;

            self.page = ko.observable(menu.student.results);
            self.errors = errors();

            self.current = {
                results: ko.observableArray([]),
                id: ko.observable(0),
                result: ko.observable(),
                details: ko.observable(null),
                question: ko.observable(null)
            };

            self.filter = {
                disciplines: ko.observableArray([]),
                discipline: ko.observable(),
                state: ko.observable(),

                clear: function(){
                    self.filter.discipline(null);
                }
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
                            self.filter.disciplines(data());
                        }
                    });
                },
                results: function(){
                    $ajaxget({
                        url: '/api/results/discipline/' + self.filter.discipline().id(),
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.results(data());
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
                }
            };
            self.get.disciplines();

            self.filter.discipline.subscribe(function(value){
                if (value){
                    self.get.results();
                }
                else{
                    self.current.results([]);
                }
            });

            return {
                page: self.page,
                current: self.current,
                filter: self.filter,
                actions: self.actions,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(resultsViewModel());
});