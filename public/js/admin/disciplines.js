$(document).ready(function(){
    var disciplinesViewModel = function(){
        return new function(){
            var self = this;

            initializeViewModel.call(self, {
                page: menu.admin.disciplines,
                mode: true,
                pagination: 10,
                multiselect: true
            });
            self.modals = {
                removeTheme: '#remove-theme-modal',
                removeDiscipline: '#remove-discipline-modal'
            };

            self.current = {
                disciplines: ko.observableArray([]),
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
                })
            };
            self.filter = {
                discipline: ko.observable(''),
                profile : ko.observable(),
                clear: function(){
                    self.filter.discipline('').profile(null);
                }
            };

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
                        self.multiselect.tags([]);
                    },
                    move: function(data){
                        window.location.href = '/admin/theme/' + data.id();
                    }
                },
                discipline: {
                    show: function(data){
                        if (self.mode() === state.none ||
                            self.current.discipline().id() !== data.id()){
                            self.mode(state.info);
                            self.alter.fill(data);
                            self.get.disciplineProfiles();
                            self.get.themes();
                            return;
                        }
                        self.actions.discipline.cancel();
                    },
                    start: {
                        add: function(){
                            self.mode() === state.create
                                ? self.mode(state.none)
                                : self.mode(state.create);
                            self.alter.empty();
                            self.multiselect.tags([]);
                            commonHelper.buildValidationList(self.validation);
                        },
                        update: function(){
                            self.mode(state.update);
                            commonHelper.buildValidationList(self.validation);
                        },
                        remove: function(){
                            self.mode(state.remove);
                            commonHelper.modal.open(self.modals.removeDiscipline);
                        }
                    },
                    end: {
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
                            commonHelper.modal.close(self.modals.removeDiscipline);
                            self.post.removal.discipline();
                        }
                    },
                    cancel: function(){
                        self.mode(state.none);
                        self.alter.empty();
                    },
                    move: function(data, e){
                        e.stopPropagation();
                        commonHelper.cookies.create({
                            testsDisciplineId: data.id()
                        });
                        window.location.href = '/admin/tests';
                    }
                }
            };

            self.alter = {
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
                },
                stringify: function(){
                    var edit = self.current.discipline();
                    var forpost = {
                        name: edit.name(),
                        abbreviation: edit.abbreviation(),
                        description: edit.description()
                    };
                    self.mode() === state.update ? forpost.id = edit.id() : null;

                    return JSON.stringify({
                        discipline: forpost,
                        profileIds: self.multiselect.tagIds.call(self)
                    });
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
                            self.current.disciplines(data.data());
                            self.pagination.itemsCount(data.count());
                        }
                    });

                },
                disciplineProfiles: function(){
                    var id = self.current.discipline().id();
                    if (!id) return;
                    self.multiselect.tags([]);
                    $ajaxget({
                        url: '/api/disciplines/' + id + '/profiles',
                        errors: self.errors,
                        successCallback: function(data){
                            $.each(self.multiselect.data(), function(i, profile){
                                $.each(data(), function(i, elem){
                                    if (elem.profile_id() == profile.id())
                                        self.multiselect.tags.push(profile);
                                });
                            });
                        }
                    });
                },
                profiles: function(){
                    $ajaxget({
                        url: '/api/profiles',
                        errors: self.errors,
                        successCallback: function(data){
                            self.multiselect.data(data());
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
                }
            };
            self.get.disciplines();
            self.get.profiles();

            self.post = {
                discipline: function(){
                    $ajaxpost({
                        url: '/api/disciplines/' + self.mode(),
                        errors: self.errors,
                        data: self.alter.stringify(),
                        successCallback: function(){
                            self.actions.discipline.cancel();
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
                    },
                    discipline: function(){
                        $ajaxpost({
                            url: '/api/disciplines/delete/' + self.current.discipline().id(),
                            errors: self.errors,
                            data: null,
                            successCallback: function(){
                                self.actions.discipline.cancel();
                                self.get.disciplines();
                            }
                        });
                    }
                }
            };

            self.events.theme = function(data, e){
                if (e.which === 13)
                    self.actions.theme.end.add();
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
                self.mode(state.none);
                self.get.disciplines();
            });
            self.filter.discipline.subscribe(function(){
                self.mode(state.none);
                self.pagination.currentPage(1);
                self.get.disciplines();
            });
            self.filter.profile.subscribe(function(){
                self.mode(state.none);
                self.pagination.currentPage(1);
                self.get.disciplines();
            });

            return returnStandart.call(self);
        };
    };

    ko.applyBindings(disciplinesViewModel());
});