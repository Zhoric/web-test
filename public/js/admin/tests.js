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
            self.errors = {
                message: ko.observable(),
                show: function(message){
                    self.errors.message(message);
                    self.toggleModal('#errors-modal', '');
                },
                accept: function(){
                    self.toggleModal('#errors-modal', 'close');
                }
            };
            self.current = {
                test: ko.validatedObservable({
                    id: ko.observable(0),
                    subject: ko.observable().extend({
                        required: {
                            params: true,
                            message: 'Вы не можете оставить это поле пустым'
                        }
                    }),
                    attempts: ko.observable(3).extend({
                        number: {
                            params: true,
                            message: 'Только целое десятичное число'
                        }
                    }),
                    timeTotal: ko.observable(0),
                    minutes: ko.observable().extend({
                        required: {
                            params: true,
                            message: 'Поле не может быть пустым'
                        },
                        number: {
                            params: true,
                            message: 'Только целое десятичное число'
                        }
                    }),
                    seconds: ko.observable().extend({
                        required: {
                            params: true,
                            message: 'Поле не может быть пустым'
                        },
                        number: {
                            params: true,
                            message: 'Только целое десятичное число'
                        }
                    }),
                    type: ko.observable(0),
                    isActive: ko.observable(true),
                    isRandom: ko.observable(true),
                    themes: ko.observableArray([])
                }),
                tests: ko.observableArray([]),

                disciplines: ko.observableArray([]),

                types: ko.observableArray([
                    {
                        id: ko.observable(1),
                        name: ko.observable('Контроль знаний')
                    },
                    {
                        id: ko.observable(2),
                        name: ko.observable('Обучающий')
                    }]),
                type: ko.observable()
            };
            self.filter = {
                name: ko.observable(''),
                discipline: ko.observable(),
            };
            self.validationTooltip = {
                init: function(selector){
                    $(selector).tooltipster({
                        theme: 'tooltipster-light',
                        trigger: 'custom',
                        timer: 3000,
                        position: 'left'
                    });
                },
                open: function(selector, content){
                    $(selector).tooltipster('content', content)
                        .tooltipster('open');
                },
                checkIfExists: function(selector){
                    if ($(selector).hasClass('tooltipstered')) return;
                    self.validationTooltip.init(selector);
                }
            };
            self.toggleCurrent = {
                fill: {
                    test: function(data){
                        var minutes = Math.floor(data.timeTotal()/60);
                        var seconds = data.timeTotal()%60;
                        self.toggleCurrent.fill.type(data.type());

                        self.current.test()
                            .id(data.id())
                            .subject(data.subject())
                            .attempts(data.attempts())
                            .timeTotal(data.timeTotal())
                            .minutes(minutes ? minutes : '00')
                            .seconds(seconds ? seconds : '00')
                            .type(data.type())
                            .isActive(data.isActive())
                            .isRandom(data.isRandom());
                        self.get.testThemes(data.id());
                    },
                    type: function(id){
                        var type = self.current.types().find(function(item){
                            return item.id() === id;
                        });
                        self.current.type(type);
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
                            .type(0)
                            .isActive(true)
                            .isRandom(true)
                            .themes([]);
                        self.current.type('');
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
                            type: t.type(),
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
                    }
                },
                check: {
                    test: function(){
                        var test = self.current.test;
                        var selector = '.approve-btn';

                        if (!test.isValid()){
                            self.validationTooltip.open(selector, 'Поля не заполнены');
                            return false;
                        }

                        if (!self.current.type()){
                            self.validationTooltip.open(selector, 'Тип не выбран');
                            return false;
                        }

                        if (!self.multiselect.tags().length){
                            self.validationTooltip.open(selector, 'Выберите хотя бы одну тему');
                            return false;
                        }

                        return true;
                    }
                }
            };
            self.pagination = {
                currentPage: ko.observable(1),
                pageSize: ko.observable(10),
                itemsCount: ko.observable(1),
                totalPages: ko.observable(1),

                selectPage: function(page){
                    self.pagination.currentPage(page);
                    self.get.tests();
                },
                dotsVisible: function(index){
                    var total = self.pagination.totalPages();
                    var current = self.pagination.currentPage();
                    if (total > 11 && index == total-1 && index > current + 2  ||total > 11 && index == current - 1 && index > 3)  {
                        return true;
                    }
                    return false;
                },
                pageNumberVisible: function(index){
                    var total = self.pagination.totalPages();
                    var current = self.pagination.currentPage();
                    if (total < 12 ||
                        index > (current - 2) && index < (current + 2) ||
                        index > total - 2 ||
                        index < 3) {
                        return true;
                    }
                    return false;
                },
            };
            self.multiselect = {
                data: ko.observableArray([]),
                tags: ko.observableArray([]),
                show: function(data){
                    return data.name();
                },
                select: function(data){
                    var item = self.multiselect.tags().find(function(item){
                        return item.id() === data.id();
                    });
                    if (!item) self.multiselect.tags.push(data);
                    return '';
                },
                remove: function(data){
                    self.multiselect.tags.remove(data);
                },
                empty: function(){
                    console.log('empty');
                    self.multiselect.tags([]);
                },
                fill: function(){
                    var testThemes = self.current.test().themes();
                    console.log('fill');
                    self.multiselect.data().find(function(item){
                        var id = item.id();
                        testThemes.find(function(theme){
                            if (theme.id() === id){
                                //self.multiselect.tags.push(item);
                                self.multiselect.select(item);
                            }
                        });
                    });
                }
            };
            self.mode = ko.observable('none');
            self.csed = {
                test: {
                    show: function(data){
                        if (self.mode() === 'info'){
                            self.mode('none');
                        }
                        else{
                            self.mode('info');
                            self.toggleCurrent.fill.test(data);
                        }
                    },
                    toggleAdd: function(){
                        self.toggleCurrent.empty.test();
                        self.mode() === 'add' ? self.mode('none') : self.mode('add');
                        self.validationTooltip.checkIfExists('.approve-btn');
                    },
                    startRemove: function(data){
                        self.toggleCurrent.fill.test(data);
                        self.mode('delete');
                        self.toggleModal('#delete-modal', '');
                    },
                    remove: function(){
                        self.post.removedTest();
                    },
                    startEdit: function(){
                        self.mode('edit');
                        self.validationTooltip.checkIfExists('.approve-btn');
                    },
                    update: function(){
                        if (!self.toggleCurrent.check.test()) return;
                        self.mode() === 'add' ? self.post.test('create') : self.post.test('update');
                    },
                    cancel: function(){
                        if (self.mode() === 'add' || self.mode() === 'edit'){
                            self.mode('none');
                            self.toggleCurrent.empty.test();
                            return;
                        }
                        self.mode('info');
                    },
                }
            };

            self.get = {
                disciplines: function(){
                    $.get('/api/disciplines/', function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()) {
                            self.current.disciplines(result.Data());
                            self.toggleCurrent.set.filter();
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
                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()) {
                            self.multiselect.data(result.Data());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                testThemes: function(id){
                    var url = '/api/tests/' + id + '/themes';
                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()) {
                            self.current.test().themes(result.Data());
                            self.multiselect.fill();
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                }
            };
            self.post = {
                test: function(action){
                    var url = '/api/tests/' + action;
                    var json = self.toggleCurrent.stringify.test();

                    $.post(url, json, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()) {
                            self.mode('none');
                            self.toggleCurrent.empty.test();
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
                            self.toggleModal('#delete-modal', 'close');
                            self.get.tests();
                            self.mode('none');
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                }
            };

            self.get.disciplines();

            self.events = {
                focusout: function(data, e){
                    var template = '#' + $(e.target).attr('tooltip-mark') + ' span';
                    var template_content = $(template).text();
                    if (!template_content) return;

                    if (!$(e.target).hasClass('tooltipstered')){
                        $(e.target).tooltipster({
                            theme: 'tooltipster-light',
                            trigger: 'custom'
                        });
                    }

                    $(e.target).tooltipster('content', template_content).tooltipster('open');
                },
                focusin: function(data, e){
                    if (!$(e.target).hasClass('tooltipstered')) return;
                    $(e.target).tooltipster('close');
                }
            };

            self.toggleModal = function(selector, action){
                $(selector).arcticmodal(action);
            };

            //SUBSCRIPTIONS
            self.pagination.itemsCount.subscribe(function(value){
                if (value){
                    self.pagination.totalPages(Math.ceil(
                        value/self.pagination.pageSize()
                    ));
                }
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
            self.current.type.subscribe(function(value){
                if (value){
                    self.current.test().type(value.id());
                }
            });

            return {
                current: self.current,
                pagination: self.pagination,
                multiselect: self.multiselect,
                toggleCurrent: self.toggleCurrent,
                mode: self.mode,
                csed: self.csed,
                filter: self.filter,
                events: self.events,
                toggleModal: self.toggleModal,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(testsViewModel());
});