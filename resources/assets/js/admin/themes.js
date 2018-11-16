$(document).ready(function(){
    var editor = ace.edit("editor");
    editor.getSession().setMode("ace/mode/c_cpp");

    var themeViewModel = function(){
        return new function(){
            var self = this;

            initializeViewModel.call(self, {
                page: menu.admin.disciplines,
                pagination: 10,
                mode: true
            });
            self.modals = {
                removeQuestion: '#remove-question-modal',
                compile: '#compile-modal',
                codeEditor: '#code-editor-modal',
                saveCode: '#save-code-modal',
                importModal: '#import-modal'
            };

            self.theme = ko.observable({});
            self.initial ={
                types: ko.observableArray(ko.mapping.fromJS(array.question)()),
                complexity: ko.observableArray(ko.mapping.fromJS(array.complexity)()),
                langs: ["C", "PHP", "Pascal"]
            };

            self.current = {
                theme: {
                    id: ko.observable(0),
                    name: ko.observable('').extend({
                        required: {
                            params: true,
                            message: 'Вы не можете оставить это поле пустым'
                        },
                        maxLength: 200
                    }),
                    mode: ko.observable(state.none)
                },
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
                importFile: ko.observable({
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
                name: ko.observable(),
                type: ko.observable(),
                complexity: ko.observable(),
                clear: function(){
                    self.filter
                        .name('')
                        .type(null)
                        .complexity(null);
                }
            };

            self.alter = {
                fill: {
                    theme: function(data){
                        self.current.theme
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
                        var theme = ko.mapping.toJS(self.current.theme);

                        theme.discipline = self.current.discipline().id();
                        delete theme.mode;

                        return JSON.stringify({
                            theme: theme,
                            disciplineId: theme.discipline
                        });
                    },
                    question: function(){
                        var answers = [];
                        var params = [];
                        var q = self.current.question();
                        var fileData = self.current.fileData();
                        var file = fileData.file() ? fileData.base64String() : null;
                        var fileType = fileData.file() ? fileData.file().type : null;
                        var program = self.code.text() ? self.code.text() : null;
                        var timeLimit = self.code.timeLimit() ? self.code.timeLimit() : null;
                        var memoryLimit = self.code.memoryLimit() ? self.code.memoryLimit() : null;
                        var lang = self.code.lang() ? self.code.lang() : null;
                        var question = {
                            image: q.image(),
                            type: q.type().id(),
                            text: q.text(),
                            complexity: q.complexity().id(),
                            time: +q.minutes() * 60 + +q.seconds()
                        };

                        self.mode() === state.update ? question.id = q.id() : null;

                        if (q.image() && !fileType){
                            fileType = 'OLD';
                        }

                        $.each(self.current.answers(), function(i, item){
                            answers.push({
                                text: item.text(),
                                isRight: item.isRight()
                            });
                        });

                        $.each(self.code.params.set(), function(i, item){
                            params.push({
                                input: item.input(),
                                expectedOutput: item.expectedOutput()
                            });
                        });

                        return JSON.stringify({
                            question: question,
                            theme: self.theme().id(),
                            answers: answers,
                            file: file,
                            fileType: fileType,
                            program: program,
                            timeLimit: timeLimit,
                            memoryLimit: memoryLimit,
                            lang: lang,
                            paramSets: params
                        });
                    },
                    importFile: function(){
                        return JSON.stringify({
                            themeId: self.current.theme.id(),
                            file: self.current.importFile().base64String(),
                            type: self.current.importFile().file().type
                        });
                    }
                },
                set: {
                    complexity: function(data){
                        var complexity = '';
                        $.each(self.initial.complexity(), function(i, item){
                            if (item.id() === data.complexity()) {
                                complexity = item.name();
                            }
                        });
                        return complexity;
                    },
                    type: function(data){
                        var type = '';
                        $.each(self.initial.types(), function(i, item){
                            if (item.id() === data.type()) {
                                type = item.name();
                            }
                        });
                        return type;
                    },
                    answerCorrectness: function(data, e){
                        var level = $(e.target).attr('level') == 1 ? true : false;
                        var type = self.current.question().type() ? self.current.question().type().id() : 0;
                        $.each(self.current.answers(), function(i, item){
                            if (type === 1){
                                if (level){
                                    item.isRight(false);
                                }
                            }
                            if (item.id() === data.id())
                                item.isRight(level);
                        });
                    }
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
                            if (!self.code.lang.isValid() ||
                                !self.code.timeLimit.isValid() ||
                                !self.code.memoryLimit.isValid())
                            {
                                self.validation[$('[accept-validation]').attr('id')].open();
                                return false;
                            }
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
                        var tps = {
                            type: null,
                            complexity: null
                        };

                        $.each(self.initial.types(), function(i, item){
                            item.id() === data.type() ? tps.type = item : null;
                        });
                        $.each(self.initial.complexity(), function(i, item){
                            item.id() === data.complexity() ? tps.complexity = item : null;
                        });

                        return tps;
                    },
                    parsedTime: function(time){
                        return {
                            minutes: Math.floor(time/60),
                            seconds: time%60
                        }
                    }
                }
            };

            self.actions = {
                theme: {
                    start: {
                        update: function(){
                            self.current.theme.mode(state.update);
                            commonHelper.buildValidationList(self.validation);
                        }
                    },
                    end: {
                        update: function(){
                            if (!self.current.theme.name.isValid()) return;
                            self.post.theme();
                        }
                    },
                    cancel: function(){
                        self.current.theme.mode(state.none);
                        self.alter.fill.theme(self.theme());
                    }
                },
                question: {
                    start: {
                        add: function(){
                            self.mode() === state.create
                                ? self.mode(state.none)
                                : self.mode(state.create);
                            self.alter.empty.question();
                            commonHelper.buildValidationList(self.validation);
                            commonHelper.scroll('#question-form');
                        },
                        update: function(data){
                            self.get.questionWithAnswers(data.id());
                            self.mode(state.update);
                            commonHelper.buildValidationList(self.validation);
                            window.scroll($("#question-form").position());
                        },
                        remove: function(data){
                            self.current.question().id(data.id());
                            self.mode(state.remove);
                            commonHelper.modal.open(self.modals.removeQuestion);
                        }
                    },
                    end: {
                        update: function(){
                            if (!self.alter.check.question()) return;
                            self.post.question();
                        },
                        remove: function(){
                            self.post.removedQuestion();
                            commonHelper.modal.close(self.modals.removeQuestion);
                        }
                    },
                    cancel: function(){
                        var selector = self.current.question().id()
                            ? '#qwn-' + self.current.question().id()
                            : '.layer';
                        commonHelper.scroll(selector);
                        self.alter.empty.question();
                        self.mode(state.none);
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
                            isRight: ko.observable(isRight),
                            isEdit: ko.observable(false)
                        });
                        self.current.answerIdCounter(++id);
                        self.alter.empty.answer();
                    },
                    remove: function(data){
                        self.current.answers.remove(function(item){
                            return item.id() === data.id();
                        });
                    },
                    edit: function (data) {
                        data.isEdit(!data.isEdit());
                    }
                },
                image: {
                    expand: function(){
                        $('.image-expander').fadeIn();
                        wheelzoom(document.querySelector('img.zoom'));
                    },
                    hide: function(){
                        $('.image-expander').fadeOut();
                        document.querySelector('img.zoom')
                            .dispatchEvent(new CustomEvent('wheelzoom.destroy'));
                    },
                    remove: function(){
                        self.current.question().showImage(null).image(null);
                    }
                },
                importFile: {
                    start: function(){
                        commonHelper.modal.open(self.modals.importModal);
                    },
                    end: function(){
                        if (!self.current.importFile().file()) return;
                        self.post.imported();
                    },
                    cancel: function(){
                        self.current.importFile().file(null)
                            .base64String(null);
                    }
                },
                exportFile: function(){
                    var win = window.open('/api/export/questions/' + self.current.theme.id(), '_blank');
                    win.focus();
                }
            };

            self.code = {
                text: ko.observable(),
                program: ko.observable(),
                timeLimit: ko.observable().extend({
                    required: true,
                    digit: true,
                    min: 1, max: 60
                }),
                memoryLimit: ko.observable().extend({
                    required: true,
                    digit: true,
                    min: 1, max: 10000
                }),
                lang: ko.observable().extend({required: true}),
                result: {
                    text: ko.observable(),
                    show: function(message){
                        self.code.result.text(message);
                        commonHelper.modal.open(self.modals.compile);
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
                    commonHelper.modal.open(self.modals.codeEditor);
                    editor.setValue(self.code.text());
                },
                compile: function(){

                    var program = JSON.stringify(editor.getValue());
                    var params = [];
                    var lang = self.code.lang();
                    var timeLimit = self.code.timeLimit();
                    var memoryLimit = self.code.memoryLimit();

                    $.each(self.code.params.set(), function(i, item){
                        var param = {
                            input: item.input(),
                            expectedOutput: item.expectedOutput()
                        };
                        params.push(param);
                    });
                    var json = JSON.stringify({program: program
                        , paramSets: params
                        , lang: lang
                        , memoryLimit: memoryLimit
                        , timeLimit: timeLimit});

                    self.post.program(json);
                    
                },
                approve: function(){
                    commonHelper.modal.open(self.modals.saveCode);
                },
                save: function(){
                    self.code.text(editor.getValue());
                    commonHelper.modal.close(self.modals.codeEditor);
                },
                clear: function(){
                    self.code.text('');
                    editor.setValue('');
                    commonHelper.modal.close(self.modals.codeEditor);
                },
                fill: function(data){
                    self.code.params.set(data.paramSets());
                    self.code.program(data.program);
                    self.code.text(data.program.template());
                    self.code.timeLimit(data.program.timeLimit());
                    self.code.memoryLimit(data.program.memoryLimit());
                    self.code.lang(data.program.lang());
                },
                empty: function(){
                    self.code.params.set([]);
                    self.code.params.input('');
                    self.code.params.output('');
                    self.code.program(null);
                    self.code.text('');
                    self.code.timeLimit('');
                    self.code.memoryLimit('');
                    self.code.lang('');
                }
            };

            self.get = {
                discipline: function(){
                    $ajaxget({
                        url: '/api/disciplines/' + self.theme().discipline(),
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.discipline(data);
                        }
                    });
                },
                questions: function(){

                    var theme = '?theme=' + self.theme().id();
                    var page = '&page=' + self.pagination.currentPage();
                    var pageSize = '&pageSize=' + self.pagination.pageSize();
                    var name = self.filter.name() ? '&name=' + self.filter.name() : '';
                    var type = self.filter.type() ? '&type=' + self.filter.type().id() : '';
                    var complexity = self.filter.complexity() ? '&complexity=' + self.filter.complexity().id(): '';
                    var url = '/api/questions/show' + theme +
                        page + pageSize + name + type + complexity;

                    $.get(url, function(response){
                        var result = JSON.parse(response);
                        var data = result.Data.data;
                        for (var i = 0; i < data.length; i++){
                            var time = data[i].time;
                            var minutes = formatTime(Math.floor(time / 60));
                            var seconds = formatTime(time % 60);
                            data[i].time = minutes + ":" + seconds;
                        }
                        if (result.Success){
                            self.current.questions(ko.mapping.fromJS(data)());
                            self.pagination.itemsCount(ko.mapping.fromJS(result.Data.count)());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                theme: function(){
                    //TODO: переделать урл
                    var url = window.location.href;
                    var themeId = +url.substr(url.lastIndexOf('/')+1);

                    url = '/api/disciplines/themes/' + themeId;

                    $ajaxget({
                        url: url,
                        errors: self.errors,
                        successCallback: function(data){
                            self.theme(data);
                            self.get.discipline();
                            self.get.questions();
                            self.alter.fill.theme(self.theme());
                        }
                    });
                },
                questionWithAnswers: function(id){
                    $ajaxget({
                        url: '/api/questions/' + id,
                        errors: self.errors,
                        successCallback: function(data){
                            data = handleKnockoutObject(data, function (data) {
                                handleArray(data.answers, function (answer) {
                                    answer.isEdit = false;
                                })
                            });
                            self.alter.fill.question(data.question, data.answers);
                            data.question.type() === types.question.code.id
                                ? self.get.code() : null;
                        }
                    });
                },
                code: function(){
                    $ajaxget({
                        url: '/api/program/byQuestion/' + self.current.question().id(),
                        errors: self.errors,
                        successCallback: function(data){
                            self.code.fill(data);
                        }
                    });
                }
            };
            self.post = {
                theme: function(){
                    $ajaxpost({
                        url: '/api/disciplines/themes/update',
                        data: self.alter.stringify.theme(),
                        errors: self.errors,
                        successCallback: function(){
                            self.theme().name(self.current.theme.name());
                            self.actions.theme.cancel();
                        }
                    });
                },
                question: function(){
                    $ajaxpost({
                        url: '/api/questions/' + self.mode(),
                        data: self.alter.stringify.question(),
                        errors: self.errors,
                        successCallback: function(){
                            self.actions.question.cancel();
                            self.get.questions();
                        }
                    });
                },
                removedQuestion: function(){
                    $ajaxpost({
                        url: '/api/questions/delete/' + self.current.question().id(),
                        data: null,
                        errors: self.errors,
                        successCallback: function(){
                            self.actions.question.cancel();
                            self.get.questions();
                        }
                    });
                },
                program: function(json){
                    $ajaxpost({
                        url: '/api/program/run',
                        data: json,
                        errors: self.errors,
                        successCallback: function(data){
                            self.code.result.show(data());
                        }
                    });
                },
                imported: function(){
                    $ajaxpost({
                        url: '/api/import/questions',
                        data: self.alter.stringify.importFile(),
                        errors: self.errors,
                        successCallback: function (data) {
                            self.actions.importFile.cancel();
                            self.inform.show({
                                message: 'Результат импорта вопросов:',
                                additionalText: 'Всего вопросов: ' + data.totalRows() + '\n' +
                                'Успешно импортировано: ' + data.imported() + '\n' +
                                'Не импортированно: ' + data.failed() +
                                (data.errors().length ? '\nОшибки импорта: ' + data.errors().join(';\n') : ''),
                                callback: function(){self.get.questions();}
                            });
                        },
                        errorCallback: function(){
                            self.actions.importFile.cancel();
                        }
                    });
                }
            };

            self.get.theme();

            self.events.answers = function(data, e){
                if (e.which === 13) {
                    self.actions.answer.add();
                }
            };
            self.events.afterRender = function(){
                commonHelper.buildValidationList(self.validation);
                return true;
            };

            //SUBSCRIPTIONS
            self.pagination.itemsCount.subscribe(function(value){
                if (!value) return;
                self.pagination.totalPages(Math.ceil(
                    value/self.pagination.pageSize()
                ));
            });
            self.pagination.currentPage.subscribe(function(){
                self.get.questions();
            });
            self.filter.type.subscribe(function(){
                self.pagination.currentPage(1);
                self.get.questions();
            });
            self.filter.complexity.subscribe(function(){
                self.pagination.currentPage(1);
                self.get.questions();
            });
            self.filter.name.subscribe(function(){
                self.pagination.currentPage(1);
                self.get.questions();
            });
            self.current.question().type.subscribe(function(value){
                if (!value) return;
                self.current.question().isOpenSingleLine(false);
                self.current.question().isOpenMultiLine(false);
                self.current.question().isCode(false);
                if (value.id() === 1){
                    $.each(self.current.answers(), function(i, item){
                        item.isRight(false);
                    });
                    return;
                }
                if (value.id() === 3){
                    self.current.question().isOpenSingleLine(true);
                    $.each(self.current.answers(), function(i, item){
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

            var ret = returnStandart.call(self);
            ret.theme = self.theme;
            return ret;
        };
    };

    ko.applyBindings(themeViewModel());
});