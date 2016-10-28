/**
 * Created by nyanjii on 28.10.16.
 */
$(document).ready(function(){
    ko.bindingHandlers.timer = {
        update: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
            var sec = valueAccessor()() + 1;
            var timer = setInterval(function () {
                $(element).text(--sec);
                if (sec == 0) {
                    bindingContext.$data.actions.answer();
                    sec = valueAccessor()() + 1;
                }
            }, 1000);
        }
    };

    var testingViewModel = function(){
        return new function(){
            var self = this;

            self.current = {
                question: ko.observable(),
                answers: ko.observableArray([]),
                answerText: ko.observable(''),
                singleAnswer: ko.observable(0),
                timeLeft : ko.observable(-1)
            };

            self.actions = {
                answer: function(){
                    self.post.answers();
                }
            };
            self.toggleCurrent = {
                stringify: {
                    answer: function(){
                        var ids = [];
                        if (self.current.question().type() === 1){
                            ids.push(self.current.singleAnswer());
                        }
                        else{
                            self.current.answers().find(function(item){
                                if (item.isRight() === true){
                                    ids.push(item.id());
                                }
                            });
                        }

                        return JSON.stringify({
                            questionId: self.current.question().id(),
                            answersIds: ids,
                            answerText: self.current.answerText()
                        });
                    }
                },
                clear: function(){
                    self.current.singleAnswer(0);
                    self.current.answerText('');
                    self.current.answers([]);
                }
            };

            self.get = {
                question: function(){
                    $.get('/api/tests/nextQuestion', function(response){
                        console.log(response);
                        var res = ko.mapping.fromJSON(response);
                        self.current.question(res.question);
                        self.current.answers(res.answers());
                        self.current.timeLeft(res.question.time());
                    });
                }
            };
            self.post = {
                answers: function(){
                    var json = self.toggleCurrent.stringify.answer();
                    $.post('/api/tests/answer', json, function(){
                        self.toggleCurrent.clear();
                        self.get.question();
                    });
                },
                startTest: function(){
                    $.post('/api/tests/start', JSON.stringify({testId: 5}), function(){
                            self.get.question();
                    });
                }
            };

            self.post.startTest();

            self.toggleModal = function(selector, action){
                $(selector).arcticmodal(action);
            };

            return {
                toggleCurrent: self.toggleCurrent,
                current: self.current,
                actions: self.actions
            };
        };
    };

    ko.applyBindings(testingViewModel());
});