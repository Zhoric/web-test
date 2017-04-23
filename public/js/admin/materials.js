$(document).ready(function(){
    var materialsViewModel = function(){
        return new function(){
            var self = this;

            initializeViewModel.call(self, {
                page: menu.admin.materials,
                mode: true,
                pagination: 10,
                multiselect: true
            });

            self.modals = {
                removeMedia: '#remove-media-modal'
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
                }),
                disciplineMedias: ko.observableArray([]),
                disciplineMedia: ko.observable({
                    id: ko.observable(0),
                    type: ko.observable(''),
                    content: ko.observable(''),
                    path: ko.observable(''),
                    name: ko.observable(''),
                    hash: ko.observable(''),
                    mediableId: ko.observable(0)
                }),
                disciplineMediables: ko.observableArray([]),
                disciplineMediable: ko.observable({
                    id: ko.observable(0),
                    media: ko.observable(0),
                    theme: ko.observable(0),
                    discipline: ko.observable(0),
                    start: ko.observable(''),
                    stop: ko.observable('')
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
                discipline: {
                    show: function(data){
                        if (self.mode() === state.none ||
                            self.current.discipline().id() !== data.id()){
                            self.mode(state.info);
                            self.alter.discipline.fill(data);
                            self.get.disciplineProfiles();
                            self.get.themes();
                            self.get.disciplineMedias(data.id());
                            return;
                        }
                        self.actions.discipline.cancel();
                    },
                    start: {
                        removeMedia: function (data) {
                            self.alter.media.fill(data);
                            console.log(self.current.disciplineMedia().mediableId());
                            commonHelper.modal.open(self.modals.removeMedia);
                        }
                    },
                    end: {
                        removeMedia: function () {
                            $ajaxpost({
                                url: '/api/mediable/delete/' + self.current.disciplineMedia().mediableId(),
                                data: null,
                                errors: self.errors,
                                successCallback: function(){
                                    self.get.disciplineMedias(self.current.discipline().id());
                                    commonHelper.modal.close(self.modals.removeMedia);
                                }
                            });
                        }
                    },
                    cancel: function(){
                        self.current.theme()
                            .id(0).name('')
                            .mode(state.none);
                        self.multiselect.tags([]);
                    },
                    addMedia: function (data) {
                        var elf = $('#elfinder').elfinder({
                            customData: {
                                _token: ''
                            },
                            url: 'http://' + window.location.host + '/elfinder/connector',
                            lang: 'ru',
                            resizable: false,
                            commands : [
                                'back', 'chmod', 'colwidth', 'copy', 'cut', 'download',
                                'edit', 'forward', 'fullscreen', 'getfile', 'help', 'home', 'info',
                                'mkdir', 'mkfile', 'netmount', 'netunmount', 'open', 'opendir', 'paste', 'places',
                                'quicklook', 'rename', 'resize', 'rm', 'search', 'sort', 'up', 'upload', 'view'
                            ],
                            commandsOptions: {
                                getfile: { multiple: false }
                            },
                            getFileCallback : function(file) {
                                self.media.add(file.hash, data.id(), null);
                            }
                        });

                        elf.dialog({
                            modal: true,
                            width : 1300,
                            resizable: true,
                            position: { my: "center top-70%", at: "center", of: window }
                        });

                        var elfinder = elf.elfinder('instance');
                        self.handlers.upload(elfinder);
                    }

                }

            };

            self.handlers = {
                upload: function (elfinder) {
                    elfinder.bind('upload', function(event) {
                        ko.utils.arrayForEach(event.data.added, function(file) {
                            var media = {
                                name: file.name,
                                type: file.mime.split('/')[0],
                                path: file.url,
                                hash: file.hash
                            };
                            var json = JSON.stringify({media: media});
                            $ajaxpost({
                                url: '/api/media/create',
                                error: self.errors,
                                data: json
                            });
                        });
                    });
                }
            };

            self.media = {
                add: function (hash, disciplineId, themeId) {
                    $ajaxget({
                        url: '/api/media/hash/' + hash,
                        errors: self.errors,
                        successCallback: function(retData){
                            var mediaId = retData()[0].id();
                            var mediable = {
                                start: null,
                                stop: null
                            };
                            var json = JSON.stringify({mediable: mediable, disciplineId: disciplineId, mediaId: mediaId, themeId: themeId});
                            $ajaxpost({
                                url: '/api/mediable/create',
                                error: self.errors,
                                data: json,
                                successCallback: function(){
                                    self.get.disciplineMedias(self.current.discipline().id());
                                }
                            });
                        }
                    });
                }
            };

            self.alter = {
                discipline: {
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
                    }
                },
                mediable: {
                    fill: function (data) {
                        self.current.disciplineMediable()
                            .id(data.id())
                            .media(data.media())
                            .theme(data.theme())
                            .discipline(data.discipline())
                            .start(data.start())
                            .stop(data.stop());
                    },
                    empty: function () {
                        self.current.disciplineMediable()
                            .id(0)
                            .media('')
                            .theme('')
                            .discipline('')
                            .start('')
                            .stop('');
                    }

                },
                media: {
                    fill: function (data) {
                        self.current.disciplineMedia()
                            .id(data.id())
                            .type(data.type())
                            .content(data.content())
                            .path(data.path())
                            .name(data.name())
                            .hash(data.hash())
                            .mediableId(data.mediableId());
                    },
                    empty: function () {
                        self.current.disciplineMedia()
                            .id(0)
                            .type('')
                            .content('')
                            .path('')
                            .name('')
                            .hash('')
                            .mediableId(0);
                    }
                }

            };


            self.get = {
                disciplines: function(profileId){
                    var filter = self.filter;
                    var profile = '';
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
                disciplineMedias: function (disciplineId) {
                    self.current.disciplineMedias.removeAll();
                    $ajaxget({
                        url: '/api/mediable/discipline/' + disciplineId,
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.disciplineMediables(data());
                            ko.utils.arrayForEach(self.current.disciplineMediables(), function (mediable) {
                                mediable.media.mediableId = mediable.id;
                               self.current.disciplineMedias.push(mediable.media);
                            });
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

    ko.applyBindings(materialsViewModel());
});