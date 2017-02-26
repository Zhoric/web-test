$(document).ready(function(){
    var monitoringViewModel = function(){
        return new function(){
            var self = this;
            self.page = ko.observable(menu.admin.results);
            self.errors = errors();
            self.user = new user();
            self.user.read(self.errors);

            self.initial = {
                settings: ko.observable(null)
            };
            self.current = {
                results: ko.observableArray([]),
                refreshTime: ko.observable(interval.thirtysec)
            };
            self.filter = {
                profile: ko.observable(),
                discipline: ko.observable(),
                group: ko.observable(),
                test: ko.observable(),
                state: ko.observable('any'),
                interval: ko.observable(interval.thirtysec),

                profiles: ko.observableArray([]),
                disciplines: ko.observableArray([]),
                groups: ko.observableArray([]),
                tests: ko.observableArray([]),

                set: {
                    profile: function(){
                        if (!self.initial.settings()) return;
                        var id = self.initial.settings().monitoring_profile;
                        if (!id) return;
                        self.filter.profiles().find(function(item){
                            if (item.id() == id()){
                                self.filter.profile(item);
                            }
                        });
                    },
                    discipline: function(){
                        if (!self.initial.settings()) return;
                        var id = self.initial.settings().monitoring_discipline;
                        if (!id) return;
                        self.filter.disciplines().find(function(item){
                            if (item.id() == id()){
                                self.filter.discipline(item);
                            }
                        });
                    },
                    group: function(){
                        if (!self.initial.settings()) return;
                        var id = self.initial.settings().monitoring_group;
                        if (!id) return;
                        self.filter.groups().find(function(item){
                            if (item.id() == id()){
                                self.filter.group(item);
                            }
                        });
                    },
                    test: function(){
                        if (!self.initial.settings()) return;
                        var id = self.initial.settings().monitoring_test;
                        if (!id) return;
                        self.filter.tests().find(function(item){
                            if (item.id() == id()){
                                self.filter.test(item);
                            }
                        });
                    },
                    state: function(){
                        self.filter.state('any');
                        if (!self.initial.settings()) return;
                        var value = self.initial.settings().monitoring_state;
                        if (!value()) return;
                        if (value() === 'any' ||
                            value() === 'process' ||
                            value() === 'finished')
                            self.filter.state(value());
                    },
                    interval: function(){
                        self.filter.interval(interval.thirtysec);
                        if (!self.initial.settings()) return;
                        var value = self.initial.settings().monitoring_interval;
                        if (!value) return;
                        if ($.isNumeric(value())) self.filter.interval(value());
                    }
                },
                get: {
                  state: function(){
                      switch (self.filter.state()){
                          case 'any':
                              return null;
                              break;
                          case 'process':
                              return 1;
                              break;
                          case 'finished':
                              return 2;
                              break;
                      }
                  }
                },
                clear: function(){
                    self.filter.profile(null);
                    self.filter.state('any');
                    self.filter.interval(interval.thirtysec);
                }
            };


            self.actions = {
                setInterval: function(data, e){
                    self.filter.interval(+$(e.target).attr('secs'));
                }
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
                    var callback = function(){
                        self.get.profiles();
                        self.filter.set.state();
                        self.filter.set.interval();
                    };
                    $ajaxpost({
                        url: '/api/uisettings/get',
                        data: json,
                        errors: self.errors,
                        successCallback: function(data){
                            self.initial.settings(data);
                            callback();
                        },
                        errorCallback: function(){
                            self.initial.settings(null);
                            callback();
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
                results: function(){
                    var test = '?testId=' + self.filter.test().id();
                    var group = '&groupId=' + self.filter.group().id();
                    var state = self.filter.get.state() ? '&state=' + self.filter.get.state() : '';

                    $ajaxget({
                        url:  '/api/tests/sessions' + test + group + state,
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
                    .discipline(null)
                    .groups([])
                    .group(null);
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

            //TIMER
            var timer;
            self.filter.interval.subscribe(function(value){
                self.post.settings({'monitoring_interval': self.filter.interval()});

                if (timer) clearInterval(timer);
                if ($.isNumeric(value)) self.current.refreshTime(value);
                timer = setInterval(function(){
                    var time = self.current.refreshTime() - 1000;
                    self.current.refreshTime(time);
                }, 1000);
            });
            self.current.refreshTime.subscribe(function(value){
                if (value) return;
                self.get.results();
                self.current.refreshTime(self.filter.interval());
            });

            return {
                page: self.page,
                user: self.user,
                current: self.current,
                filter: self.filter,
                actions: self.actions,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(monitoringViewModel());
});