ko.observable.fn.copy = function(data){
    this(ko.mapping.fromJS(ko.mapping.toJS(data)));
};
ko.observable.fn.cut = function(length){
    var dots = (this().length > length) ? ' ...' : '';
    return this().substr(0, length) + dots;
};
ko.observable.fn.parseDate = function(){
    var t = this().split(/[- :]/);
    var d = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]));
    var date = new Date(d);
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
};
ko.observable.fn.parseDay = function(){
    var date = new Date(this());
    var options = {
        timezone: 'UTC',
        year: 'numeric',
        month: 'numeric',
        day: 'numeric'
    };
    date = date.toLocaleString("ru", options);
    date = date.replace(',', ' ');

    return date;
};
ko.observable.fn.parseDayFromString = function(){
    var t = this().split(/[- :]/);
    var d = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]));
    var date = new Date(d);
    var options = {
        timezone: 'UTC',
        year: 'numeric',
        month: 'numeric',
        day: 'numeric'
    };
    date = date.toLocaleString("ru", options);
    date = date.replace(',', ' ');

    return date;
};
ko.observable.fn.parseAnswer = function(){
    if (!this()) return;
    var text = this() + '\n';
    return text.replace(/<\/answer>/g, '\n');
        //.slice(0, this().lastIndexOf('\n'));
};
ko.observableArray.fn.knot = function(startDate, endDate){
    return ko.pureComputed(function(){

        Date.prototype.addDays = function(days) {
            this.setDate(this.getDate() + parseInt(days));
            return this;
        };

        var initial = this();
        var timeline = [];
        var endDatePoint = new Date(endDate).addDays(1);
        var startDatePoint = new Date(startDate);

        timeline.push({
            name: 'Начало периода',
            date: startDatePoint,
            radius: 2
        });
        for (var i=0; i < initial.length; i++){
            var item = initial[i];
            var date = item.dateTime.date().split(/[- :]/);
            date = new Date(Date.UTC(date[0], date[1]-1, date[2], date[3], date[4], date[5]));
            date = new Date(date);
            timeline.push({
                name: item.testName() + '<br/>Оценка: ' +
                (item.mark() ? item.mark(): 'отсутствует' + '<br/>'),
                date: date,
                id: item.id()
            });
        }
        timeline.push({
            name: 'Конец периода',
            date: endDatePoint,
            radius: 2
        });
        return timeline;
    }, this);
};