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
                        var id = self.initial.settings().overall_profile;
                        if (!id) return;
                        self.filter.profiles().find(function(item){
                            if (item.id() == id()){
                                self.filter.profile(item);
                            }
                        });
                    },
                    discipline: function(){
                        var id = self.initial.settings().overall_discipline;
                        if (!id) return;
                        self.filter.disciplines().find(function(item){
                            if (item.id() == id()){
                                self.filter.discipline(item);
                            }
                        });
                    },
                    group: function(){
                        var id = self.initial.settings().overall_group;
                        if (!id) return;
                        self.filter.groups().find(function(item){
                            if (item.id() == id()){
                                self.filter.group(item);
                            }
                        });
                    },
                    startDate: function(){
                        var date = self.initial.settings().overall_start_date;
                        if (!date) return;
                        self.filter.startDate(date());
                    },
                    endDate: function(){
                        var date = self.initial.settings().overall_end_date;
                        if (!date) return;
                        self.filter.endDate(date());
                    },
                    criterion: function(){
                        var criterion = self.initial.settings().overall_criterion;
                        if (!criterion) return;
                        self.filter.criterion(criterion());
                    }
                },
                get: {
                    criterion: function(){
                        switch (self.filter.criterion()){
                            case criterion.mark:
                                return 1;
                                break;
                            case criterion.firstTry:
                                return 2;
                                break;
                            case criterion.secondTry:
                                return 3;
                                break;
                            default:
                                return 1;
                        }
                    }
                },
                clear: function(){
                    self.filter
                        .startDate(new Date())
                        .endDate(new Date())
                        .criterion(criterion.mark);
                    self.filter.profile() ? self.filter.profile(null) : null;
                    self.filter.discipline() ? self.filter.discipline(null) : null;
                    self.filter.group() ? self.filter.group(null) : null;
                }
            };


            self.actions = {
                results: function(){
                    window.location.href = '/admin/results';
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
                            self.filter.set.criterion();
                            self.filter.set.startDate();
                            self.filter.set.endDate();
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
                    if (!self.filter.group()) return;
                    if (!self.filter.discipline()) return;

                    var group = '?groupId=' + self.filter.group().id();
                    var discipline = '&disciplineId=' + self.filter.discipline().id();
                    var startDate = '&startDate=' + commonHelper.parseDate(self.filter.startDate());
                    var endDate = '&endDate=' + commonHelper.parseDate(self.filter.endDate());
                    var criterion = '&criterion=' + self.filter.get.criterion();


                    var url = '/api/results/getGroupResults' +
                        group + discipline +
                        startDate + endDate +
                        criterion;

                    return;
                    $ajaxget({
                        url: url,
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
                    self.post.settings({'overall_profile': self.filter.profile().id()});
                    self.get.groups();
                    self.get.disciplines();
                    return;
                }
                self.filter
                    .disciplines([])
                    .discipline(null)
                    .groups([])
                    .group(null);
                self.post.settings({'overall_profile': null});
            });
            self.filter.discipline.subscribe(function(value){
                value
                    ? self.post.settings({'overall_discipline': self.filter.discipline().id()})
                    : self.post.settings({'overall_discipline': null});
            });
            self.filter.group.subscribe(function(value){
                value
                    ? self.post.settings({'overall_group': self.filter.group().id()})
                    && self.get.results()
                    : self.current.results([])
                    && self.post.settings({'overall_group': null});

            });
            self.filter.startDate.subscribe(function(){
                self.get.results();
                self.post.settings({'overall_start_date': self.filter.startDate()});
            });
            self.filter.endDate.subscribe(function(){
                self.get.results();
                self.post.settings({'overall_end_date': self.filter.endDate()});
            });
            self.filter.criterion.subscribe(function(){
                self.get.results();
                self.post.settings({'overall_criterion': self.filter.criterion()});
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