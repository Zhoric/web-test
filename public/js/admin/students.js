$(document).ready(function(){
    var studentsViewModel = function(){
        return new function(){
            var self = this;

            self.page = ko.observable(menu.admin.students);
            self.errors = errors();
            self.user = new user();
            self.user.read(self.errors);
            self.validation = {};
            self.events = new validationEvents(self.validation);
            self.pagination = pagination();
            self.pagination.pageSize(20);
            self.mode = ko.observable(state.none);

            self.initial = {
                groups: ko.observableArray([])
            };
            self.current = {
                students: ko.observableArray([]),
                student: ko.validatedObservable({
                    id: ko.observable(''),
                    firstname: ko.observable('').extend({
                        required: true,
                        pattern: '^[А-ЯЁ][а-яё]+(\-{1}(?:[А-ЯЁ]{1}(?:[а-яё]+)))?$',
                        maxLength: 80
                    }),
                    lastname: ko.observable('').extend({
                        required: true,
                        pattern: '^[А-ЯЁ][а-яё]+(\-{1}(?:[А-ЯЁ]{1}(?:[а-яё]+)))?$',
                        maxLength: 80
                    }),
                    patronymic: ko.observable('').extend({
                        pattern: '^[А-ЯЁ][а-яё]+(\-{1}(?:[А-ЯЁ]{1}(?:[а-яё]+)))?$',
                        maxLength: 80
                    }),
                    group: ko.observable(null).extend({required: true}),
                    email: ko.observable('').extend({required: true, email: true}),
                    password: ko.observable('').extend({
                        required: true,
                        minLength: 6,
                        maxLength: 16
                    }),
                    active: ko.observable(true)
                }),
                password: ko.observable(null).extend({
                    required: true,
                    minLength: 6,
                    maxLength: 16
                })
            };

            self.filter = {
                name: ko.observable(''),
                group: ko.observable(''),
                request: ko.observable(filters.active.all),
                clear: function(){
                    self.filter
                        .name('')
                        .group('')
                        .request(filters.active.all);
                }
            };
            self.actions = {
                show: function(data){
                    if (self.mode() === state.none || self.current.student().id() !== data.id()){
                        self.get.student(data.id());
                        return;
                    }
                    self.mode(state.none);
                    self.alter.empty();
                },
                start: {
                    create: function(){
                        self.mode() === state.create
                            ? self.mode(state.none)
                            : self.mode(state.create);
                        self.alter.empty();
                        commonHelper.buildValidationList(self.validation);
                    },
                    update: function(data){
                        self.mode(state.update);
                        self.alter.fill(data);
                        console.log(data);
                        self.alter.set.group(data.group().id());
                        commonHelper.buildValidationList(self.validation);
                    },
                    remove: function(){
                        commonHelper.modal.open('#remove-request-modal');
                    }
                },
                end: {
                    update: function(){
                        if (!self.current.student().active()){
                            commonHelper.modal.open('#cancel-request-modal');
                            return;
                        }
                        self.current.student.isValid()
                            ? self.post.student()
                            : self.validation[$('[accept-validation]').attr('id')].open();
                    },
                    remove: function(){
                        self.post.request();
                    }
                },
                cancel: function(){
                    self.mode(state.none);
                    self.alter.empty();
                    self.current.password(null);
                },

                password: {
                    change: function(){
                        commonHelper.modal.open('#change-password-modal');
                    },
                    cancel: function(){
                        self.current.password(null);
                        self.validation[$('.box-modal [validate]').attr('id')].close();
                        commonHelper.modal.close('#change-password-modal');
                    },
                    approve: function(){
                        self.current.password.isValid()
                            ? self.post.password()
                            : self.validation[$('.box-modal [validate]').attr('id')].open();
                    }
                },
                switch: {
                    on: function(data){
                        data.active(true);
                    },
                    off: function(data){
                        data.active(false);
                    }
                }
            };

            self.alter = {
                set: {
                    group: function(id){
                        var group = self.initial.groups().find(function(item){
                            return item.id() === id;
                        });
                        self.current.student().group(group);
                    }
                },
                stringify: {
                    student: function(){
                        var student = ko.mapping.toJS(self.current.student);
                        delete student.group;

                        self.mode() === state.create
                            ? delete student.id
                            : delete student.password;

                        return JSON.stringify({
                            student: student,
                            groupId: self.current.student().group().id()
                        });
                    },
                    password: function(){
                        return JSON.stringify({
                            userId: self.current.student().id(),
                            password: self.current.password()
                        });
                    }
                },
                fill: function(data){
                    self.current.student().id(data.id())
                        .firstname(data.firstname()).lastname(data.lastname())
                        .patronymic(data.patronymic())
                        .email(data.email()).active(data.active())
                        .password('password');
                },
                empty: function(){
                    self.current.student().id('')
                        .firstname('').lastname('').patronymic('')
                        .group(null).email('').active(true).password('');
                }
            };
            self.get = {
                students: function(){
                    var name = self.filter.name() ? '&name=' + self.filter.name() : '';
                    var group = self.filter.group() ? '&groupName=' + self.filter.group() : '';

                    var active = self.filter.request() === filters.active.all ? '' : '';
                    active = self.filter.request() === filters.active.inactive ? '&isActive=false' : active;
                    active = self.filter.request() === filters.active.active ? '&isActive=true' : active;

                    var url = '/api/user/show' +
                        '?page=' + self.pagination.currentPage() +
                        '&pageSize=' + self.pagination.pageSize()
                        + name + group + active;

                    $ajaxget({
                        url: url,
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.students(data.data());
                            self.pagination.itemsCount(data.count());
                        }
                    });
                },
                student: function(id){
                    $ajaxget({
                        url: '/api/user/getStudent/' + id,
                        errors: self.errors,
                        successCallback: function(data){
                            self.alter.fill(data);
                            self.alter.set.group(data.group.id());
                            self.mode(state.info);
                        }
                    });
                },
                groups: function(){
                    $ajaxget({
                        url: '/api/groups',
                        errors: self.errors,
                        successCallback: function(data){
                            self.initial.groups(data());
                            var cookie = $.cookie();
                            if (cookie){
                                self.filter.group(cookie.groupName);
                                commonHelper.cookies.remove(cookie);
                                return;
                            }
                            self.get.students();
                        }
                    });
                }
            };
            self.get.groups();

            self.post = {
                request: function(){
                    var url = '/api/user/delete/' + self.current.student().id();
                    var json = '';
                    $post(url, json, self.errors, function(){
                        self.actions.cancel();
                        self.get.students();
                    })();
                },
                student: function(){
                    var json = self.alter.stringify.student();
                    var url = '/api/groups/student/' + self.mode();

                    $post(url, json, self.errors, function(){
                        self.actions.cancel();
                        self.get.students();
                    })();
                },
                password: function(){
                    var json = self.alter.stringify.password();
                    $post('/api/user/setPassword', json, self.errors, function(){
                        self.actions.password.cancel();
                        commonHelper.modal.open('#change-success-modal');
                    })();
                }
            };

            self.filter.group.subscribe(function(){
                self.actions.cancel();
                self.pagination.currentPage(1);
                self.get.students();
            });
            self.filter.name.subscribe(function(){
                self.actions.cancel();
                self.pagination.currentPage(1);
                self.get.students();
            });
            self.filter.request.subscribe(function(){
                self.actions.cancel();
                self.pagination.currentPage(1);
                self.get.students();
            });
            self.pagination.itemsCount.subscribe(function(value){
                if (value){
                    self.pagination.totalPages(Math.ceil(
                        value/self.pagination.pageSize()
                    ));
                }
            });
            self.pagination.currentPage.subscribe(function(){
                self.get.students();
            });

            return {
                page: self.page,
                user: self.user,
                initial: self.initial,
                filter: self.filter,
                current: self.current,
                actions: self.actions,
                events: self.events,
                mode: self.mode,
                pagination: self.pagination,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(studentsViewModel());
});