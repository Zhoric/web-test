/**
 * Created by nyanjii on 02.10.16.
 */

$(document).ready(function(){
    var studentsViewModel = function(){
        return new function(){
            var self = this;

            self.errors = errors();
            self.pagination = pagination();
            self.mode = ko.observable(state.none);

            self.initial = {
                student: {
                    id: ko.observable(''),
                    firstname: ko.observable(''),
                    lastname: ko.observable(''),
                    patronymic: ko.observable(''),
                    group: ko.observable(null),
                    email: ko.observable(''),
                    active: ko.observable(false)
                },
                groups: ko.observableArray([])
            };
            self.current = {
                students: ko.observableArray([]),
                student: ko.observable(self.initial.student),
                group: ko.observable(null),
                password: ko.observable(null)
            };

            self.filter = {
                name: ko.observable(''),
                group: ko.observable(),
                isActive: ko.observable(false)
            };
            self.actions = {
                show: function(data){
                    if (self.mode() === state.none || self.current.student().id() !== data.id()){
                        self.get.student(data.id());
                        return;
                    }
                    self.mode(state.none);
                    self.current.student(self.initial.student);
                },
                start: {
                    create: function(){
                        self.mode() === state.create
                            ? self.mode(state.none)
                            : self.mode(state.create);
                        self.current.student(self.initial.student);
                    },
                    update: function(data){
                        if (self.mode() === state.update){
                            self.actions.cancel();
                            return;
                        }
                        self.mode(state.update);
                        self.current.student.copy(data);
                    },
                    remove: function(){
                        commonHelper.modal.open('#remove-request-modal');
                    }
                },
                cancel: function(){
                    self.mode(state.none);
                    self.current.student(self.initial.student);
                    self.current.group(null);
                },
                end: {
                    update: function(){
                        self.post.student();
                        self.actions.cancel();
                    },
                    remove: function(){
                        commonHelper.modal.close('#remove-request-modal');
                        self.post.request();
                    }
                },

                password: {
                    change: function(){
                        commonHelper.modal.open('#change-password-modal');
                    },
                    cancel: function(){
                        self.current.password(null);
                        commonHelper.modal.close('#change-password-modal');
                    },
                    approve: function(){
                        self.post.password();
                    }
                }
            };

            self.alter = {
                set: {
                    group: function(){
                        var id = self.current.student().group.id();
                        var group = self.initial.groups().find(function(item){
                            return item.id() === id;
                        });
                        self.current.group(group);
                    }
                },
                stringify: {
                    student: function(){
                        self.current.student().group = null;
                        var student = ko.mapping.toJS(self.current.student);

                        return JSON.stringify({
                            student: student,
                            groupId: self.current.group().id()
                        });
                    },
                    password: function(){
                        return JSON.stringify({
                            userId: self.current.student().id(),
                            password: self.current.password()
                        });
                    }
                }
            };
            self.get = {
                students: function(){
                    var url = '/api/user/show?' +
                        'name=' + self.filter.name() +
                        '&groupName=' + self.filter.group().name() +
                        '&isActive=' + self.filter.isActive() +
                        '&pageSize=' + self.pagination.pageSize() +
                        '&page=' + self.pagination.currentPage();

                    $get(url, function(data){
                        self.current.students(data.data());
                        self.pagination.itemsCount(data.count());
                        console.log(data);
                    }, self.errors)();
                },
                student: function(id){
                    var url = '/api/user/getStudent/' + id;
                    $get(url, function(data){
                        self.current.student(data);
                        self.alter.set.group();
                        self.mode(state.info);
                        console.log(self.current.student());
                    }, self.errors)();
                },
                groups: function(){
                    $get('/api/groups', function(data){
                        self.initial.groups(data());
                    }, self.errors)();
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

            self.filter.group.subscribe(function(value){
                if (value) self.get.students();
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
                initial: self.initial,
                filter: self.filter,
                current: self.current,
                actions: self.actions,
                mode: self.mode,
                pagination: self.pagination,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(studentsViewModel());
});