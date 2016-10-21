/**
 * Created by nyanjii on 19.10.16.
 */
$(document).ready(function(){
    var themeViewModel = function(){
        return new function(){
            var self = this;

            self.theme = ko.observable({

            });

            self.current = ko.observable({
                theme: ko.observable({
                    id: ko.observable(0),
                    name: ko.observable('')
                }),
                discipline: ko.observable({
                    id: ko.observable(0),
                    name: ko.observable('')
                }),
                questions: ko.observableArray([]),
                question: ko.observable({
                    id: ko.observable(0),
                    text: ko.observable(''),
                    time: ko.observable(0),
                    complexity: ko.observable(0),
                    type: ko.observable(0),
                    minutes: ko.observable(),
                    seconds: ko.observable()
                })
            });
            self.filter = ko.observable({
                name: ko.observable(''),
                type: ko.observable(''),
                complexity: ko.observable(''),
                types: ko.observableArray([
                    {id: ko.observable(1), name: ko.observable('Закрытый с одним правильным ответом')},
                    {id: ko.observable(2), name: ko.observable('Закрытый с несколькими правильными ответами')},
                    {id: ko.observable(3), name: ko.observable('Открытый однострочный')},
                    {id: ko.observable(4), name: ko.observable('Открытый многострочный')}
                    ]),
                complexityTypes: ko.observableArray([
                    {id: ko.observable(1), name: ko.observable('Лёгкий')},
                    {id: ko.observable(2), name: ko.observable('Средний')},
                    {id: ko.observable(3), name: ko.observable('Сложный')}
                ])
            });
            self.toggleCurrent = ko.observable({
                fill: function(data){
                    self.current().theme()
                        .id(data.id())
                        .name(data.name());
                },
                empty: function(){
                    self.current().discipline()
                        .id(0)
                        .name('')
                        .abbreviation('')
                        .description('');
                    self.current().profile().selected([]);
                },
                stringify: function(){
                    var edit = self.current().discipline();
                    var profiles = [];
                    var forpost = {
                        name: edit.name(),
                        abbreviation: edit.abbreviation(),
                        description: edit.description()
                    };
                    self.mode() === 'edit' ? forpost.id = edit.id() : null;
                    self.current().profile().selected().forEach(function(item){
                        profiles.push(item.id());
                    });
                    return JSON.stringify({discipline: forpost, profileIds: profiles});
                },
                set: ko.observable({
                    complexity: function(data){
                        var complexityId = data.complexity();
                        var complexity = '';
                        self.filter().complexityTypes().find(function(item){
                            if (item.id() === complexityId) {
                                complexity = item.name();
                                return;
                            }
                            return;
                        });
                        return complexity;
                    },
                    type: function(data){
                        var typeId = data.type();
                        var type = '';
                        self.filter().types().find(function(item){
                            if (item.id() === typeId) {
                                type = item.name();
                                return;
                            }
                            return;
                        });
                        return type;
                    }
                })
            });
            self.pagination = ko.observable({
                currentPage: ko.observable(1),
                pageSize: ko.observable(10),
                itemsCount: ko.observable(1),
                totalPages: ko.observable(1),

                selectPage: function(page){
                    self.pagination().currentPage(page);
                    self.get().questions();
                },
                dotsVisible: function(index){
                    var total = self.pagination().totalPages();
                    var current = self.pagination().currentPage();
                    if (total > 11 && index == total-1 && index > current + 2  ||total > 11 && index == current - 1 && index > 3)  {
                        return true;
                    }
                    return false;
                },
                pageNumberVisible: function(index){
                    var total = self.pagination().totalPages();
                    var current = self.pagination().currentPage();
                    if (total < 12 ||
                        index > (current - 2) && index < (current + 2) ||
                        index > total - 2 ||
                        index < 3) {
                        return true;
                    }
                    return false;
                },
            });
            self.mode = ko.observable('none');
            self.csed = ko.observable({
                theme: ko.observable({
                    edit: function(){
                        self.mode('theme.edit');
                    },
                    update: function(){},
                    cancel: function(){
                        self.mode('none');
                        self.toggleCurrent().fill(self.theme());
                    }
                }),
                startAdd: function(){
                    self.toggleCurrent().empty();
                    self.mode() === 'add' ? self.mode('none') : self.mode('add');
                },
                startUpdate: function(){
                    self.mode('edit');
                    self.toggleCurrent().setInitialProfiles();
                },
                startRemove: function(){
                    self.mode('delete');
                    self.toggleModal('#delete-modal', '');
                },
                update: function(){
                    var url = self.mode() === 'add' ? '/api/disciplines/create' : '/api/disciplines/update';
                    var json = self.toggleCurrent().stringify();
                    console.log(url + ' : ' + json);
                    self.post(url, json);
                },
                remove: function(){
                    self.toggleModal('#delete-modal', 'close');
                    var url = '/api/disciplines/delete/' + self.current().discipline().id();
                    self.post(url, '');
                },
                cancel: function(){
                    if (self.mode() === 'add'){
                        self.mode('none');
                        self.toggleCurrent().empty();
                        return;
                    }
                    self.mode('info');
                }
            });

            self.get = ko.observable({
                discipline: function(){
                    $.get('/api/disciplines/' + self.theme().discipline(), function(response){
                        var res = ko.mapping.fromJSON(response);
                        self.current().discipline().id(res.id()).name(res.name());
                    });
                },
                questions: function(){
                    var theme = 'theme=' + self.theme().id();
                    var page = '&page=' + self.pagination().currentPage();
                    var pageSize = '&pageSize=' + self.pagination().pageSize();
                    var name = '&name=' + self.filter().name();
                    var type = '&type=' + (self.filter().type() ? self.filter().type().id() : '');
                    var complexity = '&complexity=' + (self.filter().complexity() ? self.filter().complexity().id() : '');

                    var url = '/api/questions/show?' + theme +
                        page + pageSize +
                        name + type + complexity;

                    $.get(url, function(response){
                        var res = ko.mapping.fromJSON(response);
                        self.current().questions(res.data());
                        self.pagination().itemsCount(res.count());
                    });
                },
                theme: function(){
                    var url = window.location.href;
                    var themeId = +url.substr(url.lastIndexOf('/')+1);

                    $.get('/api/disciplines/themes/' + themeId, function(response){
                        self.theme(ko.mapping.fromJSON(response));
                        self.get().discipline();
                        self.get().questions();
                        self.toggleCurrent().fill(self.theme());
                    });
                }
            });
            self.get().theme();


            self.post = function(url, json){
                $.post(url, json, function(result){
                    self.mode('none');
                });
            };
            self.toggleModal = function(selector, action){
                $(selector).arcticmodal(action);
            };

            //SUBSCRIPTIONS
            self.pagination().itemsCount.subscribe(function(value){
                if (value){
                    self.pagination().totalPages(Math.ceil(
                        value/self.pagination().pageSize()
                    ));
                }
            });
            self.filter().type.subscribe(function(value){

            });
            self.filter().complexity.subscribe(function(value){

            });

            return {
                theme: self.theme,
                pagination: self.pagination,
                toggleCurrent: self.toggleCurrent,
                current: self.current,
                mode: self.mode,
                csed: self.csed,
                filter: self.filter,
                toggleModal: self.toggleModal
            };
        };
    };

    ko.applyBindings(themeViewModel());
});