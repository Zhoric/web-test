/**
 * Created by nyanjii on 11.10.16.
 */
$(document).ready(function(){
    var disciplinesViewModel = function(){
        return new function(){
            var self = this;

            self.disciplines = ko.observableArray([]);
            self.current = {
                discipline: ko.observable({
                    id: ko.observable(0),
                    name: ko.observable(''),
                    abbreviation: ko.observable(''),
                    description: ko.observable('')
                }),
                profiles: ko.observableArray([]),
                // profile: ko.observable({
                //     profiles: ko.observableArray([]),
                //     selected: ko.observableArray([]),
                //     discipline: ko.observableArray([])
                // }),
                themes: ko.observableArray([]),
                theme: ko.observable({
                    id: ko.observable(0),
                    name: ko.observable('')
                }),
                sections : ko.observableArray([]),
                section: ko.observable({
                    id: ko.observable(0),
                    themeId: ko.observable(0),
                    name: ko.observable(''),
                    content: ko.observable('')
                })
            };
            self.multiselect = {
                data: ko.observableArray([]),
                tags: ko.observableArray([]),
                show: function(data){
                    return data.fullname();
                },
                select: function(data){
                    var item = self.multiselect.tags().find(function(item){
                        return item.id() === data.id();
                    });
                    if (!item) self.multiselect.tags.push(data);
                    return '';
                },
                remove: function(data){
                    self.multiselect.tags.remove(data);
                },
                empty: function(){
                    self.multiselect.tags([]);
                },
                fill: function(){
                    var profiles = self.current.profiles;
                    self.multiselect.data().find(function(item){
                        var id = item.id();
                        profiles().find(function(profile){
                            if (profile.profile_id() == id){
                                self.multiselect.select(item);
                            }
                        });
                    });
                }
            };
            self.filter = {
                discipline: ko.observable(''),
                profile : ko.observable()
            };
            self.toggleCurrent = {
                fill: function(data){
                    self.current.discipline()
                        .id(data.id())
                        .name(data.name())
                        .abbreviation(data.abbreviation())
                        .description(data.description());
                },
                empty: function(){
                    self.current.discipline()
                        .id(0)
                        .name('')
                        .abbreviation('')
                        .description('');
                    self.current.profiles([]);
                    self.multiselect.empty();
                },
                stringify: function(){
                    var edit = self.current.discipline();
                    var profiles = [];
                    var forpost = {
                        name: edit.name(),
                        abbreviation: edit.abbreviation(),
                        description: edit.description()
                    };
                    self.mode() === 'edit' ? forpost.id = edit.id() : null;
                    self.multiselect.tags().find(function(item){
                        profiles.push(item.id());
                    });
                    return JSON.stringify({discipline: forpost, profileIds: profiles});
                }
            };
            self.pagination = {
                currentPage: ko.observable(1),
                pageSize: ko.observable(10),
                itemsCount: ko.observable(1),
                totalPages: ko.observable(1),

                selectPage: function(page){
                    self.pagination.currentPage(page);
                    self.get.disciplines();
                },
                dotsVisible: function(index){
                    var total = self.pagination.totalPages();
                    var current = self.pagination.currentPage();
                    if (total > 11 && index == total-1 && index > current + 2  ||total > 11 && index == current - 1 && index > 3)  {
                        return true;
                    }
                    return false;
                },
                pageNumberVisible: function(index){
                    var total = self.pagination.totalPages();
                    var current = self.pagination.currentPage();
                    if (total < 12 ||
                        index > (current - 2) && index < (current + 2) ||
                        index > total - 2 ||
                        index < 3) {
                        return true;
                    }
                    return false;
                },
            };
            self.mode = ko.observable('none');
            self.csed = {
                show: function(data){
                    if (self.mode() === 'none' || self.current.discipline().id() !== data.id()){
                        self.mode('info');
                        self.toggleCurrent.fill(data);
                        self.get.disciplineProfiles();
                        self.get.themes();
                        return;
                    }
                    self.mode('none');
                    self.toggleCurrent.empty();
                },
                startAdd: function(){
                    self.toggleCurrent.empty();
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
                    var json = self.toggleCurrent.stringify();
                    console.log(url + ' : ' + json);
                    self.post(url, json);
                },
                remove: function(){
                    self.toggleModal('#delete-modal', 'close');
                    var url = '/api/disciplines/delete/' + self.current.discipline().id();
                    self.post(url, '');
                },
                cancel: function(){
                    if (self.mode() === 'add'){
                        self.mode('none');
                        self.toggleCurrent.empty();
                        return;
                    }
                    self.mode('info');
                },
                showSections: function (data) {
                    self.current.discipline(data);
                    self.get.sectionsByDiscipline();
                    self.toggleModal('#sections-modal', '');

                },
                theme: {
                    startAdd: function(){
                        console.log('add theme');
                        self.current.theme().id(0).name('');
                        self.toggleModal('#add-theme-modal', '');
                    },
                    add: function(){
                        var url = '/api/disciplines/themes/create';
                        var json = JSON.stringify({
                            theme: {
                                name: self.current.theme().name()
                            },
                            disciplineId: self.current.discipline().id()
                        });
                        console.log(url + ' : ' + json);
                        $.post(url, json, function(){
                            self.toggleModal('#add-theme-modal', 'close');
                            self.get.themes();
                        });
                    },
                    startRemove: function(data){
                        self.toggleModal('#remove-theme-modal', '');
                        self.current.theme().id(data.id()).name(data.name());

                    },
                    remove: function(){
                        var url = '/api/disciplines/themes/delete/' + self.current.theme().id();
                        $.post(url, function(){
                            self.toggleModal('#remove-theme-modal', 'close');
                            self.get.themes();
                        });
                    },
                    showSections : function(data) {
                        self.current.theme(data);
                        self.get.sectionsByTheme();
                        self.toggleModal('#sections-modal', '');
                    },
                    addSection : function (data) {
                        window.location.href = '/admin/editor/new/' + self.current.discipline().id() + '/' + self.current.theme().id();
                    }
                },
                section: {
                    startRemove: function (data) {
                        self.toggleModal('#remove-section-modal', '');
                        self.current.section(data);
                    },
                    remove: function () {
                        var url = '/api/sections/delete/' + self.current.section().id();
                        $.post(url, function(){
                            self.toggleModal('#remove-section-modal', 'close');
                            self.get.sections();
                        });
                    },
                    edit: function (data) {
                        window.location.href = '/admin/editor/' + data.id();

                    },
                    info: function (data) {
                        window.location.href = '/section/' + data.id();
                    }
                    
                }
            };

            self.moveTo = {
                theme: function(data){
                    window.location.href = '/admin/theme/' + data.id();
                },
                tests: function(data){
                    window.location.href = '/admin/tests/' + data.id();
                }
            };
            self.get = {
                disciplines: function(profileId){
                    var filter = self.filter;
                    var profile = 'profile=' + (filter.profile() ? filter.profile().id() : '');
                    var name = 'name=' + filter.discipline();
                    var page = 'page=' + self.pagination.currentPage();
                    var pageSize = 'pageSize=' + self.pagination.pageSize();
                    var url = '/api/disciplines/show?' + page + '&' + pageSize + '&' + name + '&' + profile;

                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        self.disciplines(result.data());
                        self.pagination.itemsCount(result.count());
                    });
                },
                disciplineProfiles: function(){
                    var id = self.current.discipline().id();
                    console.log(id);
                    if (id){
                        $.get('/api/disciplines/' + id + '/profiles', function(response){
                            self.current.profiles(ko.mapping.fromJSON(response)());
                            console.log(self.current.profiles());
                            self.multiselect.fill();
                        });
                    }
                },
                profiles: function(){
                    $.get('/api/profiles', function(response){
                        self.multiselect.data(ko.mapping.fromJSON(response)());
                        console.log(self.multiselect.data());
                    });
                },
                themes: function(){
                    var url = '/api/disciplines/' + self.current.discipline().id() +'/themes';
                    $.get(url, function(response){
                        self.current.themes(ko.mapping.fromJSON(response)());
                    });
                },
                sectionsByTheme: function() {
                    var url = '/api/sections/theme/' + self.current.theme().id() ;
                    $.get(url, function(response){
                        self.current.sections(ko.mapping.fromJSON(response)());
                    });
                },
                sectionsByDiscipline: function () {
                    var url = '/api/sections/discipline/' + self.current.discipline().id() ;
                    $.get(url, function(response){
                        self.current.sections(ko.mapping.fromJSON(response)());
                    });
                }
            };
            self.get.disciplines();
            self.get.profiles();


            self.post = function(url, json){
                $.post(url, json, function(result){
                    self.mode('none');
                    self.toggleCurrent.empty();
                    self.get.disciplines();
                });
            };
            self.toggleModal = function(selector, action){
                $(selector).arcticmodal(action);
            };

            // SUBSCRIPTIONS
            self.pagination.itemsCount.subscribe(function(value){
                if (value){
                    self.pagination.totalPages(Math.ceil(
                        value/self.pagination.pageSize()
                    ));
                }
            });
            self.filter.discipline.subscribe(function(){
                self.get.disciplines();
            });
            self.filter.profile.subscribe(function(){
                self.get.disciplines();
            });

            return {
                disciplines: self.disciplines,
                pagination: self.pagination,
                multiselect: self.multiselect,
                current: self.current,
                moveTo: self.moveTo,
                mode: self.mode,
                csed: self.csed,
                filter: self.filter,
                toggleModal: self.toggleModal
            };
        };
    };

    ko.applyBindings(disciplinesViewModel());
});