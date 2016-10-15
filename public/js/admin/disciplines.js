/**
 * Created by nyanjii on 11.10.16.
 */
$(document).ready(function(){
    var disciplinesViewModel = function(){
        return new function(){
            var self = this;

            self.disciplines = ko.observableArray([]);
            self.current = ko.observable({
                discipline: ko.observable({
                    id: ko.observable(0),
                    name: ko.observable(''),
                    abbreviation: ko.observable(''),
                    description: ko.observable('')
                }),
                profiles: ko.observableArray([]),
                selectedProfiles: ko.observableArray([]),
                themes: ko.observableArray([])
            });
            self.toggleCurrent = ko.observable({
                fill: function(data){
                    self.current().discipline()
                        .id(data.id())
                        .name(data.name())
                        .abbreviation(data.abbreviation())
                        .description(data.description());
                },
                empty: function(){
                    self.current().discipline()
                        .id(0)
                        .name('')
                        .abbreviation('')
                        .description('');
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
                    self.current().selectedProfiles().forEach(function(item){
                        profiles.push(item.id());
                    });
                    return JSON.stringify({discipline: forpost, profileIds: profiles});
                }
            });
            self.pagination = ko.observable({
                currentPage: ko.observable(1),
                pageSize: ko.observable(10),
                itemsCount: ko.observable(1),
                totalPages: ko.observable(1),

                selectPage: function(page){
                    self.pagination().currentPage(page);
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
                show: function(data){
                    if (self.mode() === 'none' || self.current().discipline().id() !== data.id()){
                        self.mode('info');
                        self.toggleCurrent().fill(data);
                        return;
                    }
                    self.mode('none');
                    self.toggleCurrent().empty();
                },
                startAdd: function(){
                    self.toggleCurrent().empty();
                    self.mode() === 'add' ? self.mode('none') : self.mode('add');
                },
                startUpdate: function(){
                    self.mode('edit');
                },
                startRemove: function(){
                    self.mode('delete');
                    self.toggleModal('#delete-modal', '');
                },
                update: function(){
                    var url = self.mode() === 'add' ? '/api/disciplines/create' : '/api/disciplines/update';
                    var json = self.toggleCurrent().stringify();
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
                },
            });

            self.get = ko.observable({
                disciplines: function(){
                    var page = 'page=' + self.pagination().currentPage();
                    var pageSize = 'pageSize=' + self.pagination().pageSize();
                    var url = '/api/disciplines/show?' + page + '&' + pageSize;
                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        self.disciplines(result.data());
                        self.pagination().itemsCount(result.count());
                    });
                },
                profiles: function(){
                    $.get('/api/profiles', function(response){
                        self.current().profiles(ko.mapping.fromJSON(response)());
                    });
                },
                themes: function(){}
            });
            self.get().disciplines();
            self.get().profiles();


            self.post = function(url, json){
                $.post(url, json, function(result){
                    self.mode('none');
                    self.toggleCurrent().empty();
                    self.get().disciplines();
                });
            };
            self.toggleModal = function(selector, action){
                $(selector).arcticmodal(action);
            };

            // SUBSCRIPTIONS
            self.pagination().itemsCount.subscribe(function(value){
                if (value){
                    self.pagination().totalPages(Math.ceil(
                        value/self.pagination().pageSize()
                    ));
                }
            });

            return {
                disciplines: self.disciplines,
                pagination: self.pagination,
                current: self.current,
                mode: self.mode,
                csed: self.csed
            };
        };
    };

    ko.applyBindings(disciplinesViewModel());
});