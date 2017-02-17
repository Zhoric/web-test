$(document).ready(function(){
    var groupsViewModel = function(){
        return new function(){
            var self = this;

            self.page = ko.observable(menu.admin.groups);
            self.errors = new errors();
            self.validation = {};
            self.events = new validationEvents(self.validation);
            self.pagination = pagination();
            self.pagination.pageSize(10);
            self.mode = ko.observable(state.none);

            self.initial = {
                profileId: ko.observable(null),
                unUrlProfileId: function(){
                    var url = window.location.href;
                    var id = +url.substring(url.lastIndexOf('/'));
                    if ($.isNumeric(id)){
                        self.initial.profileId(id);
                    }
                    self.get.groups();
                }
            };

            self.current = {
                groups: ko.observableArray([]),
                group: ko.validatedObservable({
                    id: ko.observable(''),
                    name: ko.observable('').extend({required: true}),
                    prefix: ko.observable('').extend({maxLength: 15}),
                    number: ko.observable('').extend({
                        required: true,
                        min: 1,
                        number: true
                    }),
                    isFulltime: ko.observable(true),
                    course: ko.observable('').extend({
                        min: 1,
                        required: true,
                        number: true
                    })
                }),

                institutes: ko.observableArray([]),
                institute: ko.observable(null),

                profiles: ko.observableArray([]),
                profile: ko.observable(null),

                plans: ko.observableArray([]),
                plan: ko.observable(null),

                groupPlan: ko.validatedObservable(null).extend({required: true}),
                isGenerated: ko.observable(false)
            };
            self.filter = {
                name: ko.observable(''),
                clear: function(){
                    self.filter.name('');
                }
            };
            self.alter = {
                stringify: {
                    group: function(){
                        var result = {};

                        var group = ko.mapping.toJS(self.current.group);
                        if (self.mode() === state.create) delete group.id;

                        result.group = group;
                        if (self.current.groupPlan()){
                            result.studyPlanId = self.current.groupPlan().id();
                        }

                        return JSON.stringify(result);
                    }
                },
                fill: function(data){
                    self.current.group().id(data.id()).isFulltime(data.isFulltime())
                        .name(data.name()).prefix(data.prefix())
                        .number(data.number()).course(data.course());
                },
                empty: function(){
                    self.current.group().id('').isFulltime(true)
                        .name('').prefix('')
                        .number('').course('');
                }
            };
            self.actions = {
                start: {
                    create: function(){
                        self.mode() === state.create
                            ? self.mode(state.none)
                            : self.mode(state.create);
                        self.alter.empty();
                        self.current.groupPlan(null);
                        commonHelper.buildValidationList(self.validation);
                    },
                    update: function(data){
                        self.alter.fill(data);
                        self.get.plan();
                        self.mode(state.update);
                        commonHelper.buildValidationList(self.validation);
                    },
                    remove: function(data){
                        self.alter.fill(data);
                        self.mode(state.remove);
                        commonHelper.modal.open('#remove-group-modal');
                    }
                },
                end: {
                    update: function(){
                        if(!self.current.group.isValid()){
                            self.validation[$('[accept-validation]').attr('id')].open();
                            return;
                        }
                        if (!self.current.groupPlan.isValid()){
                            self.validation[$('[special]').attr('id')].open();
                            return;
                        }

                        self.post.group();
                    },
                    remove: function(){
                        self.post.removal();
                        commonHelper.modal.close('#remove-group-modal');
                    }
                },
                cancel: function(){
                    self.alter.empty();
                    self.current.groupPlan(null);
                    self.mode(state.none);
                    self.current.isGenerated(false);
                },
                generate: function(){
                    var g = self.current.group();
                    var generated = g.prefix() + '-' + g.course() + g.number();
                    generated += g.isFulltime() ? 'о' : 'з';
                    g.name(generated);
                    self.current.isGenerated(true);
                },
                moveTo: {
                    students: function(data){
                        commonHelper.cookies.create({
                            groupName : data.name()
                        });
                        window.location.href = '/admin/students/' + data.id();
                    }
                },
                switchForm: {
                    day: function(){
                        self.current.group().isFulltime(true);
                    },
                    night: function(){
                        self.current.group().isFulltime(false);
                    }
                },
                selectPlan: {
                    start: function(){
                        self.validation[$('[special]').attr('id')].close();
                        commonHelper.modal.open('#select-plan-modal');
                    },
                    cancel: function(){
                        self.current.institute(null);
                        commonHelper.modal.close('#select-plan-modal');
                    },
                    end: function(){
                        self.current.groupPlan.copy(self.current.plan);
                        self.actions.selectPlan.cancel();
                    }
                }
            };
            self.get = {
                groups: function(){
                    var profileId = self.initial.profileId() ? 'profileId=' + self.initial.profileId() : '';
                    var name =  self.filter.name() ? '&name=' + self.filter.name() : '';

                    var url = '/api/groups/show' +
                        '?page=' + self.pagination.currentPage() +
                        '&pageSize=' + self.pagination.pageSize() +
                        name +  profileId;

                    var requestOptions = {
                        url: url,
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.groups(data.data());
                            self.pagination.itemsCount(data.count());
                        }
                    };

                    $ajaxget(requestOptions);
                },
                institutes: function(){
                    var requestOptions = {
                        url: '/api/institutes',
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.institutes(data());
                        }
                    };
                    $ajaxget(requestOptions);
                },
                profiles: function(){
                    var requestOptions = {
                        url: '/api/institute/' + self.current.institute().id() + '/profiles',
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.profiles(data());
                        }
                    };
                    $ajaxget(requestOptions);
                },
                plans: function(){
                    var requestOptions = {
                        url: '/api/profile/' + self.current.profile().id() + '/plans',
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.plans(data());
                        }
                    };
                    $ajaxget(requestOptions);
                },
                plan: function(){
                    var requestOptions = {
                        url: '/api/groups/' + self.current.group().id() + '/studyplan',
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.groupPlan(data);
                        }
                    };
                    $ajaxget(requestOptions);
                }
            };
            self.post = {
                group: function(){
                    var url = self.mode() === state.create
                        ? '/api/groups/create'
                        : '/api/groups/update';
                    var json = self.alter.stringify.group();
                    var requestOptions = {
                        url: url,
                        errors: self.errors,
                        data: json,
                        successCallback: function(){
                            self.get.groups();
                            self.actions.cancel();
                        }
                    };
                    $ajaxpost(requestOptions);
                },
                removal: function(){
                    var requestOptions = {
                        url: '/api/groups/delete/' + self.current.group().id(),
                        errors: self.errors,
                        data: null,
                        successCallback: function(){
                            self.actions.cancel();
                            self.get.groups();
                        }
                    };
                    $ajaxpost(requestOptions);
                }
            };

            self.current.institute.subscribe(function(value){
                if (value){
                    self.get.profiles();
                    return;
                }
                self.current.profiles([]);
                self.current.plans([]);
                self.current.profile(null);
                self.current.plan(null);
            });
            self.current.profile.subscribe(function(value){
                if (value){
                    self.get.plans();
                    return;
                }
                self.current.plans([]);
                self.current.plan(null);
            });
            self.pagination.itemsCount.subscribe(function(value){
                if (value){
                    self.pagination.totalPages(Math.ceil(
                        value/self.pagination.pageSize()
                    ));
                }
            });
            self.pagination.currentPage.subscribe(function(){
                self.get.groups();
            });
            self.filter.name.subscribe(function(){
                self.actions.cancel();
                self.pagination.currentPage(1);
                self.get.groups();
            });

            self.initial.unUrlProfileId();
            self.get.institutes();

            return {
                page: self.page,
                current: self.current,
                actions: self.actions,
                filter: self.filter,
                errors: self.errors,
                events: self.events,
                pagination: self.pagination,
                mode: self.mode
            };
        };
    };

    ko.applyBindings(groupsViewModel());
});