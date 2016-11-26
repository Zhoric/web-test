/**
 * Created by nyanjii on 11.10.16.
 */
$(document).ready(function(){
    var disciplinesViewModel = function(){
        return new function(){
            var self = this;

            self.errors = errors();
            self.pagination = pagination();

            self.disciplines = ko.observableArray([]);

            self.current = {
                discipline: ko.observable({
                    id: ko.observable(0),
                    name: ko.observable(''),
                    abbreviation: ko.observable(''),
                    description: ko.observable('')
                }),
                profiles: ko.observableArray([]),
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
                    commonHelper.modal.open('#delete-modal');
                },
                update: function(){
                    var url = self.mode() === 'add' ? '/api/disciplines/create' : '/api/disciplines/update';
                    var json = self.toggleCurrent.stringify();
                    self.post(url, json);
                },
                remove: function(){
                    commonHelper.modal.close('#delete-modal');
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

                    commonHelper.modal.open('#sections-modal');

                },
                theme: {
                    startAdd: function(){
                        self.current.theme().id(0).name('');
                        commonHelper.modal.open('#add-theme-modal');
                    },
                    add: function(){
                        var url = '/api/disciplines/themes/create';
                        var json = JSON.stringify({
                            theme: {
                                name: self.current.theme().name()
                            },
                            disciplineId: self.current.discipline().id()
                        });
                        $.post(url, json, function(){
                            commonHelper.modal.close('#add-theme-modal');
                            self.get.themes();
                        });
                    },
                    startRemove: function(data){
                        commonHelper.modal.open('#remove-theme-modal');
                        self.current.theme().id(data.id()).name(data.name());
                    },
                    remove: function(){
                        var url = '/api/disciplines/themes/delete/' + self.current.theme().id();
                        $.post(url, function(){
                            commonHelper.modal.close('#remove-theme-modal');
                            self.get.themes();
                        });
                    },
                    showSections : function(data) {
                        self.current.theme(data);
                        self.get.sectionsByTheme();
                        commonHelper.modal.open('#sections-modal');
                    },
                    addSection : function (data) {
                        window.location.href = '/admin/editor/new/' + self.current.discipline().id() + '/' + self.current.theme().id();
                    }
                },
                section: {
                    startRemove: function (data) {
                        commonHelper.modal.open('#remove-section-modal');
                        self.current.section(data);
                    },
                    remove: function () {
                        var url = '/api/sections/delete/' + self.current.section().id();
                        $.post(url, function(){
                            commonHelper.modal.close('#remove-section-modal');
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
                        if (result.Success()){
                            self.disciplines(result.Data.data());
                            self.pagination.itemsCount(result.Data.count());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                disciplineProfiles: function(){
                    var id = self.current.discipline().id();
                    if (id){
                        $.get('/api/disciplines/' + id + '/profiles', function(response){
                            var result = ko.mapping.fromJSON(response);
                            if (result.Success()){
                                self.current.profiles(result.Data());
                                self.multiselect.fill();
                                return;
                            }
                            self.errors.show(result.Message());
                        });
                    }
                },
                profiles: function(){
                    $.get('/api/profiles', function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            self.multiselect.data(result.Data());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                themes: function(){
                    var url = '/api/disciplines/' + self.current.discipline().id() +'/themes';
                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            self.current.themes(result.Data());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                sectionsByTheme: function() {
                    var url = '/api/sections/theme/' + self.current.theme().id() ;
                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            self.current.sections(result.Data());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                sectionsByDiscipline: function () {
                    var url = '/api/sections/discipline/' + self.current.discipline().id() ;
                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            self.current.sections(result.Data());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                }
            };
            self.get.disciplines();
            self.get.profiles();


            self.post = function(url, json){
                $.post(url, json, function(response){
                    var result = ko.mapping.fromJSON(response);
                    if (result.Success()){
                        self.mode('none');
                        self.toggleCurrent.empty();
                        self.get.disciplines();
                        return;
                    }
                    self.errors.show(result.Message());
                });
            };

            // SUBSCRIPTIONS
            self.pagination.itemsCount.subscribe(function(value){
                if (value){
                    self.pagination.totalPages(Math.ceil(
                        value/self.pagination.pageSize()
                    ));
                }
            });
            self.pagination.currentPage.subscribe(function(value){
                self.get.disciplines();
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
                errors: self.errors
            };
        };
    };

    ko.applyBindings(disciplinesViewModel());
});