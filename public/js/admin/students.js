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
                    firstName: ko.observable(''),
                    lastName: ko.observable(''),
                    middleName: ko.observable(''),
                    group: {},
                    email: ko.observable(''),
                    active: ko.observable(false)
                },
                groups: ko.observableArray([])
            };
            self.current = {
                students: ko.observableArray([]),
                student: ko.observable(self.initial.student)
            };

            self.filter = {
                name: ko.observable(''),
                group: ko.observable(),
                isActive: ko.observable(false)
            };
            self.actions = {
                show: function(data){
                    if (self.mode() === state.none){
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
                    remove: function(data){
                        self.mode(state.remove);
                        self.student(data.copy());
                    }
                },
                cancel: function(){
                    self.mode(state.none);
                    self.current.student(self.initial.student);
                },
                end: {
                    create: function(){
                        self.post.student();
                        self.actions.cancel();
                    },
                    update: function(){
                        self.post.student();
                        self.actions.cancel();
                    },
                    remove: function(){
                        self.actions.cancel();
                    }
                }
            };

            self.alter = {
                stringify: {
                    student: function(){},
                    password: function(){},
                    group: function(){}
                },
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
                group: function(){
                    var json = '';
                    $post('/api/student/setGroup', json, self.errors, function(data){

                    })();
                },
                student: function(){
                    var json = '';
                    $post('/api/student/updateStudent', json, self.errors, function(data){

                    })();
                },
                password: function(){
                    var json = '';
                    $post('/api/user/setPassword', json, self.errors, function(data){

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
            self.pagination.currentPage.subscribe(function(value){
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