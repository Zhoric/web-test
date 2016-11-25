/**
 * Created by nyanjii on 19.10.16.
 */
$(document).ready(function(){

    var editor = ace.edit("editor");
    editor.getSession().setMode("ace/mode/c_cpp");

    ko.validation.init({
        messagesOnModified: true,
        insertMessages: false
    });
    var themeViewModel = function(){
        return new function(){
            var self = this;

            self.theme = ko.observable({});
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
                theme: ko.observable({
                    id: ko.observable(0),
                    name: ko.observable('').extend({
                        required: {
                            params: true,
                            message: 'Вы не можете оставить это поле пустым'
                        },
                        maxLength: {
                            params: 200,
                            message: 'Длина не может превышать 200 символов'
                        }
                    })
                }),
                discipline: ko.observable({
                    id: ko.observable(0),
                    name: ko.observable('')
                }),
                questions: ko.observableArray([]),
                question: ko.validatedObservable({
                    id: ko.observable(0),
                    text: ko.observable().extend({
                        required: {
                            params: true,
                            message: 'Постановка вопроса обязательна'
                        }
                    }),
                    time: ko.observable(0),
                    complexity: ko.observable().extend({required: true}),
                    type: ko.observable().extend({required: true}),
                    minutes: ko.observable().extend({
                        required: {
                            message: 'Поле не может быть пустым',
                            params: true
                        }
                    }),
                    seconds: ko.observable().extend({
                        required: {
                            params: true,
                            message: 'Поле не может быть пустым'
                        }
                    }),
                    image: ko.observable(),
                    showImage: ko.observable(),
                    isOpenMultiLine: ko.observable(false),
                    isOpenSingleLine: ko.observable(false),
                    isCode: ko.observable(false)
                }),
                fileData: ko.observable({
                    file: ko.observable(),
                    dataURL: ko.observable(),
                    base64String: ko.observable()
                }),
                answer: ko.validatedObservable({
                    text: ko.observable(),
                    isRight: ko.observable(false)
                }),
                answers: ko.observableArray([]),
            };
            self.filter = {
                name: ko.observable(''),
                type: ko.observable(),
                complexity: ko.observable(),
                types: ko.observableArray([
                    {id: ko.observable(1), name: ko.observable('Закрытый с одним правильным ответом')},
                    {id: ko.observable(2), name: ko.observable('Закрытый с несколькими правильными ответами')},
                    {id: ko.observable(3), name: ko.observable('Открытый однострочный')},
                    {id: ko.observable(4), name: ko.observable('Открытый многострочный')},
                    {id: ko.observable(5), name: ko.observable('Программный код')},
                    ]),
                complexityTypes: ko.observableArray([
                    {id: ko.observable(1), name: ko.observable('Лёгкий')},
                    {id: ko.observable(2), name: ko.observable('Средний')},
                    {id: ko.observable(3), name: ko.observable('Сложный')}
                ])
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
                    theme: function(data){
                        self.current.theme()
                            .id(data.id())
                            .name(data.name());
                    },
                    question: function(data, answers){
                        self.toggleCurrent.empty.file();
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
                            .seconds(seconds)
                            .image(data.image())
                            .showImage(data.image());
                        self.current.answers(answers());
                    }
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
                            .seconds('')
                            .image(null)
                            .showImage(null);
                        self.current.answers([]);
                        self.toggleCurrent.empty.file();
                    },
                    answer: function(){
                        self.current.answer().text('').isRight(false);
                    },
                    answers: function(){
                        self.current.answers([]);
                    },
                    file: function(){
                        self.current.fileData()
                            .file('')
                            .dataURL('')
                            .base64String('');
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
                        var params = [];
                        var curq = self.current.question();
                        var fileData = self.current.fileData()
                        var file = fileData.file() ? fileData.base64String() : null;
                        var fileType = fileData.file() ? fileData.file().type : null;
                        var program = self.code.text() ? self.code.text() : null;
                        var question = {
                            type: curq.type().id(),
                            text: curq.text(),
                            complexity: curq.complexity().id(),
                            time: +curq.minutes() * 60 + +curq.seconds()
                        };

                        self.mode() === 'edit' ? question.id = curq.id() : '';

                        if (curq.image() && !fileType){
                            fileType = 'OLD';
                        }

                        self.current.answers().find(function(item){
                            var answer = {
                                text: item.text(),
                                isRight: item.isRight()
                            };
                            answers.push(answer);
                        });

                        self.code.params.set().find(function(item){
                            var parameter = {
                                input: item.input,
                                expectedOutput: item.output
                            }
                            params.push(parameter);
                        });

                        return JSON.stringify({
                            question: question,
                            theme: self.theme().id(),
                            answers: answers,
                            file: file,
                            fileType: fileType,
                            program: program,
                            paramSets: params
                        });
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
                        var q = self.current.question;
                        var selector = '.approve-btn';

                        if (!q.isValid()){
                            self.validationTooltip.open(selector, 'Поля не заполнены');
                            return false;
                        }

                        var ansr = self.current.answers();
                        var tId = q().type().id();

                        if (tId === 1 || tId === 2){
                            if (ansr.length < 2){
                                self.validationTooltip.open(selector, 'Должно быть хотя бы 2 ответа');
                                return false;
                            }
                            var correct = 0;
                            ansr.find(function(item){
                                if (item.isRight()) ++correct;
                            });
                            if (!correct){
                                self.validationTooltip.open(selector, 'Не выбрано ни одного правильного варианта ответа');
                                return false;
                            }
                        }
                        if (tId === 3) {
                            if (!ansr.length) {
                                self.validationTooltip.open(selector, 'Должен быть хотя бы один вариант ответа');
                                return false;
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
                        if (!self.current.theme().name.isValid()) return;
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
                        self.validationTooltip.checkIfExists('.approve-btn');
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
                        self.validationTooltip.checkIfExists('.approve-btn');
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
                },
                image: {
                    expand: function(){
                        $('.expanded-image').show();
                    },
                    remove: function(){
                        self.current.question().showImage(null);
                    }
                }
            };

            self.get = {
                discipline: function(){
                    $.get('/api/disciplines/' + self.theme().discipline(), function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()) {
                            self.current.discipline()
                                .id(result.Data.id())
                                .name(result.Data.name());
                            return;
                        }
                        self.errors.show(result.Message());
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
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()) {
                            self.current.questions(result.Data.data());
                            self.pagination.itemsCount(result.Data.count());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                theme: function(){
                    var url = window.location.href;
                    var themeId = +url.substr(url.lastIndexOf('/')+1);

                    $.get('/api/disciplines/themes/' + themeId, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()) {
                            self.theme(result.Data);
                            self.get.discipline();
                            self.get.questions();
                            self.toggleCurrent.fill.theme(self.theme());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                questionWithAnswers: function(id){
                    var url = '/api/questions/' + id;
                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()) {
                            self.toggleCurrent.fill.question(result.Data.question, result.Data.answers);
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                }
            };
            self.post = {
                theme: function(){
                    var url = '/api/disciplines/themes/update';
                    var json = self.toggleCurrent.stringify.theme();

                    $.post(url, json, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (!result.Success()) {
                            self.errors.show(result.Message());
                        }
                    });
                },
                question: function(action){
                    var url = '/api/questions/' + action;
                    var json = self.toggleCurrent.stringify.question();
                    $.post(url, json, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()) {
                            self.toggleCurrent.empty.question();
                            self.mode('none');
                            self.get.questions();
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                removedQuestion: function(){
                    var url = '/api/questions/delete/' + self.current.question().id();
                    $.post(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()) {
                            self.mode('none');
                            self.toggleCurrent.empty.question();
                            self.get.questions();
                            return;
                        }
                        self.errors.show(result.Message());
                    })
                },
                program: function(json){
                    $.post('api/program/run', json, function(response){
                        console.log(response);
                    });
                }
            };

            self.get.theme();

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
                self.current.question().isCode(false);
                if (value.id() === 1){
                    self.current.answers().find(function(item){
                        item.isRight(false);
                    });
                    return;
                }
                if (value.id() === 3){
                    self.current.question().isOpenSingleLine(true);
                    self.current.answers().find(function(item){
                        item.isRight(true);
                    });
                    return;
                }
                if (value.id() === 4){
                    self.current.question().isOpenMultiLine(true);
                    self.current.answers([]);
                    return;
                }
                if (value.id() === 5){
                    self.current.question().isCode(true);
                    self.current.answers([]);
                    return;
                }
            });
            self.current.question().minutes.subscribe(function(value){
                if (value){
                    var validated = value + '';
                    validated = validated.replace(/[^0-9]/g, "");
                    validated = validated.substr(0, 2);
                    validated = +validated >= 60 ? '60' : validated;
                    self.current.question().minutes(validated);
                }
            });
            self.current.question().seconds.subscribe(function(value){
                if (value){
                    var validated = value + '';
                    validated = validated.replace(/[^0-9]/g, "");
                    validated = validated.substr(0, 2);
                    validated = +validated >= 60 ? '59' : validated;
                    self.current.question().seconds(validated);
                }
            });

            self.code = {
                text: ko.observable(),
                params: {
                    set: ko.observableArray([]),
                    input: ko.observable(),
                    output: ko.observable(),
                    id: ko.observable(1),
                    add: function(){
                        var params = self.code.params;
                        var input = params.input();
                        var output = params.output();
                        var id = params.id();
                        if (!input || !output) return;
                        params.set.push({
                            id: 'param_' + id,
                            input: input,
                            output: output
                        });
                        params.input('').output('').id(id + 1);
                    },
                    remove: function(data){
                        var params = self.code.params;
                        params.set.remove(function(item){
                            return item.id === data.id;
                        });
                    },
                },
                open: function(){
                    self.toggleModal('#code-editor-modal', '');
                },
                compile: function(){
                    var program = JSON.stringify(editor.getValue());
                    var params = [];
                    self.code.params.set().find(function(item){
                        var param = {
                            input: item.input,
                            expectedOutput: item.output
                        };
                        params.push(param);
                    });
                    var json = JSON.stringify({program: program, paramSets: params});
                    console.log(json);
                    self.post.program(json);
                },
                approve: function(){
                    self.code.text(editor.getValue());
                }
            };

            return {
                theme: self.theme,
                pagination: self.pagination,
                toggleCurrent: self.toggleCurrent,
                current: self.current,
                mode: self.mode,
                csed: self.csed,
                filter: self.filter,
                events: self.events,
                toggleModal: self.toggleModal,
                errors: self.errors,
                code: self.code
            };
        };
    };

    ko.applyBindings(themeViewModel());
});