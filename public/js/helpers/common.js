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


