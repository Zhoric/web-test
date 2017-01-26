// init = {
//     dataTextField: '',
//     dataValueField: '',
//     valuePrimitive: true/false,
//     ?data: [{}, {}],
//     ?tagIds: [id, id],
//     ?tagObjects : [{}, {}]
// };

var multiselect = function(init){
    var self = this;

    var _text = '';
    var _value = '';
    var _valuePrimitive = false;

    self.tags = ko.observableArray([]);
    self.data = ko.observableArray([]);

    self.setDataSource = function(data){
        self.data(data);
    };
    self.text = function(data){
        return data[_text]();
    };
    self.select = function(data){
        var item = self.tags().find(function(item){
            return item[_value]() === data[_value]();
        });
        if (!item) self.tags.push(data);

    };
    self.remove = function(data){
        self.tags.remove(data);
    };
    self.empty = function(){
        self.tags([]);
    };

    var getDataObject = {
        byObject: function(obj){
            var item =  self.data().find(function(item){
                return item[_value]() === obj[_value]();
            });
            return item;
        },
        byId: function(id){
            return self.data().find(function(item){
                return item[_value]() === id;
            });
        }
    };
    var fill = {
        light: function(ids){
            ids.find(function(id){
                var tag = getDataObject.byId(id);
                if (tag) self.select(tag);
            });
        },
        heavy: function(objs){
            objs.find(function(obj){
                var tag = getDataObject.byObject(obj);
                if(tag) self.select(tag);
            });
        }
    };

    self.multipleSelect = function(){
        return _valuePrimitive ?  fill.light : fill.heavy;
    };
    var initialize = function(){
        _text = init.dataTextField;
        _value = init.dataValueField;
        _valuePrimitive = init.valuePrimitive;

        if (init.tagIds) fill.light(init.tagIds);
        if (init.tagObjects) fill.heavy(init.tagObjects);
        if (init.data) self.data(init.data);
    };
    initialize();

    return {
        tags: self.tags,
        source: self.data,
        setDataSource: self.setDataSource,
        select: self.select,
        multipleSelect: self.multipleSelect,
        remove: self.remove,
        empty: self.empty,
        text: self.text
    }
};
