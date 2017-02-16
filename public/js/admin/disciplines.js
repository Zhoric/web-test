$(document).ready(function(){
    var disciplinesViewModel = function(){
        return new function(){
            var self = this;

            self.page = ko.observable(menu.admin.disciplines);
            self.validation = {};
            self.events = new validationEvents(self.validation);
            self.errors = errors();
            self.pagination = pagination();
            //self.mode = ko.observable(state.none);
            self.modals = {
                removeTheme: '#remove-theme-modal'
            };
            self.multiselect = new multiselect({
                dataTextField: 'fullname',
                dataValueField: 'id',
                valuePrimitive: true
            });

            self.disciplines = ko.observableArray([]);
            self.current = {
                discipline: ko.validatedObservable({
                    id: ko.observable(0),
                    name: ko.observable('').extend({
                        required: true,
                        maxLength: 200
                    }),
                    abbreviation: ko.observable('').extend({
                        required: true,
                        maxLength: 50
                    }),
                    description: ko.observable('')
                }),
                themes: ko.observableArray([]),
                theme: ko.observable({
                    id: ko.observable(0),
                    name: ko.observable(''),
                    mode: ko.observable(state.none)
                }),
                sections : ko.observableArray([]),
                section: ko.observable({
                    id: ko.observable(0),
                    themeId: ko.observable(0),
                    name: ko.observable(''),
                    content: ko.observable('')
                })
            };
            self.filter = {
                discipline: ko.observable(''),
                profile : ko.observable(),
                clear: function(){
                    self.filter.discipline('').profile(null);
                }
            };

            self.alter = {};
            self.actions = {
                theme: {
                    start: {
                        add: function(){
                            self.current.theme()
                                .id(0).name('')
                                .mode(state.create);
                        },
                        remove: function(data, e){
                            e.stopPropagation();
                            self.current.theme()
                                .id(data.id())
                                .name(data.name())
                                .mode(state.remove);
                            commonHelper.modal.open(self.modals.removeTheme);
                        }
                    },
                    end: {
                        add: function(){
                            if (!self.current.theme().name()) return;
                            self.post.theme();
                        },
                        remove: function(){
                            self.post.removal.theme();
                        }
                    },
                    cancel: function(){
                        self.current.theme()
                            .id(0).name('')
                            .mode(state.none);
                    },
                    move: function(data){
                        window.location.href = '/admin/theme/' + data.id();
                    }
                }
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
                    commonHelper.buildValidationList(self.validation);
                },
                startUpdate: function(){
                    self.mode('edit');
                    commonHelper.buildValidationList(self.validation);
                },
                startRemove: function(){
                    self.mode('delete');
                    commonHelper.modal.open('#delete-modal');
                },
                update: function(){
                    if (!self.current.discipline.isValid()){
                        self.validation[$('[accept-validation]').attr('id')].open();
                        return;
                    }
                    if (!self.multiselect.tags().length){
                        self.validation[$('[special]').attr('id')].open();
                        return;
                    }
                    self.post.discipline();
                },
                remove: function(){
                    commonHelper.modal.close('#delete-modal');
                    self.post.discipline();
                },
                cancel: function(){
                    if (self.mode() === 'add'){
                        self.mode('none');
                        self.toggleCurrent.empty();
                        return;
                    }
                    self.mode('info');
                },
                showSections: function (data, e) {
                    e.stopPropagation();
                    self.get.sectionsByDiscipline();

                    commonHelper.modal.open('#sections-modal');

                },
                theme: {
                    showSections : function(data, e) {
                        e.stopPropagation();
                        //self.current.theme(data);
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
                tests: function(data, e){
                    e.stopPropagation();
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

                    $ajaxget({
                        url: url,
                        errors: self.errors,
                        successCallback: function(data){
                            self.disciplines(data.data());
                            self.pagination.itemsCount(data.count());
                        }
                    });

                },
                disciplineProfiles: function(){
                    var id = self.current.discipline().id();
                    if (!id) return;
                    $ajaxget({
                        url: '/api/disciplines/' + id + '/profiles',
                        errors: self.errors,
                        successCallback: function(data){
                            var profiles = [];
                            $.each(data(), function(i, item){
                                profiles.push(+item.profile_id());
                            });
                            console.log(profiles);
                            self.multiselect.multipleSelect()(profiles);
                        }
                    });
                },
                profiles: function(){
                    $ajaxget({
                        url: '/api/profiles',
                        errors: self.errors,
                        successCallback: function(data){
                            self.multiselect.setDataSource(data());
                        }
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

            self.post = {
                discipline: function(){
                    var url = '/api/disciplines/';
                    url = self.mode() === 'delete' ? url + 'delete/' + self.current.discipline().id() : url;
                    url = self.mode() === 'add' ? url + 'create' : url;
                    url = self.mode() === 'edit' ? url + 'update' : url;
                    $ajaxpost({
                        url: url,
                        errors: self.errors,
                        data: self.mode() === 'delete' ? null : self.toggleCurrent.stringify(),
                        successCallback: function(){
                            self.mode('none');
                            self.toggleCurrent.empty();
                            self.get.disciplines();
                        }
                    });
                },
                theme: function(){
                    var json = JSON.stringify({
                        theme: {
                            name: self.current.theme().name()
                        },
                        disciplineId: self.current.discipline().id()
                    });

                    $ajaxpost({
                        url: '/api/disciplines/themes/create',
                        errors: self.errors,
                        data: json,
                        successCallback: function(){
                            self.actions.theme.cancel();
                            self.get.themes();
                        }
                    });
                },
                removal: {
                    theme: function(){
                        $ajaxpost({
                            url: '/api/disciplines/themes/delete/' + self.current.theme().id(),
                            data: null,
                            errors: self.errors,
                            successCallback: function(){
                                commonHelper.modal.close(self.modals.removeTheme);
                                self.actions.theme.cancel();
                                self.get.themes();
                            }
                        });
                    }
                }
            };

            // SUBSCRIPTIONS
            self.pagination.itemsCount.subscribe(function(value){
                if (value){
                    self.pagination.totalPages(Math.ceil(
                        value/self.pagination.pageSize()
                    ));
                }
            });
            self.pagination.currentPage.subscribe(function(){
                self.get.disciplines();
            });
            self.filter.discipline.subscribe(function(){
                self.pagination.currentPage(1);
                self.get.disciplines();
            });
            self.filter.profile.subscribe(function(){
                self.pagination.currentPage(1);
                self.get.disciplines();
            });

            return {
                page: self.page,
                disciplines: self.disciplines,
                pagination: self.pagination,
                multiselect: self.multiselect,
                current: self.current,
                moveTo: self.moveTo,
                mode: self.mode,
                actions: self.actions,
                csed: self.csed,
                filter: self.filter,
                errors: self.errors,
                events: self.events
            };
        };
    };

    ko.applyBindings(disciplinesViewModel());
});