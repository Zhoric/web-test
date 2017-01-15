/**
 * Created by nyanjii on 28.10.16.
 */
$(document).ready(function(){
    var editor = ace.edit("editor");
    editor.getSession().setMode("ace/mode/c_cpp");

    var testingViewModel = function(){
        return new function(){
            var self = this;

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
                question: ko.observable(null),
                answers: ko.observableArray([]),
                answerText: ko.observable(''),
                singleAnswer: ko.observable(0),
                timeLeft : ko.observable(-1),
                testResult: ko.observable()
            };
            self.errors = errors();
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
            self.toggleCurrent = {
                stringify: {
                    answer: function(){
                        var qType = self.current.question().type();
                        var ids = [];
                        var answer = {
                            questionId: self.current.question().id()
                        };

                        if (qType === 1){
                            ids.push(self.current.singleAnswer());
                            answer.answerIds = ids;
                        }
                        if (qType === 2){
                            self.current.answers().find(function(item){
                                if (item.isRight() === true){
                                    ids.push(item.id());
                                }
                            });
                            answer.answerIds = ids;
                        }
                        if (qType === 3 || qType === 4){
                            answer.answerText = self.current.answerText();
                        }
                        if (qType === 5){
                            answer.answerText = self.code.text();
                        }
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
                question: function(){
                    $get('/api/tests/nextQuestion', function(data){
                        if (data.hasOwnProperty('question')){
                            self.current.question(data.question);
                            self.current.timeLeft(data.question.time());

                            data.question.type() === questionType.code
                                ? self.get.code()
                                : self.code.empty();self.code.empty();

                            data.answers()
                                ? self.current.answers(data.answers())
                                : self.current.answers([]);
                        }
                    }, self.errors)();
                },
                code: function(){
                    var id = self.current.question().id();
                    var url = '/api/program/byQuestion/' + id;

                    $get(url, function(data){
                        self.code.fill(data);
                    }, self.errors)();
                }
            };
            self.post = {
                answers: function(){
                    var json = self.toggleCurrent.stringify.answer();
                    console.log(json);
                    $post('/api/tests/answer', json, self.errors, function(){
                        self.toggleCurrent.clear();
                        self.get.question();
                    })();
                },
                startTest: function(){
                    var url = window.location.href;
                    var id = +url.substr(url.lastIndexOf('/')+1);

                    $post('/api/tests/start', JSON.stringify({testId: id}), self.errors, function(){
                        var test = url.substring(url.indexOf('test/') + 5, url.lastIndexOf('/'));
                        self.allowTimer(test === types.test.control.name);
                        self.get.question();
                    })();
                }
            };

            self.post.startTest();

            // TIMER
            self.allowTimer.subscribe(function(value){
                if (!value) return;
                setInterval(function(){
                    var time = self.current.timeLeft() - 1;
                    self.current.timeLeft(time);
                }, 1000);
            });
            self.current.timeLeft.subscribe(function(value){
                if (!value){
                    if(self.current.question()){
                        self.actions.answer();
                    }
                }
            });

            return {
                toggleCurrent: self.toggleCurrent,
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