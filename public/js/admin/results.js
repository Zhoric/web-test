/**
 * Created by nyanjii on 19.10.16.
 */
$(document).ready(function(){
    var resultsViewModel = function(){
        return new function(){
            var self = this;

            self.theme = ko.observable({});

            self.settings = ko.observable(null);

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
                tests: ko.observableArray([]),

                set: {
                    profile: function(){
                        var id = self.settings().result_profile;
                        if (!id) return;
                        self.filter.profiles().find(function(item){
                            if (item.id() == id()){
                                self.filter.profile(item);
                            }
                        });
                    },
                    discipline: function(){
                        var id = self.settings().result_discipline;
                        if (!id) return;
                        self.filter.disciplines().find(function(item){
                            if (item.id() == id()){
                                self.filter.discipline(item);
                            }
                        });
                    },
                    group: function(){
                        var id = self.settings().result_group;
                        if (!id) return;
                        self.filter.groups().find(function(item){
                            console.log(item.id() + ' ' + id());
                            if (item.id() == id()){
                                self.filter.group(item);
                            }
                        });
                    },
                    test: function(){
                        var id = self.settings().result_test;
                        if (!id) return;
                        self.filter.tests().find(function(item){
                            if (item.id() == id()){
                                self.filter.test(item);
                            }
                        });
                    }
                },
                clear: function(){}
            };
            self.errors = errors();

            self.showResult = function(data){
                window.location.href = '/admin/result/' + data.id();
            };
            self.actions = {
                parseDate: function(){
                    self.current.results().find(function(item){
                        var date = item.dateTime.date;
                        date(commonHelper.parseDate(date()));
                    });
                }
            };

            self.get = {
                settings: function(){
                    var url = '/api/uisettings/get';
                    var json = JSON.stringify({
                        settings: [
                            "result_profile",
                            "result_discipline",
                            "result_group",
                            "result_test"
                        ]
                    });
                    $.post(url, json, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            self.settings(result.Data);
                            console.log(self.settings());
                            self.get.profiles();
                            return;
                        }
                        self.errors.show(result.Message());
                        self.settings(null);
                        self.get.profiles();
                    });
                },
                profiles: function(){
                    $.get('/api/profiles', function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            self.filter.profiles(result.Data());
                            if (self.settings()) {
                                self.filter.set.profile();
                            }
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                disciplines: function(){
                    var url = '/api/profile/'+ self.filter.profile().id() +'/disciplines';
                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            self.filter.disciplines(result.Data());
                            if (self.settings()){
                                self.filter.set.discipline();
                            }
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
                            if (self.settings()){
                                self.filter.set.group();
                            }
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
                            if (self.settings()){
                                self.filter.set.test();
                            }
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                results: function(){
                    var group = self.filter.group();
                    var test = self.filter.test();

                    if (!group || !test) return;

                    var url = '/api/results/show?groupId='+ group.id() + '&testId=' + test.id();

                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()) {
                            self.current.results(result.Data());
                            self.actions.parseDate();
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
            };
            self.post = {
                settings: function(settings){
                    var url = '/api/uisettings/set';
                    var json = JSON.stringify({
                        settings: settings
                    });
                    $.post(url, json, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (!result.Success()){
                            self.errors.show(result.Message());
                        }
                    });
                }
            };

            self.get.settings();

            //SUBSCRIPTIONS

            self.filter.profile.subscribe(function(value){
                if (value){
                    self.post.settings({'result_profile': self.filter.profile().id()});
                    self.get.groups();
                    self.get.disciplines();
                    return;
                }
                self.filter
                    .disciplines([])
                    .groups([]);
                self.post.settings({'result_profile': null});
            });
            self.filter.discipline.subscribe(function(value){
                if (value){
                    self.post.settings({'result_discipline': self.filter.discipline().id()});
                    self.get.tests();
                    return;
                }
                self.filter.tests([]);
                self.post.settings({'result_discipline': null});
            });
            self.filter.group.subscribe(function(value){
                if (value){
                    self.post.settings({'result_group': self.filter.group().id()});
                    return;
                }
                self.post.settings({'result_group': null});
            });
            self.filter.test.subscribe(function(value){
                if (value){
                    self.post.settings({'result_test': self.filter.test().id()});
                    self.get.results();
                    return;
                }
                self.current.results([]);
                self.post.settings({'result_test': null});
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