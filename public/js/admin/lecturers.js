/**
 * Created by nyanjii on 02.10.16.
 */

$(document).ready(function(){
    var studentsViewModel = function(){
        return new function(){
            var self = this;

            self.errors = errors();
            self.pagination = pagination();
            self.pagination.pageSize(15);
            self.mode = ko.observable(state.none);

            self.initial = {
                lecturer: {
                    id: ko.observable(''),
                    firstname: ko.observable(''),
                    lastname: ko.observable(''),
                    patronymic: ko.observable(''),
                    email: ko.observable('')
                },
                disciplines: ko.observableArray([])
            };
            self.current = {
                lecturers: ko.observableArray([]),
                lecturer: ko.observable(self.initial.lecturer),
                password: ko.observable(null)
            };

            self.filter = {
                name: ko.observable(''),
                discipline: ko.observable()
            };
            self.actions = {
                show: function(data){
                    if (self.mode() === state.none || self.current.lecturer().id() !== data.id()){
                        self.mode(state.info);
                        return;
                    }
                    self.mode(state.none);
                    self.current.lecturer(self.initial.lecturer);
                },
                start: {
                    create: function(){
                        self.mode() === state.create
                            ? self.mode(state.none)
                            : self.mode(state.create);
                        self.current.lecturer(self.initial.lecturer);
                    },
                    update: function(data){
                        self.mode(state.update);
                        self.current.lecturer.copy(data);
                    },
                    remove: function(){
                        commonHelper.modal.open('#remove-request-modal');
                    }
                },
                end: {
                    update: function(){
                        self.post.lecturer();
                    },
                    remove: function(){
                        self.post.removal();
                    }
                },
                cancel: function(){
                    self.mode(state.none);
                    self.current.lecturer(self.initial.student);
                    self.current.password(null);
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
                },
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
                    lecturer: function(){

                        return JSON.stringify({

                        });
                    },
                    password: function(){
                        return JSON.stringify({
                            userId: self.current.lecturer().id(),
                            password: self.current.password()
                        });
                    }
                }
            };
            self.get = {
                lecturers: function(){},
                disciplines: function(){},
            };
            self.post = {
                password: function(){
                    var json = self.alter.stringify.password();
                    $post('/api/user/setPassword', json, self.errors, function(){
                        self.actions.password.cancel();
                        commonHelper.modal.open('#change-success-modal');
                    })();
                },
                removal: function(){},
                lecturer: function(){}
            };

            self.filter.discipline.subscribe(function(){
                self.get.lecturers();
            });
            self.filter.name.subscribe(function(){
                self.get.lecturers();
            });
            self.pagination.itemsCount.subscribe(function(value){
                if (value){
                    self.pagination.totalPages(Math.ceil(
                        value/self.pagination.pageSize()
                    ));
                }
            });
            self.pagination.currentPage.subscribe(function(){
                self.get.lecturers();
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