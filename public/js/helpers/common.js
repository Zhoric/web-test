/**
 * Created by nyanjii on 26.11.16.
 */
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
    }
};
