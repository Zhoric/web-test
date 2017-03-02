$(document).ready(function(){
    var testsViewModel = function(){
        return new function(){
            var self = this;
            initializeViewModel.call(self, {
                page: menu.admin.tests,
                mode: true,
                pagination: 10,
                multiselect: {
                    dataTextField: 'name',
                    dataValueField: 'id',
                    valuePrimitive: false
                }
            });
            self.modals = {
                removeTest: '#remove-test-modal'
            };

            self.current = {
                test: ko.validatedObservable({
                    id: ko.observable(0),
                    subject: ko.observable().extend({ required: true }),
                    attempts: ko.observable(3).extend({required: true, digit: true, min: 1, max: 100 }),
                    timeTotal: ko.observable(0),
                    minutes: ko.observable().extend({ required: true, digit: true, min: 1, max: 60}),
                    seconds: ko.observable().extend({ required: true, digit: true, min: 0, max: 59}),
                    type: ko.observable(true),
                    isActive: ko.observable(true),
                    isRandom: ko.observable(true),
                    themes: ko.observableArray([])
                }),
                tests: ko.observableArray([]),
                disciplines: ko.observableArray([])
            };
            self.filter = {
                name: ko.observable(''),
                discipline: ko.observable(),
                clear: function(){
                    self.filter
                        .discipline(null).name('');
                }
            };
            self.alter = {
                fill: function(data){
                    var minutes = Math.floor(data.timeTotal()/60);
                    var seconds = data.timeTotal()%60;
                    var type = data.type() === 1;

                    minutes = minutes < 10 ? '0' + minutes : minutes;
                    seconds = seconds < 10 ? '0' + seconds : seconds;

                    self.current.test()
                        .id(data.id()).subject(data.subject())
                        .attempts(data.attempts()).timeTotal(data.timeTotal())
                        .minutes(minutes).seconds(seconds).type(type)
                        .isActive(data.isActive()).isRandom(data.isRandom());

                    self.get.testThemes();
                },
                empty: function(){
                    self.current.test()
                        .id(0)
                        .subject('')
                        .attempts(3)
                        .timeTotal(0)
                        .minutes('')
                        .seconds('')
                        .type(true)
                        .isActive(true)
                        .isRandom(true)
                        .themes([]);
                    self.multiselect.empty();
                },
                stringify: function(){
                    var t = self.current.test();
                    t.timeTotal(+t.minutes() * 60 + +t.seconds());

                    var test = ko.mapping.toJS(self.current.test);

                    delete test.minutes;
                    delete test.seconds;
                    delete test.themes;

                    test.type = test.type ? 1 : 2;
                    self.mode() === state.create ? delete test.id : null;

                    return JSON.stringify({
                        test: test,
                        themeIds: self.multiselect.getTagsArray(),
                        disciplineId: self.filter.discipline().id()
                    });
                },
                set: {
                    filter: function(){
                        var discipline = self.filter.discipline;
                        var url = window.location.href;

                        if (!discipline()){
                            var disciplineId = +url.substr(url.lastIndexOf('/')+1);

                            if (!$.isNumeric(disciplineId)) return;

                            var disciplineReceived = self.current.disciplines().find(function(item){
                                return item.id() === disciplineId;
                            });
                            discipline(disciplineReceived);
                        }
                    },
                    random: {
                        asTrue: function(){
                            self.current.test().isRandom(true);
                        },
                        asFalse: function(){
                            self.current.test().isRandom(false);
                        }
                    },
                    type: {
                        asTrue: function(){
                            self.current.test().type(true);
                        },
                        asFalse: function(){
                            self.current.test().type(false);
                        }
                    }
                }
            };

            self.actions = {
                show: function(data){
                    self.current.test().id() === data.id()
                        ? self.actions.cancel()
                        : self.mode(state.info) && self.alter.fill(data);
                },
                start: {
                    add: function(){
                        self.mode() === state.create
                            ? self.mode(state.none)
                            : self.mode(state.create);
                        self.alter.empty();
                        commonHelper.buildValidationList(self.validation);
                    },
                    update: function(){
                        self.mode(state.update);
                        commonHelper.buildValidationList(self.validation);
                    },
                    remove: function(){
                        self.mode(state.remove);
                        commonHelper.modal.open(self.modals.removeTest);
                    }
                },
                end: {
                    update: function(){
                        if (!self.current.test.isValid()){
                            self.validation[$('[accept-validation]').attr('id')].open();
                            return;
                        }
                        if (!self.multiselect.tags().length){
                            self.validation[$('[special]').attr('id')].open();
                            return;
                        }
                        self.post.test();
                    },
                    remove: function(){
                        self.post.removedTest();
                    }
                },
                cancel: function(){
                    self.alter.empty();
                    self.mode(state.none);
                }
            };

            self.get = {
                disciplines: function(){
                    $ajaxget({
                        url: '/api/disciplines/',
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.disciplines(data());
                        }
                    });
                },
                tests: function(){
                    if (!self.filter.discipline()){
                        self.current.tests([]);
                        return;
                    }
                    var page = '?page=' + self.pagination.currentPage();
                    var pageSize = '&pageSize=' + self.pagination.pageSize();
                    var name = self.filter.name() ?'&name=' + self.filter.name() : '';
                    var filterDiscipline = '&discipline=' + self.filter.discipline().id();

                    var url = '/api/tests/show' + page + pageSize + name + filterDiscipline;
                    $ajaxget({
                        url: url,
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.tests(data.data());
                            self.pagination.itemsCount(data.count());
                            self.get.themes();
                        }
                    });
                },
                themes: function(){
                    $ajaxget({
                        url: '/api/disciplines/' + self.filter.discipline().id() + '/themes',
                        errors: self.errors,
                        successCallback: function(data){
                            self.multiselect.setDataSource(data());
                        }
                    });
                },
                testThemes: function(){
                    $ajaxget({
                        url: '/api/tests/' + self.current.test().id() + '/themes',
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.test().themes(data());
                            self.multiselect.multipleSelect()(self.current.test().themes());
                        }
                    });
                }
            };
            self.post = {
                test: function(){
                    $ajaxpost({
                        url: '/api/tests/' + self.mode(),
                        data: self.alter.stringify(),
                        errors: self.errors,
                        successCallback: function(){
                            self.actions.cancel();
                            self.get.tests();
                        }
                    });
                },
                removedTest: function(){
                    $ajaxpost({
                        url: '/api/tests/delete/' + self.current.test().id(),
                        data: null,
                        errors: self.errors,
                        successCallback: function(){
                            self.actions.cancel();
                            self.get.tests();
                        }
                    });
                }
            };

            self.get.disciplines();

            //SUBSCRIPTIONS
            self.pagination.itemsCount.subscribe(function(value){
                if (value){
                    self.pagination.totalPages(Math.ceil(
                        value/self.pagination.pageSize()
                    ));
                }
            });
            self.pagination.currentPage.subscribe(function(){
                self.get.tests();
            });
            self.filter.discipline.subscribe(function(){
                self.mode(state.none);
                self.pagination.currentPage(1);
                self.get.tests();
            });
            self.filter.name.subscribe(function(){
                self.mode(state.none);
                self.pagination.currentPage(1);
                self.get.tests();
            });

            return returnStandart.call(self);
        };
    };

    ko.applyBindings(testsViewModel());
});