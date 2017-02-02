$(document).ready(function(){
    var monitoringViewModel = function(){
        return new function(){
            var self = this;
            self.percentage = ko.observable(20);

            self.initial = {
                settings: ko.observable(null)
            };
            self.current = {
                results: ko.observableArray([])
            };
            self.filter = {
                profile: ko.observable(),
                discipline: ko.observable(),
                group: ko.observable(),
                test: ko.observable(),
                state: ko.observable(),
                interval: ko.observable(),

                profiles: ko.observableArray([]),
                disciplines: ko.observableArray([]),
                groups: ko.observableArray([]),
                tests: ko.observableArray([]),
                intervals: ko.observableArray([{
                    id: ko.observable(1),
                    name: ko.observable('5 секунд')
                }, {
                    id: ko.observable(2),
                    name: ko.observable('30 секунд')
                },{
                    id: ko.observable(3),
                    name: ko.observable('1 минута')
                }, {
                    id: ko.observable(4),
                    name: ko.observable('5 минут')
                }]),

                set: {
                    profile: function(){
                        var id = self.initial.settings().monitoring_profile;
                        if (!id) return;
                        self.filter.profiles().find(function(item){
                            if (item.id() == id()){
                                self.filter.profile(item);
                            }
                        });
                    },
                    discipline: function(){
                        var id = self.initial.settings().monitoring_discipline;
                        if (!id) return;
                        self.filter.disciplines().find(function(item){
                            if (item.id() == id()){
                                self.filter.discipline(item);
                            }
                        });
                    },
                    group: function(){
                        var id = self.initial.settings().monitoring_group;
                        if (!id) return;
                        self.filter.groups().find(function(item){
                            if (item.id() == id()){
                                self.filter.group(item);
                            }
                        });
                    },
                    test: function(){
                        var id = self.initial.settings().monitoring_test;
                        if (!id) return;
                        self.filter.tests().find(function(item){
                            if (item.id() == id()){
                                self.filter.test(item);
                            }
                        });
                    },
                    state: function(){
                        var value = self.initial.settings().monitoring_state;
                        if (!value) return;
                        self.filter.state(value());
                    },
                    intervals: function(){
                        var id = self.initial.settings().monitoring_interval;
                        if (!id) return;
                        self.filter.intervals().find(function(item){
                            if (item.id() == id()){
                                self.filter.interval(item);
                            }
                        });
                    }
                },
                clear: function(){
                    self.filter.profile(null);
                    self.filter.state('any');
                    self.filter.interval(null);
                }
            };
            self.errors = errors();


            self.actions = {

            };

            self.get = {
                settings: function(){
                    var json = JSON.stringify({
                        settings: [
                            "monitoring_profile",
                            "monitoring_discipline",
                            "monitoring_group",
                            "monitoring_test",
                            "monitoring_state",
                            "monitoring_interval"
                        ]
                    });
                    $ajaxpost({
                        url: '/api/uisettings/get',
                        data: json,
                        successCallback: function(data){
                            self.initial.settings(data);
                            self.get.profiles();
                            self.filter.set.state();
                            self.filter.set.intervals();
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
                        successCallback:function(data){
                            self.filter.profiles(data());
                            self.filter.set.profile();
                        }
                    });
                },
                disciplines: function(){
                    $ajaxget({
                        url: '/api/profile/'+ self.filter.profile().id() +'/disciplines',
                        errors: self.errors,
                        successCallback: function(data){
                            self.filter.disciplines(data());
                            self.filter.set.discipline();
                        }
                    });
                },
                groups: function(){
                    $ajaxget({
                        url: '/api/profile/'+ self.filter.profile().id() +'/groups',
                        errors: self.errors,
                        successCallback: function(data){
                            self.filter.groups(data());
                            self.filter.set.group();
                        }
                    });
                },
                tests: function(){
                    $ajaxget({
                        url: '/api/disciplines/' + self.filter.discipline().id()+ '/tests',
                        errors: self.errors,
                        successCallback: function(data){
                            self.filter.tests(data());
                            self.filter.set.test();
                        }
                    });
                },
                results: function(){}
            };

            self.post = {
                settings: function(settings){
                    $ajaxpost({
                        url: '/api/uisettings/set',
                        data: JSON.stringify({
                            settings: settings
                        })
                    });
                }
            };

            self.get.settings();

            //SUBSCRIPTIONS

            self.filter.profile.subscribe(function(value){
                if (value){
                    self.post.settings({'monitoring_profile': self.filter.profile().id()});
                    self.get.groups();
                    self.get.disciplines();
                    return;
                }
                self.filter
                    .disciplines([])
                    .groups([]);
                self.post.settings({'monitoring_profile': null});
            });
            self.filter.discipline.subscribe(function(value){
                if (value){
                    self.post.settings({'monitoring_discipline': self.filter.discipline().id()});
                    self.get.tests();
                    return;
                }
                self.filter.tests([]);
                self.post.settings({'monitoring_discipline': null});
            });
            self.filter.group.subscribe(function(value){
                if (value){
                    self.post.settings({'monitoring_group': self.filter.group().id()});
                    return;
                }
                self.post.settings({'monitoring_group': null});
            });
            self.filter.test.subscribe(function(value){
                if (value){
                    self.post.settings({'monitoring_test': self.filter.test().id()});
                    self.get.results();
                    return;
                }
                self.current.results([]);
                self.post.settings({'monitoring_test': null});
            });
            self.filter.state.subscribe(function(){
                self.post.settings({'monitoring_state': self.filter.state()});
            });
            self.filter.interval.subscribe(function(value){
                value
                    ? self.post.settings({'monitoring_interval': self.filter.interval().id()})
                    : self.post.settings({'monitoring_interval': null});
            });


            return {
                current: self.current,
                filter: self.filter,
                showResult: self.showResult,
                errors: self.errors,
                percentage: self.percentage
            };
        };
    };

    ko.applyBindings(monitoringViewModel());
});