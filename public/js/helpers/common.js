/**
 * Created by nyanjii on 26.11.16.
 */
var state = {
    none: 'none',
    update: 'update',
    remove: 'remove',
    create: 'create',
    info: 'info'
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
            rusname: 'открытый многострочный'
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
        name: function(type){
            var test = types.test;
            switch(type){
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
            id: 1,
            rusname: 'Средний'
        },
        hard: {
            id: 1,
            rusname: 'Сложный'
        }
    }
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
}



var complexity = {
    easy: 1,
    medium: 2,
    hard: 3
};

var commonHelper = {
    parseDate: function(date){
        date = new Date(date);
        var options = {
            timezone: 'UTC',
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric',
            year: 'numeric',
            month: 'numeric',
            day: 'numeric'
        };
        date = date.toLocaleString("ru", options);
        date = date.replace(',', ' ');

        return date;
    },
    shortenText: function(text, length){
        var dots = (text.length > length) ? ' ...' : '';
        return text.substr(0, length) + dots;
    },
    modal: {
        open: function(selector){
            $(selector).arcticmodal();
        },
        close: function(selector){
            $(selector).arcticmodal('close');
        }
    },
    parseAnswers: function(answers){
        return answers
            .replace(/<\/answer>/g, '\n\n')
            .slice(0, answers.lastIndexOf('\n\n'));
    }
};


