ko.observable.fn.copy = function(data){
    this(ko.mapping.fromJS(ko.mapping.toJS(data)));
};
ko.observable.fn.cut = function(length){
    var dots = (this().length > length) ? ' ...' : '';
    return this().substr(0, length) + dots;
};
ko.observable.fn.parseDate = function(){
    var date = new Date(this());
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
ko.observable.fn.parseAnswer = function(){
    if (!this()) return;
    return this().replace(/<\/answer>/g, '\n\n')
        .slice(0, this().lastIndexOf('\n\n'));
};
ko.observableArray.fn.knot = function(startDate, endDate){
    return ko.pureComputed(function(){
        var initial = this();
        var timeline = [];
        var endDatePoint = new Date(endDate);
        Date.prototype.addDays = function(days) {
            this.setDate(this.getDate() + parseInt(days));
            return this;
        };
        var item = null;
        timeline.push({
            name: 'Начало периода',
            date: new Date(startDate),
            radius: 2
        });
        for (var i=0; i < initial.length; i++){
            item = initial[i];
            timeline.push({
                name: item.testName() + '<br/>Оценка: ' +
                (item.mark() ? item.mark(): 'отсутствует' + '<br/>'),
                date: new Date(item.dateTime.date())
            });
        }
        timeline.push({
            name: 'Конец периода',
            date: endDatePoint.addDays(1),
            radius: 2
        });
        return timeline;
    }, this);
};