var multiselectVM = function(params){
    var self = this;

    var _text = params.textField ? params.textField : "name";
    var _value = params.valueField ? params.valueField: "id";
    var _source = params.source;
    self.tags = params.tags;
    self.ddwidth = ko.observable('500px');
    self.query = ko.observable('');
    self.visible = ko.observable(false);
    self.data = ko.pureComputed(function(){
        var filtered = [];
        var query = new RegExp(self.query().toLowerCase());
        $.each(_source(), function(i, item){
            if (query.test(item[_text]().toLowerCase())
                && $.inArray(item, self.tags()) < 0){
                filtered.push(item);
            }
        });
        return filtered;
    });

    var refillTags = function(){
        var tags = [];
        if(!self.tags().length) return;
        $.each(_source(), function(i, sourceItem){
            $.each(self.tags(), function(j, tagItem){
                if (tagItem[_value]() === sourceItem[_value]()){
                    tags.push(sourceItem);
                }
            });
        });
        self.tags(tags);
    }();

    self.text = function(item){
        return item[_text]();
    };
    self.select = function(data){
        self.query('');
        self.hide();
        if ($.inArray(data, self.tags()) < 0){
            self.tags.push(data);
        }
    };
    self.remove = function(data){
        self.tags.remove(data);
        self.hide();
    };
    self.show = function(){
        self.visible(true);
    };
    self.hide = function(){
        self.visible(false);
    };
    self.leave = function(){
        setTimeout(self.hide, 100);
    };
    self.visible.subscribe(function(visible){
        if (!visible) return;
        self.ddwidth($('.knockout-multiselect').width());
    });
};

ko.components.register('multiselect', {
    viewModel: {
        createViewModel: function(params) {
            return new multiselectVM(params);
        }
    },
    template: '<div class="multiselect-wrap knockout-multiselect">' +
    '<!-- ko if: tags().length --> ' +
    '<div class="multiselect"> ' +
    '<ul data-bind="foreach: tags"> ' +
    '<li> ' +
    '<span data-bind="click: $parent.remove" class="fa">&#xf00d;</span> ' +
    '<span data-bind="text: $parent.text($data)"></span> ' +
    '</li> ' +
    '</ul> ' +
    '</div> ' +
    '<!-- /ko --> ' +
    '<input placeholder="Начните вводить"' +
    'data-bind="textInput: query,event: {focusin: show, focusout: leave},css: {full: tags().length}"/> ' +
    '</div> ' +
    '<!-- ko if: data().length -->' +
    '<div class="multiselect-list" data-bind="foreach: data, visible: visible, style: {width: ddwidth}">' +
    '<div class="exact-item" data-bind="text: $parent.text($data), click: $parent.select"></div>' +
    '</div>' +
    '<!-- /ko -->'
});