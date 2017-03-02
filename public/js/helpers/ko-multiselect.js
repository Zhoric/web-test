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
        var elem = null;
        $.each(self.tags(), function(i, item){
            item[_value]() === data[_value]()
                ? elem = item : null;
        });
        if (!elem) self.tags.push(data);
    };
    self.remove = function(data){
        self.tags.remove(data);
    };
    self.empty = function(){
        self.tags([]);
    };
    self.getTagsArray = function(){
        var arr = [];
        $.each(self.tags(), function(i, item){
            arr.push(item.id());
        });
        return arr;
    };

    var getDataObject = {
        byObject: function(obj){
            var elem = null;
            $.each(self.data(), function(i, item){
                item[_value]() === obj[_value]()
                    ? elem = item : null;
            });
            return elem;
        },
        byId: function(id){
            var elem = null;
            $.each(self.data(), function(i, item){
                item[_value]() === id
                    ? elem = item : null;
            });
            return elem;
        }
    };
    var fill = {
        light: function(ids){
            $.each(ids, function(i, id){
                var tag = getDataObject.byId(id);
                if (tag) self.select(tag);
            });
        },
        heavy: function(objs){
            $.each(objs, function(i, obj){
                var tag = getDataObject.byObject(obj);
                if(tag) self.select(tag);
            });
        }
    };

    self.multipleSelect = function(){
        self.empty();
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
        text: self.text,
        getTagsArray: self.getTagsArray
    }
};
