$(document).ready(function(){
    var resultsViewModel = function(){
        return new function(){
            var self = this;

            self.page = ko.observable(menu.admin.results);
            self.theme = ko.observable({});
            self.errors = errors();
            self.user = new user();
            self.user.read(self.errors);
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
                    group: function(id){
                        var id = id || self.settings().result_group;
                        if (!id) return;
                        self.filter.groups().find(function(item){
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
                clear: function(){
                    self.filter.profile() ? self.filter.profile(null) : null;
                    self.filter.group() ? self.filter.group(null) : null;
                    self.filter.discipline() ? self.filter.discipline(null) : null;
                    self.filter.test() ? self.filter.test(null) : null;
                    self.settings(null);
                }
            };


            self.actions = {
                show: function(data){
                    window.location.href = '/admin/result/' + data.id();
                },
                overall: function(){
                    self.post.settings({'overall_profile': self.filter.profile().id()});
                    self.post.settings({'overall_discipline': self.filter.discipline().id()});
                    self.post.settings({'overall_group': self.filter.group().id()});
                    window.location.href = '/admin/overallresults';
                }
            };

            self.get = {
                settings: function(){
                    var json = JSON.stringify({
                        settings: [
                            "result_profile",
                            "result_discipline",
                            "result_group",
                            "result_test"
                        ]
                    });
                    $ajaxpost({
                        url: '/api/uisettings/get',
                        data: json,
                        errors: self.errors,
                        successCallback: function(data){
                            self.settings(data);
                            self.get.profiles();
                        },
                        errorCallback: function(){
                            self.settings(null);
                            self.get.profiles();
                        }
                    });
                },
                profiles: function(){
                    $ajaxget({
                        url: '/api/profiles',
                        errors: self.errors,
                        successCallback: function(data){
                            self.filter.profiles(data());
                            self.settings() ? self.filter.set.profile() : null;
                        }
                    });
                },
                disciplines: function(){
                    $ajaxget({
                        url: '/api/profile/'+ self.filter.profile().id() +'/disciplines',
                        errors: self.errors,
                        successCallback: function(data){
                            self.filter.disciplines(data());
                            self.settings() ? self.filter.set.discipline() : null;
                        }
                    });
                },
                groups: function(){
                    $ajaxget({
                        url: '/api/profile/'+ self.filter.profile().id() +'/groups',
                        errors: self.errors,
                        successCallback: function(data){
                            self.filter.groups(data());
                            self.settings() ? self.filter.set.group() : null;
                        }
                    });
                },
                tests: function(){
                    $ajaxget({
                        url: '/api/disciplines/' + self.filter.discipline().id()+ '/tests',
                        errors: self.errors,
                        successCallback: function(data){
                            self.filter.tests(data());
                            self.settings() ? self.filter.set.test() : null;
                        }
                    });
                },
                results: function(){
                    var group = self.filter.group();
                    var test = self.filter.test();

                    if (!group || !test) return;

                    $ajaxget({
                        url: '/api/results/show?groupId='+ group.id() + '&testId=' + test.id(),
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.results(data());
                        }
                    });
                }
            };
            self.post = {
                settings: function(settings){
                    $ajaxpost({
                        url: '/api/uisettings/set',
                        errors: self.errors,
                        data: JSON.stringify({settings: settings})
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
                page: self.page,
                user: self.user,
                current: self.current,
                actions: self.actions,
                filter: self.filter,
                showResult: self.showResult,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(resultsViewModel());
});