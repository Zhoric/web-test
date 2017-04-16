$(document).ready(function(){
    var studentsViewModel = function(){
        return new function(){
            var self = this;

            initializeViewModel.call(self, {
                page: menu.admin.students,
                pagination: 20,
                mode: true
            });

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
                group: ko.observable(),
                request: ko.observable(filters.active.all),
                clear: function(){
                    self.filter
                        .name('')
                        .group(null)
                        .request(filters.active.all);
                },
                set: {
                    group: function(id){
                        $.each(self.initial.groups(), function(i, item){
                            if (item.id() == id) self.filter.group(item);
                        });
                    }
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
                        self.alter.empty();
                        self.mode() === state.create
                            ? self.mode(state.none)
                            : self.mode(state.create);
                        commonHelper.buildValidationList(self.validation);
                    },
                    update: function(data){
                        self.mode(state.update);
                        self.alter.fill(data);
                        commonHelper.buildValidationList(self.validation);
                    },
                    remove: function(){
                        commonHelper.modal.open('#remove-request-modal');
                    }
                },
                end: {
                    update: function(){
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
                    on: function(data, e){
                        self.confirm.show({
                            message: 'Вы действительно хотите подтвердить заявку?',
                            approve: function(){
                                self.post.approval(data.id());
                            }
                        });
                        e.stopPropagation();
                    },
                    off: function(data, e){
                        self.confirm.show({
                            message: 'Заявка будет удалена. Вы действительно хотите отклонить выбранную заявку?',
                            approve: function(){
                                self.post.request(data.id());
                            }
                        });
                        e.stopPropagation();
                    }
                }
            };

            self.alter = {
                set: {
                    group: function(id){
                        $.each(self.initial.groups(), function(i, item){
                            if (item.id() === id)
                                self.current.student().group(item);
                        });
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
                    self.current.student().id('').group(null)
                        .firstname('').lastname('').patronymic('')
                        .email('').active(true).password('');
                }
            };
            self.get = {
                students: function(){
                    var name = self.filter.name() ? '&name=' + self.filter.name() : '';
                    var group = self.filter.group() ? '&groupId=' + self.filter.group().id() : '';

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
                            commonHelper.tooltip({selector: '.item > .fa', side: 'top'});
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
                            if (!$.isEmptyObject(cookie)){
                                self.filter.set.group(cookie.groupId);
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
                request: function(studentId){
                    var id = studentId ? studentId : self.current.student().id();
                    $ajaxpost({
                        url: '/api/user/delete/' + id,
                        data: null,
                        errors: self.errors,
                        successCallback: function(){
                            self.actions.cancel();
                            self.get.students();
                        }
                    });
                },
                approval: function(id){
                    $ajaxpost({
                        url: '/api/user/activate/' + id,
                        errors: self.errors,
                        successCallback: function(){
                            self.get.students();
                        }
                    })
                },
                student: function(){
                    $ajaxpost({
                        url: '/api/groups/student/' + self.mode(),
                        errors: self.errors,
                        data: self.alter.stringify.student(),
                        successCallback: function(){
                            self.actions.cancel();
                            self.get.students();
                        }
                    });
                },
                password: function(){
                    $ajaxpost({
                        url: '/api/user/setPassword',
                        errors: self.errors,
                        data: self.alter.stringify.password(),
                        successCallback: function(){
                            self.actions.password.cancel();
                            self.inform.show({
                                message: 'Пароль успешно изменен'
                            });
                        }
                    });
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

            return returnStandart.call(self);
        };
    };

    ko.applyBindings(studentsViewModel());
});