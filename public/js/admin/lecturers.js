$(document).ready(function(){
    var studentsViewModel = function(){
        return new function(){
            var self = this;

            self.errors = errors();
            self.pagination = pagination();
            self.pagination.pageSize(15);
            self.mode = ko.observable(state.none);
            self.multiselect = new multiselect({
                dataTextField: 'name',
                dataValueField: 'id',
                valuePrimitive: false
            });

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
                disciplines: ko.observableArray([]),
                password: ko.observable(null)
            };

            self.filter = {
                name: ko.observable(''),
                clear: function(){
                    self.filter.name('');
                }
            };
            self.actions = {
                show: function(data){
                    if (self.mode() === state.none || self.current.lecturer().id() !== data.lecturer.id()){
                        self.current.lecturer.copy(data.lecturer);
                        self.current.disciplines(data.disciplines());
                        self.multiselect.multipleSelect()(self.current.disciplines());
                        self.mode(state.info);
                        return;
                    }
                    self.actions.cancel();
                },
                start: {
                    create: function(){
                        self.mode() === state.create
                            ? self.mode(state.none)
                            : self.mode(state.create);
                        self.current.lecturer(self.initial.lecturer);
                        self.current.disciplines([]);
                    },
                    update: function(){
                        self.mode(state.update);
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
                    self.current.lecturer(self.initial.lecturer);
                    self.current.disciplines([]);
                    self.multiselect.empty();
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
                stringify: {
                    lecturer: function(){
                        var lecturer = ko.mapping.toJS(self.current.lecturer);
                        var disciplines = [];
                        if (self.mode() === state.create) delete lecturer.id;

                        self.multiselect.tags().find(function(item){
                            disciplines.push(item.id());
                        });

                        return JSON.stringify({
                            lecturer: lecturer,
                            disciplineIds: disciplines
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
                lecturers: function(){
                    var name = self.filter.name() ? '&name=' + self.filter.name() : '';
                    var url = '/api/lecturers/show' +
                            '?page=' + self.pagination.currentPage() +
                            '&pageSize=' + self.pagination.pageSize() + name;

                    var requestOptions = {
                        url: url,
                        errors: self.errors,
                        successCallback: function (data) {
                            self.current.lecturers(data.data());
                            self.pagination.itemsCount(data.count());
                        }
                    };
                    $ajaxget(requestOptions);
                },
                disciplines: function(){
                    var requestOptions = {
                        url: '/api/disciplines/',
                        errors: self.errors,
                        successCallback: function(data){
                            self.initial.disciplines(data());
                            self.multiselect.setDataSource(self.initial.disciplines());
                        }
                    };
                    $ajaxget(requestOptions);
                }
            };
            self.post = {
                password: function(){
                    var json = self.alter.stringify.password();
                    $post('/api/user/setPassword', json, self.errors, function(){
                        self.actions.password.cancel();
                        commonHelper.modal.open('#change-success-modal');
                    })();
                },
                removal: function(){
                    var requestOptions = {
                        url: '/api/lecturers/delete/' + self.current.lecturer().id(),
                        data: null,
                        errors: self.errors,
                        successCallback: function(){
                            self.get.lecturers();
                        }
                    };
                    $ajaxpost(requestOptions);
                },
                lecturer: function(){
                    var requestOptions = {
                        url: self.mode() === state.create ? '/api/lecturers/create' : '/api/lecturers/update',
                        errors: self.errors,
                        data: self.alter.stringify.lecturer(),
                        successCallback: function(){
                            self.actions.cancel();
                            self.get.lecturers();
                        }
                    };
                    $ajaxpost(requestOptions);
                }
            };

            self.filter.name.subscribe(function(value){
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

            self.get.disciplines();
            self.get.lecturers();



            return {
                initial: self.initial,
                filter: self.filter,
                current: self.current,
                actions: self.actions,
                mode: self.mode,
                pagination: self.pagination,
                errors: self.errors,
                multiselect: self.multiselect
            };
        };
    };

    ko.applyBindings(studentsViewModel());
});