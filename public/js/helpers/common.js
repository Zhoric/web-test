var state = {
    none: 'none',
    update: 'update',
    remove: 'remove',
    create: 'create',
    info: 'info',
    overall: 'overall',
    themes: 'themes',
    materials: 'materials',
    anchor: 'anchor'
};

var filters = {
    active: {
        all: 'all',
        active: 'active',
        inactive: 'inactive'
    }
};

var testType = {
    control: 1,
    study: 2
};

var questionType = {
    closedSingle: 1,
    closedMultiple: 2,
    openSingleLine: 3,
    openMultiLine: 4,
    code: 5
};

var types = {
    question: {
        singleAnswer: {
            id: 1,
            rusname: 'Закрытый с одним правильным ответом'
        },
        multiAnswer: {
            id: 2,
            rusname: 'Закрытый с несколькими правильными ответами'
        },
        singleLine: {
            id: 3,
            rusname: 'Открытый однострочный'
        },
        multiLine: {
            id: 4,
            rusname: 'Открытый многострочный'
        },
        code: {
            id: 5,
            rusname: 'Программный код'
        }
    },
    test: {
        control: {
            id: 1,
            name: 'control',
            rusname: 'Контроль знаний'
        },
        study: {
            id: 2,
            name: 'study',
            rusname: 'Обучающий'
        },
        name: function(type) {
            var test = types.test;
            switch (type) {
                case test.control.id:
                    return test.control.name;
                    break;
                case test.study.id:
                    return test.study.name;
                    break;
            }
        }
    },
    complexity: {
        easy: {
            id: 1,
            rusname: 'Лёгкий'
        },
        medium: {
            id: 2,
            rusname: 'Средний'
        },
        hard: {
            id: 3,
            rusname: 'Сложный'
        }
    }
};

var array = {
    complexity: [
        {id: types.complexity.easy.id, name: types.complexity.easy.rusname},
        {id: types.complexity.medium.id, name: types.complexity.medium.rusname},
        {id: types.complexity.hard.id, name: types.complexity.hard.rusname}
    ],
    question: [
        {id: types.question.singleAnswer.id, name:  types.question.singleAnswer.rusname},
        {id: types.question.multiAnswer.id, name:  types.question.multiAnswer.rusname},
        {id: types.question.singleLine.id, name:  types.question.singleLine.rusname},
        {id: types.question.multiLine.id, name:  types.question.multiLine.rusname},
        {id: types.question.code.id, name:  types.question.code.rusname}
    ]
};

var role = {
    student: {
        name: 'student',
        location: '/home'
    },
    lecturer: {
        name: 'lecturer',
        location: '/admin'
    },
    admin: {
        name: 'admin',
        location: '/admin'
    }
};

var complexity = {
    easy: 1,
    medium: 2,
    hard: 3
};

var criterion = {
    firstTry: 'firstTry',
    secondTry: 'secondTry',
    mark: 'maxMark'
};

var testStatus = {
    all: 'all-disciplines',
    left: 'has-tests',
    none: 'no-tests'
};
var attemptsStatus = {
    all: 'all-tests',
    some: 'started-not-finished',
    left: 'not-started-yet',
    none: 'finished'
};

var testCheckedStatus = {
    all: 'all-tests',
    done: 'checked-tests',
    not: 'unchecked-tests'
};

var commonHelper = {
    parseDate: function(date){
        date = new Date(date);
        var options = {
            timezone: 'UTC',
            year: 'numeric',
            month: 'numeric',
            day: 'numeric'
        };
        date = date.toLocaleString("be", options);

        return date;
    },

    buildValidationList: function(validation){
        $('[validate]').each(function(){
            var selector = $(this).attr('id');
            validation[selector] = new validationTooltip({
                selector: '#' + selector
            });
        });
        $('[accept-validation]').each(function(){
            var selector = $(this).attr('id');
            validation[selector] = new validationTooltip({
                selector: '#' + selector,
                side: 'left'
            });
            validation[selector].option('timer', 2000);
        });
    },
    shortenText: function(text, length){
        var dots = (text.length > length) ? ' ...' : '';
        return text.substr(0, length) + dots;
    },
    modal: {
        open: function(selector){
            $(selector).arcticmodal({
                closeOnEsc: false,
                closeOnOverlayClick: false,
                overlay: {
                    css: {
                        backgroundColor: '#000',
                        backgroundPosition: '50% 0',
                        opacity: .5
                    }
                }
            });
        },
        close: function(selector){
            $(selector).arcticmodal('close');
        }
    },
    cookies: {
        create: function(cookies){
            for (var prop in cookies){
                if (cookies.hasOwnProperty(prop)){
                    $.cookie(prop, cookies[prop], { path : "/" });
                }
            }
        },
        remove: function(cookies){
            for (var prop in cookies){
                if (cookies.hasOwnProperty(prop)){
                    $.removeCookie(prop, { path : "/" });
                }
            }
        }
    },
    scroll: function(selector){

        // $('html, body').animate({
        //     scrollTop: $(selector).offset().top
        // }, 1000);
    },
    parseAnswers: function(answers){
        return answers
            .replace(/<\/answer>/g, '\n\n')
            .slice(0, answers.lastIndexOf('\n\n'));
    },
    tooltip: function(settings){
        $(settings.selector).tooltipster({
            theme: 'tooltipster-light',
            side: settings.side ? settings.side : 'top'
        });
    }
};

var interval = {
    fivesec: 5000,
    thirtysec: 30000,
    onemin: 60000
};

var menu = {
    admin: {
        main: 'institutes',
        lecturers: 'lecturers',
        groups: 'groups',
        students: 'students',
        disciplines: 'disciplines',
        tests: 'tests',
        results: 'results',
        materials: 'materials'
    },
    student: {
        main: 'home',
        results: 'results',
        stats: 'statistics',
        faq: 'faq',
        materials: 'materials'
    }
};


