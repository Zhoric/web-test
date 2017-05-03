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
                removeMedia: '#remove-media-modal',
                repeatAdd: '#repeat-add-modal',
                lastDelete: '#last-delete-modal',
                changeMedia: '#change-media-modal',
                haveMediables: '#have-mediables-modal'
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
                themeMediables: ko.observableArray([]),
                changeMode: ko.observable(false),
                elf: ko.observable()
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
                    cancel: function(){
                        self.mode(state.none);
                        self.alter.discipline.empty();
                    },
                    // вывод всех файлов дисциплины
                    overall: function (data) {
                        if (self.mode() != state.overall){
                            self.mode(state.overall);
                            self.get.disciplineMedias(data.id());
                        }
                    },
                    // вывод всех тем дисциплины
                    themes: function () {
                        if (self.mode() != state.themes){
                            self.mode(state.themes);
                            self.get.themes();
                        }
                    }
                },
                theme: {
                    cancel: function(){
                        self.current.theme()
                            .id(0);
                        self.multiselect.tags([]);
                    },
                    // вывод файлов темы
                    materials: function (data) {
                        if (self.current.theme().id() !== data.id()){
                            self.mode(state.materials);
                            self.current.theme().id(data.id());
                            self.get.themeMedias(data.id());
                            return;
                        }
                        self.actions.theme.cancel();
                    }
                },
                media: {
                    add: function () {
                        self.elfinder.open();
                    },
                    remove: function () {
                        $ajaxpost({
                            url: '/api/media/delete/' + self.current.media().id(),
                            data: null,
                            errors: self.errors,
                            successCallback: function(){
                                $ajaxpost({
                                    url: '/api/media/deletefile',
                                    data: JSON.stringify({path: self.current.media().path()}),
                                    errors: self.errors,
                                    successCallback: function () {
                                        commonHelper.modal.close(self.modals.lastDelete);
                                        $('#elfinder').elfinder('instance').exec('reload');
                                    }
                                });
                            }
                        });
                    },
                    move: function (data) {
                        if (data.type() == 'text') {
                            var newWindow = window.open();
                            newWindow.document.write(data.content());
                            return;
                        }
                        var index = data.path().indexOf(data.name());
                        var path = data.path().substring(0,index);
                        window.open(window.location.origin + '/' + encodeURI(path) + encodeURIComponent(data.name()));
                    },
                    start: {
                        change: function (data) {
                            self.alter.media.fill(data);
                            commonHelper.modal.open(self.modals.changeMedia);
                        },
                        remove: function (data) {
                            self.alter.media.fill(data);
                            commonHelper.modal.open(self.modals.removeMedia);
                        }
                    },
                    end: {
                        change: function () {
                            self.current.changeMode(true);
                            self.elfinder.open();
                        },
                        remove: function () {
                            $ajaxpost({
                                url: '/api/mediable/delete/' + self.current.media().mediableId(),
                                data: null,
                                errors: self.errors,
                                successCallback: function(){
                                    self.get.currentMedias();
                                    commonHelper.modal.close(self.modals.removeMedia);
                                    self.check.lastDelete(self.current.media().id());
                                }
                            });
                        }
                    }
                }
            };

            self.elfinder = {
                initialize: function () {
                    var elfOptions = {
                        customData: {
                            _token: ''
                        },
                        url: 'http://' + window.location.host + '/elfinder/connector',
                        lang: 'ru',
                        resizable: false,
                        commands : [
                            'getfile', 'back', 'chmod', 'colwidth', 'copy', 'cut',
                            'edit', 'forward',  'help', 'home', 'info', 'reload',
                            'mkdir', 'mkfile', 'netmount', 'netunmount', 'open', 'opendir', 'paste', 'places',
                            'quicklook', 'rename', 'resize', 'rm', 'search', 'sort', 'up', 'upload', 'view'
                        ],
                        uiOptions: {
                            toolbar: [
                                ['back', 'forward'],
                                ['reload'],
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
                                self.elfinder.getFile(file);
                            });
                            elf.dialog("close");
                        }
                    };
                    var elf = $('#elfinder').elfinder(elfOptions);
                    self.elfinder.handlers.upload(elf.elfinder('instance'));
                    self.elfinder.handlers.rename(elf.elfinder('instance'));
                    self.elfinder.handlers.open(elf.elfinder('instance'));
                    self.current.elf(elf);
                },
                open: function () {
                    self.current.elf().dialog({
                        modal: true,
                        width : 1300,
                        resizable: true,
                        position: { my: "center top-70%", at: "center", of: window }
                    });
                },
                getFile: function (file) {
                    if(self.current.changeMode()) {
                        self.media.change(file);
                        self.current.changeMode(false);
                        return;
                    }
                    self.media.add(file.hash, self.current.discipline().id(), self.current.theme().id());
                },
                handlers: {
                    upload: function (elfinder) {
                        elfinder.bind('upload', function(event) {
                            ko.utils.arrayForEach(event.data.added, function(file) {
                                var media = {
                                    name: file.name,
                                    path: file.url.substring(file.url.search('upload'),file.url.length),
                                    hash: file.hash
                                };
                                var type = file.mime.split('/')[0];
                                if (type == 'audio' || type == 'video' || type == 'image'){
                                    media.type = type;
                                    self.media.createSimple(media);
                                }
                                else if (type == 'application' || type == 'text') {
                                    media.type = 'text';
                                    self.media.createDocx(media);
                                }
                                else {
                                    media.type = 'text';
                                    self.media.createPdf(media);
                                }
                            });
                        });
                    },
                    open: function (elfinder) {
                        elfinder.bind('open', function (event) {
                            console.log(event);

                        });
                    },
                    rename: function (elfinder) {
                        elfinder.bind('rename', function (event) {
                            $ajaxget({
                                url: '/api/media/hash/' + event.data.removed[0], // получение переименованного файла
                                errors: self.errors,
                                successCallback: function(data){
                                    var media = data()[0];
                                    // новый путь к файлу - старый путь + новое название файла
                                    var index = media.path().indexOf(media.name());
                                    var newPath = media.path().substring(0, index) + event.data.added[0].name;
                                    var mediaJSON = {
                                        id: media.id(),
                                        type: media.type(),
                                        path: newPath,
                                        name: event.data.added[0].name,
                                        hash: event.data.added[0].hash
                                    };
                                    $ajaxpost({
                                        url: '/api/media/update',
                                        error: self.errors,
                                        data: JSON.stringify({media: mediaJSON}),
                                        successCallback: function(){
                                            self.get.currentMedias();
                                            $('#elfinder').elfinder('instance').exec('reload');
                                        }
                                    });
                                }
                            });
                        });
                    }
                }
            };

            self.media = {
                add : function (hash, disciplineId, themeId) {
                    $ajaxget({
                        url: '/api/media/hash/' + hash,
                        errors: self.errors,
                        successCallback: function(data){
                            var mediaId = data()[0].id();
                            //проверка повторного добавления данного файла к текущей теме или дисциплине
                            if((self.current.theme().id() != 0 && (self.check.repeatAdd(self.current.themeMediables(), mediaId) == true)) ||
                                (self.current.theme().id() == 0 && (self.check.repeatAdd(self.current.disciplineMediables(), mediaId) == true))) return;

                            $ajaxpost({
                                url: '/api/mediable/create',
                                error: self.errors,
                                data: JSON.stringify({
                                    mediable: {start: null, stop: null},
                                    disciplineId: disciplineId,
                                    mediaId: mediaId,
                                    themeId: themeId == 0 ? null : themeId}),
                                successCallback: function () {
                                    self.get.currentMedias();
                                }
                            });
                        }

                    });
                },
                createSimple: function (media) {
                    $ajaxpost({
                        url: '/api/media/create',
                        error: self.errors,
                        data: JSON.stringify({media: media})
                    });
                },
                createDocx: function (media) {
                    $ajaxpost({
                        url: '/api/media/createdocx',
                        error: self.errors,
                        data: JSON.stringify({media: media})
                    });
                },
                createPdf: function (media) {

                },
                change: function (file) {
                    $ajaxget({
                        url: '/api/media/hash/' + file.hash,
                        errors: self.errors,
                        successCallback: function(media){
                            var mediaId = media()[0].id();
                            $ajaxget({
                                url: '/api/mediable/media/' + mediaId,
                                error: self.errors,
                                successCallback: function (data) {
                                    if (data().length == 0){ // если заменяющий файл ни к чему не привязан
                                        self.media.update(file); // поменять заменяемый файл
                                        self.media.removeAfterUpdate(mediaId, self.current.media().path()); // удалить старый заменяемый файл
                                        self.get.currentMedias();
                                    }
                                    else {
                                        commonHelper.modal.open(self.modals.haveMediables);
                                    }
                                }
                            })
                        }
                    });
                },
                update: function (file) {
                    var mediaJSON = {
                        id: self.current.media().id(),
                        type: file.mime.split('/')[0],
                        path: decodeURIComponent(file.url.substring(file.url.search('upload'), file.url.length)),
                        name: file.name,
                        hash: file.hash
                    };
                    $ajaxpost({
                        url: '/api/media/update',
                        error: self.errors,
                        data: JSON.stringify({media: mediaJSON}),
                        successCallback: function(){
                            self.get.currentMedias();
                            $('#elfinder').elfinder('instance').exec('reload');
                        }
                    });
                },
                removeAfterUpdate: function (mediaId, path) {
                    // удаление старого файла из директории
                    $ajaxpost({
                        url: '/api/media/deletefile',
                        data: JSON.stringify({path: path}),
                        errors: self.errors
                    });
                    // удаление из БД нового файла
                    $ajaxpost({
                        url: '/api/media/delete/' + mediaId,
                        data: null,
                        errors: self.errors
                    });

                }
            };
            self.check = {
                // проверка повторного привязывания файла
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
                // проверка на последнее удаление файла
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
                },
                currentMedias: function () {
                    if(self.current.theme().id() == 0)
                        self.get.disciplineMedias(self.current.discipline().id());
                    else self.get.themeMedias(self.current.theme().id());
                }
            };
            self.get.disciplines();
            self.get.profiles();
            self.elfinder.initialize();

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