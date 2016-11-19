/**
 * Created by nyanjii on 01.10.16.
 */
//var doubleNameRegex = '^[А-ЯЁ][а-яё]+(\-{1}(?:[А-ЯЁ]{1}(?:[а-яё]+)))?$';
$(document).ready(function(){

    ko.validation.init({
        messagesOnModified: true,
        insertMessages: false
    });

    var registerViewModel = function(){
        return new function(){

            self.user = ko.validatedObservable({
                name : {
                    last: ko.observable().extend({
                        required: {
                            params: true,
                            message: 'Поле обязательно для заполнения'
                        },
                        pattern: {
                            params: '^[А-ЯЁ][а-яё]+(\-{1}(?:[А-ЯЁ]{1}(?:[а-яё]+)))?$',
                            message: 'Некорректный формат фамилии. Прим. "Иванов" или "Иванов-Семёнов"'
                        },
                        maxLength: {
                            params: 100,
                            message: 'Длина поля не может быть более 100 символов.'
                        }
                    }),
                    first: ko.observable().extend({
                        required: {
                            params: true,
                            message: 'Поле обязательно для заполнения'
                        },
                        pattern: {
                            params: '^[А-ЯЁ][а-яё]+(\-{1}(?:[А-ЯЁ]{1}(?:[а-яё]+)))?$',
                            message: 'Некорректный формат имени. Прим. "Мария" "Мария-Вера"'
                        },
                        maxLength: {
                            params: 100,
                            message: 'Длина поля не может быть более 100 символов.'
                        }
                    }),
                    patronymic: ko.observable().extend({
                        pattern: {
                            params: '^[А-ЯЁ][а-яё]+(\-{1}(?:[А-ЯЁ]{1}(?:[а-яё]+)))?$',
                            message: 'Некорректный формат отчества.'
                        },
                        maxLength: {
                            params: 100,
                            message: 'Длина поля не может быть более 100 символов.'
                        }
                    })
                },
                email: ko.observable().extend({
                    required: {
                        params: true,
                        message: 'Поле обязатель для заполнения'
                    },
                    email: {
                        params: true,
                        message: 'Некорректный формат ввода email'
                    },
                    maxLength: {
                        params: 100,
                        message: 'Длина поля не может быть более 100 символов.'
                    }
                }),
                password: ko.observable().extend({
                    required: {
                        params: true,
                        message: 'Поле обязательно для заполнения'
                    },
                    pattern: {
                        params: '^[a-zA-Z0-9]{8}$',
                        message: 'Пароль может состоять только из 8 символов букв и цифр.'
                    }
                }),
                group: ko.observable().extend({
                    required: true
                })
            });
            self.valid = ko.observable(true);
            self.groups = ko.observableArray([]);
            self.registerResult = ko.observable();

            self.stringify = function(){
                var user = {
                    lastname: self.user().name.last(),
                    firstname: self.user().name.first(),
                    patronymic: self.user().name.patronymic(),
                    email: self.user().email(),
                    password: self.user().password()
                };
                return JSON.stringify({
                    user: user,
                    groupId: self.user().group().id()
                });
            };
            self.register = function(){
                if (!self.check.user()) return;
                var url = '/register';
                var json = self.stringify();
                $.post(url, json, function(response){
                    self.registerResult(ko.mapping.fromJSON(response));
                    self.modal('#register-info', '');
                });
            };

            self.check = {
                email: function(){
                    //TODO:[UI] добавить проверку занят ли такой email
                },
                user: function(){
                    if (!self.user.isValid()){
                        self.events.showTooltip.overall();
                        if (!self.user().group.isValid()){
                            self.events.showTooltip.group();
                            return false;
                        }
                        return false;
                    }
                    self.events.closeTooltip.overall();
                    return true;
                }
            };
            self.get = {
                groups: function(){
                    $.get('api/groups/', function(response){
                        self.groups(ko.mapping.fromJSON(response)());
                    });
                }
            };
            self.modal = function(selector, action){
                $(selector).arcticmodal(action);
            };
            self.acceptInformation = function(){
                self.modal('register-info', 'close');
                if (self.registerResult().success()){
                    window.location.href = '/login';
                }
            };

            self.get.groups();

            self.events = {
                focusout: function(data, e){
                    var template = '#' + $(e.target).attr('tooltip-mark') + ' span';

                    var template_content = $(template).text();
                    console.log(template_content);
                    if (!template_content) return;

                    if (!$(e.target).hasClass('tooltipstered')){
                        $(e.target).tooltipster({
                            theme: 'tooltipster-light',
                            trigger: 'custom',
                            position: 'right'
                        });
                    }

                    $(e.target).tooltipster('content', template_content).tooltipster('open');
                },
                focusin: function(data, e){
                    self.events.closeTooltip.overall();
                    if (!$(e.target).hasClass('tooltipstered')) return;
                    $(e.target).tooltipster('close');
                },
                showTooltip: {
                    group: function(){
                        var groupField = 'select[tooltip-mark=group_tooltip]';
                        var message = $('#group_tooltip span').text();
                        if (!$(groupField).hasClass('tooltipstered')){
                            $(groupField).tooltipster({
                                theme: 'tooltipster-light',
                                trigger: 'custom',
                                position: 'right'
                            });
                        }
                        $(groupField).tooltipster('content', message).tooltipster('open');
                    },
                    overall: function(){
                        var saveButton = 'button[tooltip-mark=overall_tooltip]';
                        var message = $('#overall_tooltip span').text();
                        if (!$(saveButton).hasClass('tooltipstered')){
                            $(saveButton).tooltipster({
                                theme: 'tooltipster-light',
                                trigger: 'custom',
                                position: 'left'
                            });
                        }
                        $(saveButton).tooltipster('content', message).tooltipster('open');
                    }
                },
                closeTooltip:{
                    group: function(){
                        var groupField = 'select[tooltip-mark=group_tooltip]';
                        if (!$(groupField).hasClass('tooltipstered')) return;
                        $(groupField).tooltipster('close');
                    },
                    overall: function(){
                        var saveButton = 'button[tooltip-mark=overall_tooltip]';
                        if (!$(saveButton).hasClass('tooltipstered')) return;
                        $(saveButton).tooltipster('close');
                    }
                }
            };

            self.user().group.subscribe(function(value){
                if (value){
                    self.events.closeTooltip.group();
                    self.events.closeTooltip.overall();
                }
            });

            return {
                user: self.user,
                register: self.register,
                registerResult: self.registerResult,
                acceptInformation: self.acceptInformation,
                groups: self.groups,
                events: self.events
            };
        };
    };

    ko.applyBindings(registerViewModel());
});