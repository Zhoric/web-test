/**
 * Created by nyanjii on 19.10.16.
 */
$(document).ready(function(){
    var resultsViewModel = function(){
        return new function(){
            var self = this;

            self.theme = ko.observable({});

            self.current = {
                results: ko.observableArray([])
            };
            self.filter = {
                profile: ko.observable(),
                discipline: ko.observable(),
                group: ko.observable(),
                test: ko.observable(),

                profiles: ko.observableArray([]),
                disciplines: ko.observableArray([]),
                groups: ko.observableArray([]),
                tests: ko.observableArray([])
            };
            self.errors = {
                message: ko.observable(),
                show: function(message){
                    self.errors.message(message);
                    self.toggleModal('#errors-modal', '');
                },
                accept: function(){
                    self.toggleModal('#errors-modal', 'close');
                }
            };

            self.showResult = function(data){
                window.location.href = '/admin/result/' + data.id();
            },

            self.get = {
                profiles: function(){
                    $.get('/api/profiles', function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            self.filter.profiles(result.Data());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                disciplines: function(){
                    $.get('/api/disciplines', function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            self.filter.disciplines(result.Data());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                groups: function(){
                    var url = '/api/profile/'+ self.filter.profile().id() +'/groups';
                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            self.filter.groups(result.Data());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                tests: function(){
                    var url = '/api/disciplines/' + self.filter.discipline().id()+ '/tests';
                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()) {
                            self.filter.tests(result.Data());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                results: function(){
                    var group = self.filter.group().id();
                    var test = self.filter.test().id();
                    var url = '/api/results/show?groupId='+ group + '&testId=' + test;

                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()) {
                            self.current.results(result.Data());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
            };

            self.get.profiles();
            self.get.disciplines();

            //SUBSCRIPTIONS

            self.filter.profile.subscribe(function(value){
                if (value){
                    self.get.groups();
                }
            });
            self.filter.discipline.subscribe(function(value){
                if (value){
                    self.get.tests();
                }
            });
            self.filter.test.subscribe(function(value){
                if (value){
                    self.get.results();
                }
            });


            return {
                current: self.current,
                filter: self.filter,
                showResult: self.showResult,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(resultsViewModel());
});