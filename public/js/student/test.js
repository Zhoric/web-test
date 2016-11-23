/**
 * Created by nyanjii on 28.10.16.
 */
$(document).ready(function(){

    var testingViewModel = function(){
        return new function(){
            var self = this;

            self.current = {
                question: ko.observable(),
                answers: ko.observableArray([]),
                answerText: ko.observable(''),
                singleAnswer: ko.observable(0),
                timeLeft : ko.observable(-1),

                testResult: ko.observable()
            };
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

                        return JSON.stringify(answer);
                    }
                },
                clear: function(){
                    self.current.singleAnswer(0);
                    self.current.answerText('');
                    self.current.answers([]);
                    self.current.question(null);
                }
            };

            self.get = {
                question: function(){
                    $.get('/api/tests/nextQuestion', function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            if (result.Data.hasOwnProperty('question')){
                                self.current.question(result.Data.question);
                                self.current.timeLeft(result.Data.question.time());
                                if (result.Data.answers() == null) {
                                    self.current.answers([]);
                                }
                                else{
                                    self.current.answers(result.Data.answers());
                                }
                            }
                            else{
                                self.current.testResult(result.Data);
                            }
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                }
            };
            self.post = {
                answers: function(){
                    var json = self.toggleCurrent.stringify.answer();
                    $.post('/api/tests/answer', json, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            self.toggleCurrent.clear();
                            self.get.question();
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                startTest: function(){
                    var url = window.location.href;
                    var id = +url.substr(url.lastIndexOf('/')+1);

                    $.post('/api/tests/start', JSON.stringify({testId: id}), function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            self.get.question();
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                }
            };

            self.post.startTest();

            self.toggleModal = function(selector, action){
                $(selector).arcticmodal(action);
            };

            // TIMER

            setInterval(function(){
                var time = self.current.timeLeft() - 1;
                self.current.timeLeft(time);
            }, 1000);

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
                errors: self.errors
            };
        };
    };

    ko.applyBindings(testingViewModel());
});