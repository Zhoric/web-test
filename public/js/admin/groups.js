$(document).ready(function(){
    var groupsViewModel = function(){
        return new function(){
            var self = this;

            initializeViewModel.call(self, {
                page: menu.admin.groups,
                pagination: 10,
                mode: true
            });

            self.initial = {
                profileId: ko.observable(null),
                unUrlProfileId: function(){
                    if (self.initial.profileId()) return '&profileId=' + self.initial.profileId();

                    var url = window.location.href;
                    var id = +url.substring(url.lastIndexOf('/')+1);
                    if ($.isNumeric(id)){
                        self.initial.profileId(id);
                        return '&profileId=' + id;
                    }

                    return '';
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
                isGenerated: ko.observable(false),
                hasInactive: ko.observable(false)
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
                    self.get.inactive();
                },
                empty: function(){
                    self.current.group().id('').isFulltime(true)
                        .name('').prefix('')
                        .number('').course('');
                    self.current.hasInactive(false);
                }
            };
            self.actions = {
                show: function(data){
                    if (self.mode() === state.update) {
                        self.mode(state.info);
                        return;
                    }
                    if (self.mode() === state.info && self.current.group().id() === data.id()){
                        self.actions.cancel();
                        return;
                    }
                    self.alter.fill(data);
                    self.get.plan();
                    self.mode(state.info);
                },
                start: {
                    create: function(){
                        self.mode() === state.create
                            ? self.mode(state.none)
                            : self.mode(state.create);
                        self.alter.empty();
                        self.current.groupPlan(null);
                        commonHelper.buildValidationList(self.validation);
                    },
                    update: function(){
                        self.mode(state.update);
                        commonHelper.buildValidationList(self.validation);
                    },
                    remove: function(){
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
                    self.mode(state.none);
                    self.alter.empty();
                    self.current.groupPlan(null);
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
                    students: function(data, e){
                        commonHelper.cookies.create({
                            groupId : data.id()
                        });
                        window.location.href = '/admin/students/' + data.id();
                        e.stopPropagation();
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
                },
                approveStudents: function(){
                    self.confirm.show({
                        message: 'Вы действительно хотите принять все заявки студентов группы ' +
                        self.current.group().name(),
                        approve: function(){self.post.students();}
                    });
                }
            };
            self.get = {
                groups: function(){
                    var page = '?page=' + self.pagination.currentPage();
                    var pageSize = '&pageSize=' + self.pagination.pageSize();
                    var name =  self.filter.name() ? '&name=' + self.filter.name() : '';
                    var profile = self.initial.unUrlProfileId();

                    var requestOptions = {
                        url: '/api/groups/show' + page + pageSize + name + profile,
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
                },
                inactive: function(){
                    $ajaxget({
                        url: '/api/groups/' + self.current.group().id() +'/hasUnactive',
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.hasInactive(data());
                        }
                    })
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
                students: function(){
                    $ajaxpost({
                        url: '/api/groups/acceptAll/' + self.current.group().id(),
                        errors: self.errors,
                        data: null,
                        successCallback: function(){
                            self.inform.show({message: 'Все заявки приняты'});
                        }
                    });
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

            self.get.institutes();
            self.get.groups();

            return returnStandart.call(self);
        };
    };

    ko.applyBindings(groupsViewModel());
});