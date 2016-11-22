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
                }),
                mark: {
                    isInput: ko.observable(false),
                    value: ko.observable('Оценить')
                }
            };
            self.actions = {
                answer: {
                    show: function(data){
                        self.current.answer().id() === data.id() ?
                            self.current.answer().id(0) :
                            self.toggleCurrent.fill.answer(data);
                    }
                },
                mark: {
                    edit: function(data){
                        self.current.mark.isInput(true);
                        self.current.mark.value('');
                        if (data.rightPercentage()){
                            self.current.mark.value(data.rightPercentage());
                        }
                    },
                    approve: function(data){
                        var value = self.current.mark.value;
                        if ($.isNumeric(value()) && value() <= 100 && value() >= 0 && value() !== ''){
                            data.rightPercentage(value());
                            self.current.mark.isInput(false);
                        }
                        value('Оценить');
                    },
                    cancel: function(){
                        self.current.mark.isInput(false);
                        self.current.mark.value('Оценить');
                    },
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
            };
            self.mode = ko.observable('none');

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