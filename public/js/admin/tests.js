/**
 * Created by nyanjii on 26.10.16.
 */
$(document).ready(function(){
    var testsViewModel = function(){
        return new function(){
            var self = this;

            self.current = {
                test: ko.observable({
                    id: ko.observable(0),
                    subject: ko.observable(''),
                    attempts: ko.observable(3),
                    timeTotal: ko.observable(0),
                    minutes: ko.observable(''),
                    seconds: ko.observable(''),
                    type: ko.observable(0),
                    isActive: ko.observable(true),
                    isRandom: ko.observable(true)
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
                type: ko.observable(),

                themes: ko.observableArray([]),
                selectedThemes: ko.observableArray([])
            };
            self.filter = {
                name: ko.observable(''),
                discipline: ko.observable(),
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
                            .isRandom(true);
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

                        self.current.themes().find(function(item){
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
            self.mode = ko.observable('none');
            self.csed = {
                test: {
                    show: function(data){
                        self.toggleCurrent.fill.test(data);
                        self.mode('info');
                    },
                    toggleAdd: function(){
                        self.toggleCurrent.empty.test();
                        if (self.mode() === 'add'){
                            self.mode('none');
                        }
                        else{
                            self.mode('add');
                            self.get.themes();
                        }

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
                        self.get.themes();
                    },
                    update: function(){
                        self.mode() === 'add' ? self.post.test('create') : self.post.test('update');
                    },
                    cancel: function(){
                        if (self.mode() === 'add'){
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
                        self.current.disciplines(ko.mapping.fromJSON(response)());
                        self.toggleCurrent.set.filter();
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
                        var res = ko.mapping.fromJSON(response);
                        self.current.tests(res.data());
                        self.pagination.itemsCount(res.count());
                    });
                },
                themes: function(){
                    var url = '/api/disciplines/' + self.filter.discipline().id() + '/themes';
                    $.get(url, function(response){
                        self.current.themes(ko.mapping.fromJSON(response)());
                    });
                }
            };
            self.post = {
                test: function(action){
                    var url = '/api/tests/' + action;
                    var json = self.toggleCurrent.stringify.test();

                    $.post(url, json, function(){
                        self.mode('none');
                        self.get.tests();
                    });
                },
                removedTest: function(){
                    $.post('/api/tests/delete/' + self.current.test().id(), function(){
                        self.toggleModal('#delete-modal', 'close');
                        self.get.tests();
                        self.mode('none');
                    });
                }
            };

            self.get.disciplines();




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
            self.filter.discipline.subscribe(function(){
                self.mode('none');
                self.get.tests();
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
                toggleCurrent: self.toggleCurrent,
                mode: self.mode,
                csed: self.csed,
                filter: self.filter,
                toggleModal: self.toggleModal
            };
        };
    };

    ko.applyBindings(testsViewModel());
});