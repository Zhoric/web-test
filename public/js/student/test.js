$(document).ready(function(){
    var editor = ace.edit("editor");
    editor.getSession().setMode("ace/mode/c_cpp");

    var testingViewModel = function(){
        return new function(){
            var self = this;
            self.errors = errors();
            self.code = {
                task: ko.observable(''),
                text: ko.observable(''),
                write: function(){
                    commonHelper.modal.open('#code-editor-modal');
                },
                clear: function(){
                    editor.setValue('');
                },
                fill: function(data){
                    self.code.task(self.current.question().text());
                    editor.setValue(data.program.template());
                },
                empty: function(){
                    self.code
                        .text('')
                        .task('');
                },
                approve: function(){
                    self.code.text(editor.getValue());
                }
            };
            self.allowTimer = ko.observable(null);
            self.current = {
                test: {
                    id: ko.observable(),
                    name: ko.observable(''),
                    discipline: ko.observable(''),
                    type: ko.observable()
                },
                question: ko.observable(null),
                answers: ko.observableArray([]),
                answerText: ko.observable(''),
                singleAnswer: ko.observable(0),
                timeLeft : ko.observable(-1),
                testResult: ko.observable()
            };

            self.actions = {
                answer: function(){
                    self.post.answers();
                },
                goHome: function(){
                    window.location.href = '/home';
                },
                image: {
                    expand: function(){
                        $('#image-expander').fadeIn();
                    },
                    hide: function(){
                        $('#image-expander').fadeOut();
                    },
                }
            };
            self.alter = {
                stringify: {
                    answer: function(){
                        var qType = self.current.question().type();
                        var ids = [];
                        var answer = {
                            questionId: self.current.question().id()
                        };

                        switch(qType){
                            case questionType.closedSingle:
                                ids.push(self.current.singleAnswer());
                                break;
                            case questionType.closedMultiple:
                                self.current.answers().find(function(item){
                                    if (item.isRight() === true){
                                        ids.push(item.id());
                                    }
                                });
                                break;
                            case questionType.openSingleLine:
                            case questionType.openMultiLine:
                                answer.answerText = self.current.answerText();
                                break;
                            case questionType.code:
                                answer.answerText = self.code.text();
                                break;
                        }
                        answer.answerIds = ids;

                        return JSON.stringify(answer);
                    }
                },
                clear: function(){
                    self.current.singleAnswer(0);
                    self.current.answerText('');
                    self.current.answers([]);
                    self.current.question(null);
                    self.code.clear();
                }
            };

            self.get = {
                description: function(){
                    var cookie = $.cookie();

                    if (!cookie.testId){
                        alert('хитрая жопа');
                        return;
                    }
                    self.current.test
                        .id(+cookie.testId).name(cookie.testName)
                        .type(+cookie.testType).discipline(cookie.disciplineName);

                    commonHelper.cookies.remove(cookie);

                    self.post.startTest();
                },
                question: function(){
                    $ajaxget({
                        url: '/api/tests/nextQuestion',
                        errors: self.errors,
                        successCallback: function(data){
                            if (!data.hasOwnProperty('question')) return;

                            self.current.question(data.question);
                            self.current.timeLeft(data.question.time());

                            data.question.type() === questionType.code
                                ? self.get.code()
                                : self.code.empty();

                            data.answers()
                                ? self.current.answers(data.answers())
                                : self.current.answers([]);
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
                answers: function(){
                    var json = self.alter.stringify.answer();

                    $ajaxpost({
                        url: '/api/tests/answer',
                        data: json,
                        errors: self.errors,
                        successCallback: function(){
                            self.alter.clear();
                            self.get.question();
                        }
                    });
                },
                startTest: function(){
                    $ajaxpost({
                        url: '/api/tests/start',
                        data: JSON.stringify({testId: self.current.test.id()}),
                        errors: self.errors,
                        successCallback: function(){
                            self.allowTimer(self.current.test.type() === types.test.control.id);
                            self.get.question();
                        }
                    });
                }
            };

            self.get.description();

            // TIMER
            self.allowTimer.subscribe(function(value){
                if (!value) return;
                setInterval(function(){
                    var time = self.current.timeLeft() - 1;
                    self.current.timeLeft(time);
                }, 1000);
            });
            self.current.timeLeft.subscribe(function(value){
                if (value || !self.current.question()) return;
                self.actions.answer();
            });

            return {
                current: self.current,
                actions: self.actions,
                code: self.code,
                errors: self.errors,
                timer: self.allowTimer
            };
        };
    };

    ko.applyBindings(testingViewModel());
});