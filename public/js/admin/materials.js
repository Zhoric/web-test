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
                removeDisciplineMedia: '#remove-discipline-media-modal',
                removeThemeMedia: '#remove-theme-media-modal',
                repeatAdd: '#repeat-add-modal',
                lastDelete: '#last-delete-modal',
                changeDisciplineMedia: '#change-discipline-media-modal'
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
                medias: ko.observableArray([]),
                media: ko.observable({
                    id: ko.observable(0),
                    type: ko.observable(''),
                    content: ko.observable(''),
                    path: ko.observable(''),
                    name: ko.observable(''),
                    hash: ko.observable(''),
                    mediableId: ko.observable(0)
                }),
                disciplineMediables: ko.observableArray([]),
                themeMediables: ko.observableArray([])
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
                            self.mode(state.overall);
                            self.alter.discipline.fill(data);
                            self.get.disciplineProfiles();
                            self.get.disciplineMedias(data.id());
                            return;
                        }
                        self.actions.discipline.cancel();
                    },
                    overall: function (data) {
                        if (self.mode() != state.overall){
                            self.mode(state.overall);
                            self.get.disciplineMedias(data.id());
                        }
                    },
                    themes: function (data) {
                        if (self.mode() != state.themes){
                            self.mode(state.themes);
                            self.get.themes();
                        }
                    },
                    start: {
                        removeMedia: function (data) {
                            self.alter.media.fill(data);
                            commonHelper.modal.open(self.modals.removeDisciplineMedia);
                        },
                        changeMedia: function (data) {
                            self.alter.media.fill(data);
                            commonHelper.modal.open(self.modals.changeDisciplineMedia);
                        }
                    },
                    end: {
                        removeMedia: function () {
                            $ajaxpost({
                                url: '/api/mediable/delete/' + self.current.media().mediableId(),
                                data: null,
                                errors: self.errors,
                                successCallback: function(){
                                    self.get.disciplineMedias(self.current.discipline().id());
                                    commonHelper.modal.close(self.modals.removeDisciplineMedia);
                                    self.check.lastDelete(self.current.media().id());
                                }
                            });
                        },
                        changeMedia: function () {
                           // self.elfinder.openForChange(self.current.discipline().id(), null, self.current.media().hash());
                        }
                    },
                    cancel: function(){
                        self.mode(state.none);
                        self.alter.discipline.empty();
                    },
                    addMedia: function () {
                        self.elfinder.open();
                    }

                },
                theme: {
                    cancel: function(){
                        self.current.theme()
                            .id(0);
                        self.multiselect.tags([]);
                    },
                    materials: function (data) {
                        if (self.current.theme().id() !== data.id()){
                            self.mode(state.materials);
                            self.current.theme().id(data.id());
                            self.get.themeMedias(data.id());
                            return;
                        }
                        self.actions.theme.cancel();
                    },
                    addMedia: function () {
                        self.elfinder.open();
                    },
                    start: {
                        removeMedia: function (data) {
                            self.alter.media.fill(data);
                            commonHelper.modal.open(self.modals.removeThemeMedia);
                        }
                    },
                    end: {
                        removeMedia: function () {
                            $ajaxpost({
                                url: '/api/mediable/delete/' + self.current.media().mediableId(),
                                data: null,
                                errors: self.errors,
                                successCallback: function(){
                                    self.get.themeMedias(self.current.theme().id());
                                    commonHelper.modal.close(self.modals.removeThemeMedia);
                                    self.check.lastDelete(self.current.media().id());
                                }
                            });
                        }
                    }
                },
                media: {
                    remove: function () {
                        $ajaxpost({
                            url: '/api/media/delete/' + self.current.media().id(),
                            data: null,
                            errors: self.errors,
                            successCallback: function(){
                                commonHelper.modal.close(self.modals.lastDelete);
                            }
                        });
                    }
                }

            };

            self.elfinder = {
                open: function () {
                    var elf = $('#elfinder').elfinder({
                        customData: {
                            _token: ''
                        },
                        url: 'http://' + window.location.host + '/elfinder/connector',
                        lang: 'ru',
                        resizable: false,
                        commands : [
                            'getfile', 'back', 'chmod', 'colwidth', 'copy', 'cut', 'download',
                            'edit', 'forward', 'fullscreen', 'help', 'home', 'info',
                            'mkdir', 'mkfile', 'netmount', 'netunmount', 'open', 'opendir', 'paste', 'places',
                            'quicklook', 'rename', 'resize', 'rm', 'search', 'sort', 'up', 'upload', 'view'
                        ],
                        uiOptions: {
                             toolbar: [
                                 ['back', 'forward'],
                                 ['mkdir', 'mkfile', 'upload'],
                                 ['open', 'opendir'],
                                 ['copy', 'cut', 'paste']
                                 ['info'],
                                 ['quicklook'],
                                 ['rename', 'edit', 'resize'],
                                 ['search'],
                                 ['view'],
                                 ['help'],
                                 ['getfile']
                            ]
                        },
                        commandsOptions: {
                            getfile : {
                                onlyURL  : false, // send only URL or URL+path if false
                                multiple : true, // allow to return multiple files info
                                folders  : false, // allow to return folders info
                                oncomplete : 'close' // action after callback (close/destroy)
                            }
                        },
                        getFileCallback : function(files) {
                            ko.utils.arrayForEach(files, function (file) {
                                self.elfinder.getFile(file.hash);
                            });
                            elf.dialog("close");
                        }
                    });

                    elf.dialog({
                        modal: true,
                        width : 1300,
                        resizable: true,
                        position: { my: "center top-70%", at: "center", of: window }
                    });

                    var elfinder = elf.elfinder('instance');
                    self.elfinder.handlers.upload(elfinder);
                },
                getFile: function (hash) {
                    if (self.current.theme().id() == 0)
                        self.media.addToDiscipline(hash, self.current.discipline().id(), null);
                    else self.media.addToTheme(hash, self.current.discipline().id(), self.current.theme().id());
                },
                handlers: {
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
                }
            };

            self.media = {
                addToDiscipline: function (hash, disciplineId, themeId) {
                    $ajaxget({
                        url: '/api/media/hash/' + hash,
                        errors: self.errors,
                        successCallback: function(retData){
                            var mediaId = retData()[0].id();
                            if (self.check.repeatAdd(self.current.disciplineMediables(), mediaId) == true) return;
                            var json = JSON.stringify({mediable: {start: null, stop: null}, disciplineId: disciplineId, mediaId: mediaId, themeId: themeId});
                            $ajaxpost({
                                url: '/api/mediable/create',
                                error: self.errors,
                                data: json,
                                successCallback: function () {
                                    self.get.disciplineMedias(self.current.discipline().id());
                                }
                            });
                        }

                    });
                },
                addToTheme : function (hash, disciplineId, themeId) {
                    $ajaxget({
                        url: '/api/media/hash/' + hash,
                        errors: self.errors,
                        successCallback: function(retData){
                            var mediaId = retData()[0].id();
                            if (self.check.repeatAdd(self.current.themeMediables(), mediaId) == true) return;
                            var json = JSON.stringify({mediable: {start: null, stop: null}, disciplineId: disciplineId, mediaId: mediaId, themeId: themeId});
                            $ajaxpost({
                                url: '/api/mediable/create',
                                error: self.errors,
                                data: json,
                                successCallback: function(){
                                    self.get.themeMedias(self.current.theme().id());
                                }
                            });
                        }
                    });
                },
                change: function (file) {
                    var media = {
                        name: file.name,
                        type: file.mime.split('/')[0],
                        path: file.url,
                        hash: file.hash
                    };
                    var json = JSON.stringify({media: media});
                    $ajaxpost({
                        url: '/api/media/update',
                        error: self.errors,
                        data: json
                    });
                }
            };

            self.check = {
                repeatAdd : function (mediables, mediaId) {
                    var repeat = false;
                    ko.utils.arrayForEach(mediables, function (mediable) {
                        if (mediaId == mediable.media.id()) {
                            commonHelper.modal.open(self.modals.repeatAdd);
                            repeat = true;
                        }
                    });
                    return repeat;
                },
                lastDelete: function (mediaId) {
                    $ajaxget({
                        url: '/api/mediable/media/' + mediaId,
                        error: self.errors,
                        successCallback: function (data) {
                            if (data().length == 0)
                                commonHelper.modal.open(self.modals.lastDelete);
                        }
                    })
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
                media: {
                    fill: function (data) {
                        self.current.media()
                            .id(data.id())
                            .type(data.type())
                            .content(data.content())
                            .path(data.path())
                            .name(data.name())
                            .hash(data.hash())
                            .mediableId(data.mediableId());
                    },
                    empty: function () {
                        self.current.media()
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
                    $ajaxget({
                        url: '/api/mediable/discipline/' + disciplineId,
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.medias.removeAll();
                            self.current.disciplineMediables(data());
                            ko.utils.arrayForEach(self.current.disciplineMediables(), function (mediable) {
                                mediable.media.mediableId = mediable.id;
                               self.current.medias.push(mediable.media);
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
                },
                themeMedias: function (themeId) {
                    $ajaxget({
                        url: '/api/mediable/theme/' + themeId,
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.medias.removeAll();
                            self.current.themeMediables(data());
                            ko.utils.arrayForEach(self.current.themeMediables(), function (mediable) {
                                mediable.media.mediableId = mediable.id;
                                self.current.medias.push(mediable.media);
                            });
                        }
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