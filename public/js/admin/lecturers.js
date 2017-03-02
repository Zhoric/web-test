$(document).ready(function(){
    var studentsViewModel = function(){
        return new function(){
            var self = this;

            initializeViewModel.call(self,{
                page: menu.admin.lecturers,
                pagination: 15,
                mode: true,
                multiselect: {
                    dataTextField: 'name',
                    dataValueField: 'id',
                    valuePrimitive: false
                }
            });

            self.initial = {
                disciplines: ko.observableArray([])
            };
            self.current = {
                lecturers: ko.observableArray([]),
                lecturer: ko.validatedObservable({
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
                    email: ko.observable('').extend({required: true, email: true}),
                    password: ko.observable().extend({
                        required: true,
                        minLength: 6,
                        maxLength: 16
                    })
                }),
                disciplines: ko.observableArray([]),
                password: ko.validatedObservable(null).extend({
                    required: {
                        params: true,
                        message: 'Вы не можете оставить это поле путым'
                    },
                    minLength: 6,
                    maxLength: 16
                })
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
                        self.alter.fill(data.lecturer);
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
                        self.alter.empty();
                        self.current.disciplines([]);
                        commonHelper.buildValidationList(self.validation);
                    },
                    update: function(){
                        self.mode(state.update);
                        commonHelper.buildValidationList(self.validation);
                    },
                    remove: function(){
                        commonHelper.modal.open('#remove-request-modal');
                    }
                },
                end: {
                    update: function(){
                        self.current.lecturer.isValid()
                            ? self.post.lecturer()
                            : self.validation[$('[accept-validation]').attr('id')].open();
                    },
                    remove: function(){
                        self.post.removal();
                    }
                },
                cancel: function(){
                    self.mode(state.none);
                    self.alter.empty();
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
                        self.validation[$('.box-modal [validate]').attr('id')].close();
                        commonHelper.modal.close('#change-password-modal');
                    },
                    approve: function(){
                        self.current.password.isValid()
                            ? self.post.password()
                            : self.validation[$('.box-modal [validate]').attr('id')].open();
                    }
                }
            };

            self.alter = {
                stringify: {
                    lecturer: function(){
                        var lecturer = ko.mapping.toJS(self.current.lecturer);

                        self.mode() === state.create
                            ? delete lecturer.id
                            : delete lecturer.password;

                        return JSON.stringify({
                            lecturer: lecturer,
                            disciplineIds: self.multiselect.getTagsArray()
                        });
                    },
                    password: function(){
                        return JSON.stringify({
                            userId: self.current.lecturer().id(),
                            password: self.current.password()
                        });
                    }
                },
                fill: function(data){
                    self.current.lecturer()
                        .id(data.id()).email(data.email())
                        .firstname(data.firstname())
                        .lastname(data.lastname())
                        .patronymic(data.patronymic())
                        .password('password');
                },
                empty: function(){
                    self.current.lecturer()
                        .id('').email('').password('')
                        .firstname('').lastname('').patronymic('');
                }
            };
            self.get = {
                lecturers: function(){
                    var name = self.filter.name() ? '&name=' + self.filter.name() : '';
                    var url = '/api/lecturers/show' +
                            '?page=' + self.pagination.currentPage() +
                            '&pageSize=' + self.pagination.pageSize() + name;

                    $ajaxget({
                        url: url,
                        errors: self.errors,
                        successCallback: function (data) {
                            self.current.lecturers(data.data());
                            self.pagination.itemsCount(data.count());
                        }
                    });
                },
                disciplines: function(){
                    $ajaxget({
                        url: '/api/disciplines/',
                        errors: self.errors,
                        successCallback: function(data){
                            self.initial.disciplines(data());
                            self.multiselect.setDataSource(self.initial.disciplines());
                        }
                    });
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

            self.filter.name.subscribe(function(){
                self.mode(state.none);
                self.pagination.currentPage(1);
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

            return returnStandart.call(self);
        };
    };

    ko.applyBindings(studentsViewModel());
});