/**
 * Created by nyanjii on 19.10.16.
 */
$(document).ready(function(){
    var themeViewModel = function(){
        return new function(){
            var self = this;

            self.theme = ko.observable({});

            self.current = {
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
                    complexity: ko.observable(),
                    type: ko.observable(),
                    minutes: ko.observable(),
                    seconds: ko.observable(),
                    isOpenMultiLine: ko.observable(false),
                    isOpenSingleLine: ko.observable(false),
                    validationMessage: ko.observable('')
                }),
                fileData: ko.observable({
                    file: ko.observable(),
                    dataURL: ko.observable(),
                    base64String: ko.observable()
                }),
                answer: ko.observable({
                    text: ko.observable(''),
                    isRight: ko.observable(false)
                }),
                answers: ko.observableArray([]),
            };
            ko.fileBindings.defaultOptions = {
                fileName: true
            };
            self.filter = {
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
            };

            self.toggleCurrent = {
                fill: {
                    theme: function(data){
                        self.current.theme()
                            .id(data.id())
                            .name(data.name());
                    },
                    question: function(data){
                        var type = self.filter.types().find(function(item){
                            return item.id() === data.type();
                        });
                        var complexity = self.filter.complexityTypes().find(function(item){
                            return item.id() === data.complexity();
                        });
                        var minutes = Math.floor(data.time()/60);
                        var seconds = data.time()%60;

                        self.current.question()
                            .id(data.id())
                            .text(data.text())
                            .time(data.time())
                            .complexity(complexity)
                            .type(type)
                            .minutes(minutes)
                            .seconds(seconds);
                    },
                    answers: function(){}
                },
                empty: {
                    question: function(){
                        self.current.question()
                            .id(0)
                            .text('')
                            .time(0)
                            .complexity(0)
                            .type(0)
                            .minutes('')
                            .seconds('');
                        self.current.answers([]);
                    },
                    answer: function(){
                        self.current.answer().text('').isRight(false);
                    },
                    answers: function(){
                        self.current.answers([]);
                    }
                },
                stringify: {
                    theme: function(){
                        var disciplineId = self.current.discipline().id();
                        var themeForPost = {
                            id: self.current.theme().id(),
                            name: self.current.theme().name(),
                            discipline: disciplineId
                        };

                        return JSON.stringify({
                            theme: themeForPost,
                            disciplineId: disciplineId
                        });
                    },
                    question: function(){
                        var answers = [];
                        var curq = self.current.question();
                        var question = {
                            type: curq.type().id(),
                            text: curq.text(),
                            complexity: curq.complexity().id(),
                            time: +curq.minutes() * 60 + +curq.seconds(),
                            file: self.current.fileData().base64String(),
                            fileType: self.current.fileData().file().type
                        };
                        self.mode() === 'edit' ? question.id = curq.id() : '';
                        self.current.answers().find(function(item){
                            var answer = {
                                text: item.text(),
                                isRight: item.isRight()
                            };
                            answers.push(answer);
                        });

                        return JSON.stringify({question: question, theme: self.theme().id(), answers: answers});
                    }
                },
                set: {
                    complexity: function(data){
                        var complexityId = data.complexity();
                        var complexity = '';
                        self.filter.complexityTypes().find(function(item){
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
                        self.filter.types().find(function(item){
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
                        var type = self.current.question().type() ? self.current.question().type().id() : 0;
                        self.current.answers().find(function(item){
                            if (type === 1){
                                if (level){
                                    item.isRight(false);
                                }
                            }
                            if (item.id() === data.id())
                                item.isRight(level);
                        });
                    },
                },
                check: {
                    question: function(){
                        var q = self.current.question();
                        var awr = self.current.answers();
                        if (!q.text()) {
                            q.validationMessage('Отсутствует трактовка вопроса');
                            self.toggleModal('#validation-modal', '');
                            return false;
                        }
                        if (!q.minutes() || !q.seconds()){
                            q.validationMessage('Время не определено');
                            self.toggleModal('#validation-modal', '');
                            return false;
                        }
                        if (!q.type()){
                            q.validationMessage('Не указан тип вопроса');
                            self.toggleModal('#validation-modal', '');
                            return false;
                        }
                        if (!q.complexity()){
                            q.validationMessage('Не указана сложность вопроса');
                            self.toggleModal('#validation-modal', '');
                            return false;
                        }
                        if (q.type().id() !== 4) {
                            if (awr.length < 2) {
                                q.validationMessage('Слишком мало вариантов ответа');
                                self.toggleModal('#validation-modal', '');
                                return false;
                            }
                            else{
                                var correct = 0;
                                awr.find(function(item){
                                    if (item.isRight() === true) correct++;
                                });
                                if (!correct){
                                    q.validationMessage('Вопрос должен соржать хотя бы один правильный ответ');
                                    self.toggleModal('#validation-modal', '');
                                    return false;
                                }
                            }
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
                    self.get.questions();
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
                theme: {
                    edit: function(){
                        self.mode('theme.edit');
                    },
                    update: function(){
                        self.theme().name(self.current.theme().name());
                        self.post.theme();
                        self.mode('none');
                    },
                    cancel: function(){
                        self.mode('none');
                        self.toggleCurrent.fill.theme(self.theme());
                    }
                },
                question: {
                    toggleAdd: function(){
                        self.mode() === 'add' ? self.mode('none') : self.mode('add');
                        self.toggleCurrent.empty.question();
                    },
                    cancel: function(){
                        self.mode('none');
                        self.toggleCurrent.empty.question();
                    },
                    update: function(){
                        var isQok = self.toggleCurrent.check.question();
                        if (!isQok) return;
                        self.mode() === 'add' ? self.post.question('create') : self.post.question('update');
                    },
                    edit: function(data){
                        self.get.questionWithAnswers(data.id());
                        self.mode('edit');
                    },
                    startDelete: function(data){
                        self.get.questionWithAnswers(data.id());
                        self.mode('delete');
                        self.toggleModal('#delete-modal', '');
                    },
                    remove: function(){
                        self.post.removedQuestion();
                        self.toggleModal('#delete-modal', 'close');
                    }
                },
                answer: {
                    add: function(){
                        var text = self.current.answer().text();
                        if (!text) return;
                        var isRight = self.current.answer().isRight();
                        var id = self.current.answers().length ? self.current.answers().length + 1 : 1;

                        self.current.answers.push({
                            id: ko.observable(id),
                            text: ko.observable(text),
                            isRight: ko.observable(isRight)
                        });
                        self.toggleCurrent.empty.answer();
                    },
                    remove: function(data){
                        self.current.answers.remove(function(item){
                            return item.id() === data.id();
                        });
                    }
                }
            };

            self.get = {
                discipline: function(){
                    $.get('/api/disciplines/' + self.theme().discipline(), function(response){
                        var res = ko.mapping.fromJSON(response);
                        self.current.discipline().id(res.id()).name(res.name());
                    });
                },
                questions: function(){
                    var theme = 'theme=' + self.theme().id();
                    var page = '&page=' + self.pagination.currentPage();
                    var pageSize = '&pageSize=' + self.pagination.pageSize();
                    var name = '&name=' + self.filter.name();
                    var type = '&type=' + (self.filter.type() ? self.filter.type().id() : '');
                    var complexity = '&complexity=' + (self.filter.complexity() ? self.filter.complexity().id() : '');

                    var url = '/api/questions/show?' + theme +
                        page + pageSize +
                        name + type + complexity;

                    $.get(url, function(response){
                        var res = ko.mapping.fromJSON(response);
                        self.current.questions(res.data());
                        self.pagination.itemsCount(res.count());
                    });
                },
                theme: function(){
                    var url = window.location.href;
                    var themeId = +url.substr(url.lastIndexOf('/')+1);

                    $.get('/api/disciplines/themes/' + themeId, function(response){
                        self.theme(ko.mapping.fromJSON(response));
                        self.get.discipline();
                        self.get.questions();
                        self.toggleCurrent.fill.theme(self.theme());
                    });
                },
                questionWithAnswers: function(id){
                    var url = '/api/questions/' + id;
                    $.get(url, function(response){
                        var res = ko.mapping.fromJSON(response);
                        self.current.answers(res.answers());
                        self.toggleCurrent.fill.question(res.question);
                    });
                }
            };
            self.post = {
                theme: function(){
                    var url = '/api/disciplines/themes/update';
                    var json = self.toggleCurrent.stringify.theme();

                    $.post(url, json, function(){});
                },
                question: function(action){
                    var url = '/api/questions/' + action;
                    var json = self.toggleCurrent.stringify.question();
                    $.post(url, json, function(){
                        self.toggleCurrent.empty.question();
                        self.mode('none');
                        self.get.questions();
                    });
                },
                removedQuestion: function(){
                    var url = '/api/questions/delete/' + self.current.question().id();
                    $.post(url, function(){
                        self.mode('none');
                        self.toggleCurrent.empty.question();
                        self.get.questions();
                    })
                }
            };

            self.get.theme();



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
            self.filter.type.subscribe(function(){
                self.get.questions();
            });
            self.filter.complexity.subscribe(function(){
                self.get.questions();
            });
            self.filter.name.subscribe(function(){
                self.get.questions();
            });
            self.current.question().type.subscribe(function(value){
                if (!value) return;
                self.current.question().isOpenSingleLine(false);
                self.current.question().isOpenMultiLine(false);
                if (value.id() === 1){
                    self.current.answers().find(function(item){
                        item.isRight(false);
                    });
                    return;
                }
                if (value.id() === 3){
                    self.current.question().isOpenSingleLine(true);
                    return;
                }
                if (value.id() === 4){
                    self.current.question().isOpenMultiLine(true);
                    self.current.answers([]);
                    return;
                }
            });
            self.current.question().minutes.subscribe(function(value){
                var validated = value.replace(/[^0-9]/g, "");
                validated = validated.substr(0, 2);
                validated = +validated >= 60 ? '60' : validated;
                self.current.question().minutes(validated);
            });
            self.current.question().seconds.subscribe(function(value){
                var validated = value.replace(/[^0-9]/g, "");
                validated = validated.substr(0, 2);
                validated = +validated >= 60 ? '59' : validated;
                self.current.question().seconds(validated);
            });
            self.current.fileData().file.subscribe(function(value){console.log(value);});

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