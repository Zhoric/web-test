/**
 * Created by nyanjii on 19.10.16.
 */
$(document).ready(function(){

    var themeViewModel = function(){
        return new function(){
            var self = this;

            self.theme = ko.observable({});

            self.current = {
                result: ko.observable(),
                test: ko.observable(),
                attempts: ko.observable(),
                answers: ko.observableArray([]),
                answer: ko.observable({
                    id: ko.observable(0),
                    answer: ko.observable(),
                    question: ko.observable({
                        text: ko.observable()
                    }),
                    rightPercentage: ko.observable()
                })
            };
            self.actions = {
                answer: {
                    show: function(data){
                        self.current.answer().id() === data.id() ?
                            self.current.answer().id(0) :
                            self.toggleCurrent.fill.answer(data);
                    }
                }
            },

            self.toggleCurrent = {
                fill: {
                    answer: function(d){
                        self.current.answer()
                            .id(d.id())
                            .answer(d.answer())
                            .question(d.question)
                            .rightPercentage(d.rightPercentage());
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
                        var curq = self.current.question();
                        var fileData = self.current.fileData()
                        var file = fileData.file() ? fileData.base64String() : null;
                        var fileType = fileData.file() ? fileData.file().type : null;
                        var question = {
                            type: curq.type().id(),
                            text: curq.text(),
                            complexity: curq.complexity().id(),
                            time: +curq.minutes() * 60 + +curq.seconds()
                        };

                        if (curq.image() && !fileType){
                            fileType = 'OLD';
                        }

                        self.mode() === 'edit' ? question.id = curq.id() : '';
                        self.current.answers().find(function(item){
                            var answer = {
                                text: item.text(),
                                isRight: item.isRight()
                            };
                            answers.push(answer);
                        });

                        return JSON.stringify({question: question, theme: self.theme().id(), answers: answers, file: file, fileType: fileType});
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
                    show: function(){

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
                result: function(){
                    var url = window.location.href;
                    var id = +url.substr(url.lastIndexOf('/')+1);
                    url = '/api/results/' + id;

                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        console.log(result);
                        if (result.Success()){
                            self.current.answers(result.Data.answers());
                            self.current.result(result.Data.testResult);
                            self.current.attempts(result.Data.attemptsAllowed);
                            self.current.test(result.Data.test);
                            console.log(self.current.result());
                        }
                        else{
                            //result.Message
                        }
                    })
                }
            };
            self.post = {

            };

            self.get.result();

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


            return {
                actions: self.actions,
                toggleCurrent: self.toggleCurrent,
                current: self.current,
                mode: self.mode,
                csed: self.csed,
                filter: self.filter,
                events: self.events,
                toggleModal: self.toggleModal
            };
        };
    };

    ko.applyBindings(themeViewModel());
});