$(document).ready(function(){

    var editor = ace.edit("editor");
    editor.getSession().setMode("ace/mode/c_cpp");


    var themeViewModel = function(){
        return new function(){
            var self = this;
            self.page = ko.observable(menu.admin.disciplines);
            self.errors = errors();
            self.pagination = pagination();
            self.validation = {};
            self.events = new validationEvents(self.validation);


            self.theme = ko.observable({});

            self.current = {
                theme: ko.observable({
                    id: ko.observable(0),
                    name: ko.observable('').extend({
                        required: {
                            params: true,
                            message: 'Вы не можете оставить это поле пустым'
                        },
                        maxLength: 200
                    })
                }),
                discipline: ko.observable({}),
                questions: ko.observableArray([]),
                question: ko.validatedObservable({
                    id: ko.observable(0),
                    text: ko.observable().extend({required: true}),
                    time: ko.observable(0),
                    complexity: ko.observable().extend({required: true}),
                    type: ko.observable().extend({required: true}),
                    minutes: ko.observable().extend({
                        required: true,
                        number:true,
                        min: 0,
                        max: 60
                    }),
                    seconds: ko.observable().extend({
                        required: true,
                        min: 0,
                        max: 59
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
                code: ko.observable(),
                answerIdCounter: ko.observable(0)
            };
            self.filter = {
                name: ko.observable(''),
                type: ko.observable(null),
                complexity: ko.observable(null),
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
                ]),
                clear: function(){
                    self.filter.name('')
                        .type(null).complexity(null);
                }
            };

            self.alter = {
                fill: {
                    theme: function(data){
                        self.current.theme()
                            .id(data.id())
                            .name(data.name());
                    },
                    question: function(data, answers){
                        self.alter.empty.file();
                        var types = self.alter.get.types(data);
                        var time = self.alter.get.parsedTime(data.time());


                        self.current.question()
                            .id(data.id())
                            .text(data.text())
                            .time(data.time())
                            .complexity(types.complexity)
                            .type(types.type)
                            .minutes(time.minutes)
                            .seconds(time.seconds)
                            .image(data.image())
                            .showImage(data.image());
                        self.current.answers(answers());
                        self.current.answerIdCounter(0);
                    }
                },
                empty: {
                    question: function(){
                        self.current.question()
                            .id(0)
                            .text('')
                            .time(0)
                            .complexity(null)
                            .type(null)
                            .minutes('')
                            .seconds('')
                            .image(null)
                            .showImage(null);
                        self.current.answers([]);
                        self.current.answerIdCounter(0);
                        self.alter.empty.file();
                        self.code.empty();
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
                                input: item.input(),
                                expectedOutput: item.expectedOutput()
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
                        commonHelper.buildValidationList(self.validation);

                        if (!q.isValid()){
                            self.validation[$('[accept-validation]').attr('id')].open();
                            return false;
                        }

                        var type = q().type().id();
                        var answers = self.current.answers();

                        if (type === questionType.openMultiLine) return true;

                        var validateAnswers = $('[special]').attr('id');
                        self.validation[validateAnswers].option('timer', 2000);

                        if (type === questionType.openSingleLine){
                            if (answers.length) return true;
                            self.validation[validateAnswers].text('Пожалуйта, укажите хотя бы один вариант ответа');
                            self.validation[validateAnswers].open();
                            return false;
                        }
                        if (type === questionType.code){
                            if (self.code.params.set().length) return true;
                            self.validation[validateAnswers].open();
                            return false;
                        }

                        if (answers.length < 2) {
                            self.validation[validateAnswers].text('Пожалуйста, укажите хотя бы 2 варианта ответа');
                            self.validation[validateAnswers].open();
                            return false;
                        }
                        var correctAnswers = 0;
                        $.each(answers, function(i, answer){
                            correctAnswers = answer.isRight()
                                ? correctAnswers + 1
                                : correctAnswers;
                        });
                        if (correctAnswers) return true;
                        self.validation[validateAnswers].text('Пожалуйста, укажите хотя бы 1 правильный вариант ответа');
                        self.validation[validateAnswers].open();
                        return false;
                    }
                },
                get: {
                    types: function(data){
                        var type = self.filter.types().find(function(item){
                            return item.id() === data.type();
                        });
                        var complexity = self.filter.complexityTypes().find(function(item){
                            return item.id() === data.complexity();
                        });
                        return {
                            type: type,
                            complexity: complexity
                        }
                    },
                    parsedTime: function(time){
                        return {
                            minutes: Math.floor(time/60),
                            seconds: time%60
                        }
                    }
                }
            };

            self.mode = ko.observable('none');
            self.csed = {
                theme: {
                    edit: function(){
                        self.mode('theme.edit');
                        commonHelper.buildValidationList(self.validation);
                    },
                    update: function(){
                        if (!self.current.theme().name.isValid()) return;
                        self.theme().name(self.current.theme().name());
                        self.post.theme();
                        self.mode('none');
                    },
                    cancel: function(){
                        self.mode('none');
                        self.alter.fill.theme(self.theme());
                    }
                },
                question: {
                    toggleAdd: function(){
                        self.mode() === 'add' ? self.mode('none') : self.mode('add');
                        self.alter.empty.question();
                        commonHelper.buildValidationList(self.validation);
                    },
                    cancel: function(){
                        self.mode('none');
                        self.alter.empty.question();
                    },
                    update: function(){
                        var isQok = self.alter.check.question();
                        if (!isQok) return;
                        self.mode() === 'add' ? self.post.question('create') : self.post.question('update');
                    },
                    edit: function(data){
                        self.get.questionWithAnswers(data.id());
                        self.mode('edit');
                        commonHelper.buildValidationList(self.validation);
                    },
                    startDelete: function(data){
                        self.get.questionWithAnswers(data.id());
                        self.mode('delete');
                        commonHelper.modal.open('#delete-modal');
                    },
                    remove: function(){
                        self.post.removedQuestion();
                        commonHelper.modal.close('#delete-modal');
                    }
                },
                answer: {
                    add: function(){
                        var text = self.current.answer().text();
                        if (!text) return;
                        var isRight = self.current.answer().isRight();
                        var id = self.current.answerIdCounter();

                        self.current.answers.push({
                            id: ko.observable(id),
                            text: ko.observable(text),
                            isRight: ko.observable(isRight)
                        });
                        self.current.answerIdCounter(++id);
                        self.alter.empty.answer();
                    },
                    remove: function(data){
                        self.current.answers.remove(function(item){
                            return item.id() === data.id();
                        });
                    }
                },
                image: {
                    expand: function(){
                        $('.image-expander').show();
                    },
                    remove: function(){
                        self.current.question().showImage(null);
                    }
                }
            };

            self.code = {
                text: ko.observable(),
                program: ko.observable(),
                result: {
                    text: ko.observable(),
                    show: function(message){
                        self.code.result.text(message);
                        commonHelper.modal.open('#compile-modal');
                    }
                },
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
                            id: ko.observable('param_' + id),
                            input: ko.observable(input),
                            expectedOutput: ko.observable(output)
                        });
                        params.input('').output('').id(id + 1);
                    },
                    remove: function(data){
                        var params = self.code.params;
                        params.set.remove(function(item){
                            return item.id === data.id;
                        });
                    }
                },
                open: function(){
                    commonHelper.modal.open('#code-editor-modal');
                    editor.setValue(self.code.text());
                },
                compile: function(){
                    var program = JSON.stringify(editor.getValue());
                    var params = [];

                    self.code.params.set().find(function(item){
                        var param = {
                            input: item.input(),
                            expectedOutput: item.expectedOutput()
                        };
                        params.push(param);
                    });
                    var json = JSON.stringify({program: program, paramSets: params});
                    self.post.program(json);
                },
                approve: function(){
                    commonHelper.modal.open('#save-code-modal');
                },
                save: function(){
                    self.code.text(editor.getValue());
                    commonHelper.modal.close('#code-editor-modal');
                },
                clear: function(){
                    self.code.text('');
                    editor.setValue('');
                    commonHelper.modal.close('#code-editor-modal');
                },
                fill: function(data){
                    self.code.params.set(data.paramSets());
                    self.code.program(data.program);
                    self.code.text(data.program.template());
                },
                empty: function(){
                    self.code.params.set([]);
                    self.code.params.input('');
                    self.code.params.output('');
                    self.code.program(null);
                    self.code.text('');
                },
            };

            self.get = {
                discipline: function(){
                    var url = '/api/disciplines/' + self.theme().discipline();
                    $get(url, function(data){
                        self.current.discipline(data);
                    }, self.errors)();
                },
                questions: function(){
                    var url = '/api/questions/show?' +
                        'theme=' + self.theme().id() +
                        '&page=' + self.pagination.currentPage() +
                        '&pageSize=' + self.pagination.pageSize() +
                        '&name=' + self.filter.name() +
                        '&type=' + (self.filter.type() ? self.filter.type().id() : '') +
                        '&complexity=' + (self.filter.complexity() ? self.filter.complexity().id(): '');
                    $get(url, function(data){
                        self.current.questions(data.data());
                        self.pagination.itemsCount(data.count());
                    }, self.errors)();
                },
                theme: function(){
                    var url = window.location.href;
                    var themeId = +url.substr(url.lastIndexOf('/')+1);
                    url = '/api/disciplines/themes/' + themeId;
                    $get(url, function(data){
                        self.theme(data);
                        self.get.discipline();
                        self.get.questions();
                        self.alter.fill.theme(self.theme());
                    }, self.errors)();
                },
                questionWithAnswers: function(id){
                    var url = '/api/questions/' + id;
                    $get(url, function(data){
                        self.alter.fill.question(data.question, data.answers);
                        if (data.question.type() === 5){
                            self.get.code();
                        }
                    }, self.errors)();
                },
                code: function(){
                    var url = '/api/program/byQuestion/' + self.current.question().id();
                    $get(url, function(data){
                        self.code.fill(data);
                    }, self.errors)();
                }
            };
            self.post = {
                theme: function(){
                    var url = '/api/disciplines/themes/update';
                    var json = self.alter.stringify.theme();
                    $post(url, json, self.errors)();
                },
                question: function(action){
                    var url = '/api/questions/' + action;
                    var json = self.alter.stringify.question();
                    $post(url, json, self.errors, function(){
                        self.alter.empty.question();
                        self.mode('none');
                        self.get.questions();
                    })();
                },
                removedQuestion: function(){
                    var url = '/api/questions/delete/' + self.current.question().id();
                    $post(url, '', self.errors, function(){
                        self.mode('none');
                        self.alter.empty.question();
                        self.get.questions();
                    })();
                },
                program: function(json){
                    $post('/api/program/run', json, self.errors, function(data){
                        self.code.result.show(data());
                    })();
                }
            };

            self.get.theme();

            self.events.answers = function(data, e){
                if (e.which === 13) {
                    self.csed.answer.add();
                }
            };

            //SUBSCRIPTIONS
            self.pagination.itemsCount.subscribe(function(value){
                if (value){
                    self.pagination.totalPages(Math.ceil(
                        value/self.pagination.pageSize()
                    ));
                }
            });
            self.pagination.currentPage.subscribe(function(value){
                self.get.questions();
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
                // if (value){
                //     var validated = value + '';
                //     validated = validated.replace(/[^0-9]/g, "");
                //     validated = validated.substr(0, 2);
                //     validated = +validated >= 60 ? '60' : validated;
                //     self.current.question().minutes(validated);
                // }
            });
            self.current.question().seconds.subscribe(function(value){
                // if (value){
                //     var validated = value + '';
                //     validated = validated.replace(/[^0-9]/g, "");
                //     validated = validated.substr(0, 2);
                //     validated = +validated >= 60 ? '59' : validated;
                //     self.current.question().seconds(validated);
                // }
            });



            return {
                page: self.page,
                theme: self.theme,
                pagination: self.pagination,
                alter: self.alter,
                current: self.current,
                mode: self.mode,
                csed: self.csed,
                filter: self.filter,
                events: self.events,
                errors: self.errors,
                code: self.code
            };
        };
    };

    ko.applyBindings(themeViewModel());
});