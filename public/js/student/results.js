/**
 * Created by nyanjii on 10.12.16.
 */
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
                discipline: ko.observable()
            };

            self.alter = {

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
                    $.get('/api/disciplines/testresults', function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            self.filter.disciplines(result.Data());
                            // TODO:[UI] УДАЛИТЬ
                            self.filter.discipline(result.Data()[1]);
                            //
                            return;
                        }
                        self.errors.show(result.Message());
                    })
                },
                results: function(){
                    var url = '/api/results/discipline/' + self.filter.discipline().id();
                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            console.log(result);
                            self.current.results(result.Data());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                result: function(){
                    var url = '/api/results/show/' + self.current.id();
                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            if (result.Data.test.type() === 2){
                                self.current.details(result.Data);
                            }
                            console.log(self.current.details());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
            };
            self.get.disciplines();

            self.filter.discipline.subscribe(function(value){
                if (value){
                    self.get.results();
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