$(document).ready(function(){
    var overallViewModel = function(){
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
                results: ko.observableArray([])
            };
            self.filter = {
                profile: ko.observable(),
                discipline: ko.observable(),
                group: ko.observable(),
                startDate: ko.observable(new Date()),
                endDate: ko.observable(new Date()),
                criterion: ko.observable(criterion.mark),

                profiles: ko.observableArray([]),
                disciplines: ko.observableArray([]),
                groups: ko.observableArray([]),

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
                    startDate: function(){},
                    endDate: function(){}
                },
                clear: function(){
                    self.filter
                        .startDate(new Date())
                        .endDate(new Date())
                        .profile(null);
                }
            };


            self.actions = {
                date: {
                    start: function(){

                    },
                    end: function(){}
                }
            };

            self.get = {
                settings: function(){
                    var json = JSON.stringify({
                        settings: [
                            "overall_profile",
                            "overall_discipline",
                            "overall_group",
                            "overall_start_date",
                            "overall_end_date",
                            "overall_criterion"
                        ]
                    });
                    $ajaxpost({
                        url: '/api/uisettings/get',
                        data: json,
                        successCallback: function(data){
                            self.initial.settings(data);
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

    ko.applyBindings(overallViewModel());
});