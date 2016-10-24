/**
 * Created by nyanjii on 19.10.16.
 */
$(document).ready(function(){
    var themeViewModel = function(){
        return new function(){
            var self = this;

            self.theme = ko.observable({});

            self.current = ko.observable({
                theme: ko.observable({
                    id: ko.observable(0),
                    name: ko.observable('')
                }),
                discipline: ko.observable({
                    id: ko.observable(0),
                    name: ko.observable('')
                }),
                questions: ko.observableArray([]),
                question: ko.observable({
                    id: ko.observable(0),
                    text: ko.observable(''),
                    time: ko.observable(0),
                    complexity: ko.observable(0),
                    type: ko.observable(0),
                    minutes: ko.observable(),
                    seconds: ko.observable()
                }),
                answer: ko.observable({
                    name: ko.observable(''),
                    isRight: ko.observable(false)
                }),
                answers: ko.observableArray([])
            });
            self.filter = ko.observable({
                name: ko.observable(''),
                type: ko.observable(),
                complexity: ko.observable(),
                types: ko.observableArray([
                    {id: ko.observable(1), name: ko.observable('Закрытый с одним правильным ответом')},
                    {id: ko.observable(2), name: ko.observable('Закрытый с несколькими правильными ответами')},
                    {id: ko.observable(3), name: ko.observable('Открытый однострочный')},
                    {id: ko.observable(4), name: ko.observable('Открытый многострочный')}
                    ]),
                complexityTypes: ko.observableArray([
                    {id: ko.observable(1), name: ko.observable('Лёгкий')},
                    {id: ko.observable(2), name: ko.observable('Средний')},
                    {id: ko.observable(3), name: ko.observable('Сложный')}
                ])
            });
            self.toggleCurrent = ko.observable({
                fill: ko.observable({
                    theme: function(data){
                        self.current().theme()
                            .id(data.id())
                            .name(data.name());
                    },
                    question: function(data){
                        self.current().question()
                            .id(data.id())
                            .text(data.text())
                            .time(data.time())
                            .complexity(data.complexity())
                            .type(data.type());
                    },
                    answers: function(){}
                }),
                empty: ko.observable({
                    question: function(){
                        self.current().question()
                            .id(0)
                            .text('')
                            .time(0)
                            .complexity(0)
                            .type(0)
                            .minutes(0)
                            .seconds(0);
                    },
                    answer: function(){
                        self.current().answer().name('').isRight(false);
                    },
                    answers: function(){
                        self.current().answers([]);
                    }
                }),
                stringify: ko.observable({
                    theme: function(){
                        var disciplineId = self.current().discipline().id();
                        var themeForPost = {
                            id: self.current().theme().id(),
                            name: self.current().theme().name(),
                            discipline: disciplineId
                        };

                        return JSON.stringify({
                            theme: themeForPost,
                            disciplineId: disciplineId
                        });
                    },
                    question: function(){
                        var answers = [];
                        var curq = self.current().question();
                        var question = {
                            type: curq.type().id(),
                            text: curq.text(),
                            complexity: curq.complexity().id(),
                            time: +curq.minutes() * 60 + +curq.seconds()
                        };
                        self.current().answers().find(function(item){
                            var answer = {
                                text: item.name(),
                                isRight: item.isRight()
                            };
                            answers.push(answer);
                        });

                        return JSON.stringify({question: question, theme: self.theme().id(), answers: answers});
                    }
                }),
                set: ko.observable({
                    complexity: function(data){
                        var complexityId = data.complexity();
                        var complexity = '';
                        self.filter().complexityTypes().find(function(item){
                            if (item.id() === complexityId) {
                                complexity = item.name();
                                return;
                            }
                            return;
                        });
                        return complexity;
                    },
                    type: function(data){
                        var typeId = data.type();
                        var type = '';
                        self.filter().types().find(function(item){
                            if (item.id() === typeId) {
                                type = item.name();
                                return;
                            }
                            return;
                        });
                        return type;
                    },
                    answerCorrectness: function(data, e){
                        var level = $(e.target).attr('level') == 1 ? true : false;
                        var type = self.current().question().type() ? self.current().question().type().id() : 0;
                        console.log(type);
                        self.current().answers().find(function(item){
                            if (type === 1){
                                if (level){
                                    item.isRight(false);
                                }
                            }
                            if (item.id() === data.id())
                                item.isRight(level);
                        });
                    },
                })
            });
            self.pagination = ko.observable({
                currentPage: ko.observable(1),
                pageSize: ko.observable(10),
                itemsCount: ko.observable(1),
                totalPages: ko.observable(1),

                selectPage: function(page){
                    self.pagination().currentPage(page);
                    self.get().questions();
                },
                dotsVisible: function(index){
                    var total = self.pagination().totalPages();
                    var current = self.pagination().currentPage();
                    if (total > 11 && index == total-1 && index > current + 2  ||total > 11 && index == current - 1 && index > 3)  {
                        return true;
                    }
                    return false;
                },
                pageNumberVisible: function(index){
                    var total = self.pagination().totalPages();
                    var current = self.pagination().currentPage();
                    if (total < 12 ||
                        index > (current - 2) && index < (current + 2) ||
                        index > total - 2 ||
                        index < 3) {
                        return true;
                    }
                    return false;
                },
            });
            self.mode = ko.observable('none');
            self.csed = ko.observable({
                theme: ko.observable({
                    edit: function(){
                        self.mode('theme.edit');
                    },
                    update: function(){
                        self.theme().name(self.current().theme().name());
                        self.post().theme();
                        self.mode('none');
                    },
                    cancel: function(){
                        self.mode('none');
                        self.toggleCurrent().fill().theme(self.theme());
                    }
                }),
                question: ko.observable({
                    toggleAdd: function(){
                        self.mode() === 'add' ? self.mode('none') : self.mode('add');
                        self.toggleCurrent().empty().question();
                    },
                    add: function(){
                        self.post().question();
                    }
                }),
                answer: ko.observable({
                    add: function(){
                        var name = self.current().answer().name();
                        var isRight = self.current().answer().isRight();
                        var id = self.current().answers().length ? self.current().answers().length + 1 : 1;

                        self.current().answers.push({
                            id: ko.observable(id),
                            name: ko.observable(name),
                            isRight: ko.observable(isRight)
                        });
                        self.toggleCurrent().empty().answer();
                    },
                    remove: function(data){
                        self.current().answers.remove(function(item){
                            return item.id() === data.id();
                        });
                    }
                })
            });

            self.get = ko.observable({
                discipline: function(){
                    $.get('/api/disciplines/' + self.theme().discipline(), function(response){
                        var res = ko.mapping.fromJSON(response);
                        self.current().discipline().id(res.id()).name(res.name());
                    });
                },
                questions: function(){
                    var theme = 'theme=' + self.theme().id();
                    var page = '&page=' + self.pagination().currentPage();
                    var pageSize = '&pageSize=' + self.pagination().pageSize();
                    var name = '&name=' + self.filter().name();
                    var type = '&type=' + (self.filter().type() ? self.filter().type().id() : '');
                    var complexity = '&complexity=' + (self.filter().complexity() ? self.filter().complexity().id() : '');

                    var url = '/api/questions/show?' + theme +
                        page + pageSize +
                        name + type + complexity;

                    $.get(url, function(response){
                        var res = ko.mapping.fromJSON(response);
                        self.current().questions(res.data());
                        self.pagination().itemsCount(res.count());
                    });
                },
                theme: function(){
                    var url = window.location.href;
                    var themeId = +url.substr(url.lastIndexOf('/')+1);

                    $.get('/api/disciplines/themes/' + themeId, function(response){
                        self.theme(ko.mapping.fromJSON(response));
                        self.get().discipline();
                        self.get().questions();
                        self.toggleCurrent().fill().theme(self.theme());
                    });
                }
            });
            self.post = ko.observable({
                theme: function(){
                    var url = '/api/disciplines/themes/update';
                    var json = self.toggleCurrent().stringify().theme();

                    $.post(url, json, function(){});
                },
                question: function(){
                    var url = '/api/questions/create';
                    var json = self.toggleCurrent().stringify().question();
                    $.post(url, json, function(){
                        self.toggleCurrent().empty().question();
                        self.mode('none');
                        self.get().questions();
                    });
                }
            });

            self.get().theme();



            self.toggleModal = function(selector, action){
                $(selector).arcticmodal(action);
            };

            //SUBSCRIPTIONS
            self.pagination().itemsCount.subscribe(function(value){
                if (value){
                    self.pagination().totalPages(Math.ceil(
                        value/self.pagination().pageSize()
                    ));
                }
            });
            self.filter().type.subscribe(function(){
                self.get().questions();
            });
            self.filter().complexity.subscribe(function(){
                self.get().questions();
            });
            self.filter().name.subscribe(function(){
                self.get().questions();
            });
            self.current().question().type.subscribe(function(value){
                if (value){
                    if (value.id() === 1){
                        self.current().answers().find(function(item){
                            item.isRight(false);
                        });
                    }
                }
            });

            return {
                theme: self.theme,
                pagination: self.pagination,
                toggleCurrent: self.toggleCurrent,
                current: self.current,
                mode: self.mode,
                csed: self.csed,
                filter: self.filter,
                toggleModal: self.toggleModal
            };
        };
    };

    ko.applyBindings(themeViewModel());
});