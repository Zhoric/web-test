/**
 * Created by nyanjii on 26.10.16.
 */
$(document).ready(function(){
    ko.validation.init({
        messagesOnModified: true,
        insertMessages: false
    });
    var testsViewModel = function(){
        return new function(){
            var self = this;
            self.page = ko.observable(menu.admin.tests);
            self.errors = errors();
            self.validation = {};
            self.events = new validationEvents(self.validation);
            self.current = {
                test: ko.validatedObservable({
                    id: ko.observable(0),
                    subject: ko.observable().extend({ required: true }),
                    attempts: ko.observable(3).extend({required: true, number: true, min: 1, max: 100 }),
                    timeTotal: ko.observable(0),
                    minutes: ko.observable().extend({ required: true, number: true, min: 1, max: 60}),
                    seconds: ko.observable().extend({ required: true, number: true, min: 0, max: 59}),
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
                    self.filter.name('');
                    self.filter.discipline(null);
                }
            };
            self.alter = {
                fill: {
                    test: function(data){
                        var minutes = Math.floor(data.timeTotal()/60);
                        var seconds = data.timeTotal()%60;
                        var type = data.type() === 1;

                        data.minutes = ko.observable(minutes ? minutes : '00');
                        data.seconds = ko.observable(seconds);
                        data.themes = ko.observableArray([]);
                        data.type(type);

                        self.current.test.copy(data);
                        self.get.testThemes(data.id());
                    }
                },
                empty: {
                    test: function(){
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
                    }
                },
                stringify: {
                    test: function(){
                        var t = self.current.test();
                        var totalTime = +t.minutes() * 60 + +t.seconds();
                        var themes = [];

                        var test = {
                            subject: t.subject(),
                            attempts: t.attempts(),
                            timeTotal: totalTime,
                            type: t.type() ? 1 : 2,
                            isActive: t.isActive(),
                            isRandom: t.isRandom()
                        };
                        if (self.mode() === 'edit'){
                            test.id = t.id();
                        }
                        self.multiselect.tags().find(function(item){
                            themes.push(item.id());
                        });

                        return JSON.stringify({
                            test: test,
                            themeIds: themes,
                            disciplineId: self.filter.discipline().id()
                        });
                    }
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
            self.pagination = pagination();
            self.multiselect = new multiselect({
                    dataTextField: 'name',
                    dataValueField: 'id',
                    valuePrimitive: false
            });
            self.mode = ko.observable('none');
            self.csed = {
                test: {
                    show: function(data){
                        if (self.mode() === 'info'){
                            self.mode('none');
                        }
                        else{
                            self.mode('info');
                            self.alter.fill.test(data);
                        }
                    },
                    toggleAdd: function(){
                        self.alter.empty.test();
                        self.mode() === 'add' ? self.mode('none') : self.mode('add');
                        commonHelper.buildValidationList(self.validation);
                    },
                    startRemove: function(data){
                        self.alter.fill.test(data);
                        self.mode('delete');
                        commonHelper.modal.open('#delete-modal');
                    },
                    remove: function(){
                        self.post.removedTest();
                    },
                    startEdit: function(){
                        self.mode('edit');
                        commonHelper.buildValidationList(self.validation);
                    },
                    update: function(){
                        if (!self.current.test.isValid()){
                            self.validation[$('[accept-validation]').attr('id')].open();
                            return;
                        }
                        if (!self.multiselect.tags().length){
                            self.validation[$('[special]').attr('id')].open();
                            return;
                        }
                        self.mode() === 'add' ? self.post.test('create') : self.post.test('update');
                    },
                    cancel: function(){
                        if (self.mode() === 'add' || self.mode() === 'edit'){
                            self.mode('none');
                            self.alter.empty.test();
                            return;
                        }
                        self.mode('info');
                    }
                }
            };

            self.get = {
                disciplines: function(){
                    $.get('/api/disciplines/', function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()) {
                            self.current.disciplines(result.Data());
                            self.alter.set.filter();
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                tests: function(){
                    var filterDiscipline = 'discipline=' + self.filter.discipline().id();
                    var page = '&page=' + self.pagination.currentPage();
                    var pageSize = '&pageSize=' + self.pagination.pageSize();
                    var name = '&name=' + self.filter.name();

                    var url = '/api/tests/show?' + filterDiscipline +
                        page + pageSize + name;

                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()) {
                            self.current.tests(result.Data.data());
                            self.pagination.itemsCount(result.Data.count());
                            self.get.themes();
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                themes: function(){
                    var url = '/api/disciplines/' + self.filter.discipline().id() + '/themes';
                    $ajaxget({
                        url: url,
                        errors: self.errors,
                        successCallback: function(data){
                            self.multiselect.setDataSource(data());
                        }
                    });
                },
                testThemes: function(id){
                    $ajaxget({
                        url: '/api/tests/' + id + '/themes',
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.test().themes(data());
                            self.multiselect.multipleSelect()(self.current.test().themes());
                        }
                    });
                }
            };
            self.post = {
                test: function(action){
                    var url = '/api/tests/' + action;
                    var json = self.alter.stringify.test();

                    $.post(url, json, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()) {
                            self.mode('none');
                            self.alter.empty.test();
                            self.get.tests();
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                removedTest: function(){
                    $.post('/api/tests/delete/' + self.current.test().id(), function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()) {
                            commonHelper.modal.close('#delete-modal');
                            self.get.tests();
                            self.mode('none');
                            return;
                        }
                        self.errors.show(result.Message());
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
            self.pagination.currentPage.subscribe(function(value){
                self.get.tests();
            });
            self.filter.discipline.subscribe(function(value){
                self.mode('none');
                if (value){
                    self.get.tests();
                    return;
                }
                self.current.tests([]);
            });
            self.filter.name.subscribe(function(){
                self.get.tests();
            });

            return {
                page: self.page,
                current: self.current,
                pagination: self.pagination,
                multiselect: self.multiselect,
                alter: self.alter,
                mode: self.mode,
                csed: self.csed,
                filter: self.filter,
                events: self.events,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(testsViewModel());
});