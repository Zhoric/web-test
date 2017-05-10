$(document).ready(function(){
    var materialsViewModel = function(){
        return new function(){
            var self = this;

            initializeViewModel.call(self, {
                page: menu.admin.materials,
                mode: true,
                pagination: 5,
                multiselect: true
            });

            self.modals = {
                anchorMultimedia: '#anchor-multimedia-modal',
                multimedia: '#multimedia-modal'
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
                    mediableId: ko.observable(0),
                    start: ko.observable(null),
                    stop: ko.observable(null)
                }),
                disciplineMediables: ko.observableArray([]),
                themeMediables: ko.observableArray([]),
                changeMode: ko.observable(false),
                anchorMode: ko.observable(false),
                elf: ko.observable(),
                multimediaURL: ko.observable(''),
                anchor: ko.validatedObservable({
                    startHour: ko.observable('').extend({ digit: true }),
                    startMinute: ko.observable('').extend({ digit: true, max: 59 }),
                    startSecond: ko.observable('').extend({ digit: true, required: true, max: 59 }),
                    stopHour: ko.observable('').extend({ digit: true }),
                    stopMinute: ko.observable('').extend({ digit: true, max: 59 }),
                    stopSecond: ko.observable('').extend({ digit: true, required: true, max: 59 }),
                    startTime: ko.observable(''),
                    stopTime: ko.observable(''),
                    maxValue: ko.observable(0),
                    request: ko.observable('start'),
                    init: function() {
                        this.stopTime = this.stopTime.extend({min: this.startTime, max: this.maxValue});
                        return this;
                    }
                }.init())
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
                        self.elfinder.open("Добавление материала");
                    },
                    move: function (data) {
                        self.alter.media.fill(data);
                        var index = data.path().indexOf(data.name());
                        var path = data.path().substring(0,index);

                        if (data.type() == 'text') {
                            window.open('/admin/media/' + data.id());
                        }
                        else  if (data.type() == 'audio' || data.type() == 'video') {
                            self.multimedia.open(data);
                        }
                        else window.open(window.location.origin + '/' + encodeURI(path) + encodeURIComponent(data.name()));
                    },
                    anchor: function () {
                        self.current.anchorMode(true);
                        self.elfinder.open("Выделение отрывка");
                    },
                    change: function (data) {
                        self.alter.media.fill(data);
                        self.confirm.show({
                            message: 'Заменить данный материал во всех вхождениях (старая версия материала будет удалена)?',
                            approve: function(){
                                self.current.changeMode(true);
                                self.elfinder.open("Замена материала");
                            }
                        });
                    },
                    remove: function (data) {
                        console.log(data);
                        self.alter.media.fill(data);
                        self.confirm.show({
                            message: 'Вы уверены, что хотите удалить выбранный материал?',
                            approve: function(){
                                $ajaxpost({
                                    url: '/api/mediable/delete/' + self.current.media().mediableId(),
                                    data: null,
                                    errors: self.errors,
                                    successCallback: function(){
                                        self.get.currentMedias();
                                        self.check.lastDelete(self.current.media().id());
                                    }
                                });
                            }
                        });
                    },
                    editor: function (data) {
                        window.location.href = '/admin/editor/' + data.id();
                    }

                },
                anchor: {
                    add: function (data) {
                        self.current.anchor().stopTime(+self.current.anchor().stopSecond() +
                        +self.current.anchor().stopMinute() * 60 +
                        +self.current.anchor().stopHour() * 3600);

                        self.current.anchor().startTime(+self.current.anchor().startSecond() +
                            +self.current.anchor().startMinute() * 60 +
                            +self.current.anchor().startHour() * 3600);

                        if (self.current.anchor.isValid()){
                            self.anchor.create();
                            commonHelper.modal.close(self.modals.anchorMultimedia);
                        }
                        else self.validation['bAddAnchor'].open();
                    }
                }
            };

            self.multimedia = {
                open: function (data) {
                    var index = data.path().indexOf(data.name());
                    var path = data.path().substring(0,index);
                    var url = window.location.origin + '/' + encodeURI(path) + encodeURIComponent(data.name());
                    self.current.multimediaURL(url);
                    commonHelper.modal.open(self.modals.multimedia);
                    $('#multimedia')[0].load();
                },
                anchor: function () {
                    var multimedia = $('#multimediaAnchor')[0];
                    var currentTime = multimedia.currentTime;
                    var sec_num = parseInt(currentTime, 10);
                    var hours   = Math.floor(sec_num / 3600);
                    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
                    var seconds = sec_num - (hours * 3600) - (minutes * 60);

                    if (hours   < 10) hours   = "0" + hours;
                    if (minutes < 10) minutes = "0" + minutes;
                    if (seconds < 10) seconds = "0" + seconds;

                    if (self.current.anchor().request() == 'start'){
                        self.current.anchor().startTime(currentTime);
                        self.current.anchor().startHour(hours);
                        self.current.anchor().startMinute(minutes);
                        self.current.anchor().startSecond(seconds);
                    }
                    else {
                        self.current.anchor().stopTime(currentTime);
                        self.current.anchor().stopHour(hours);
                        self.current.anchor().stopMinute(minutes);
                        self.current.anchor().stopSecond(seconds);
                    }
                    self.current.anchor().maxValue(Math.floor(multimedia.duration));
                },
                loadeddata: function () {
                    if (self.current.media().start() == null) return;
                    var multimedia = $('#multimedia')[0];
                    multimedia.currentTime = self.toSeconds(self.current.media().start());
                },
                play: function () {
                    if (self.current.media().start() == null && self.current.media().stop() == null) return;
                    var multimedia = $('#multimedia')[0];
                    var stopTime = self.toSeconds(self.current.media().stop());
                    var startTime = self.toSeconds(self.current.media().start());
                    var currentTime = Math.floor(multimedia.currentTime);

                    if (currentTime >= stopTime){
                        multimedia.pause();
                        multimedia.currentTime = stopTime;
                    }
                    else if (currentTime < startTime) {
                        multimedia.pause();
                        multimedia.currentTime = startTime;
                    }
                }


            };

            self.anchor = {
                open: {
                    common: function (file) {
                        var type = file.mime.split('/')[0];
                        if (type == 'video' || type == 'audio')
                            self.anchor.open.multimedia(file);
                        else if (type == 'image')
                            self.errors.show('Нельзя выделить отрывок в изображении!');
                        else {
                            self.anchor.open.editor(file);
                        }

                    },
                    multimedia: function (file) {
                        $ajaxget({
                            url: '/api/media/hash/' + file.hash,
                            errors: self.errors,
                            successCallback: function(data){
                                var media = data()[0];
                                media.mediableId = ko.observable(null);
                                media.start = ko.observable(null);
                                media.stop = ko.observable(null);
                                self.alter.media.fill(media);
                                console.log(self.current.media());
                                var index = file.path.indexOf(file.name);
                                var path = file.path.substring(0,index);
                                var url = window.location.origin + '/' + encodeURI(path) + encodeURIComponent(file.name);
                                self.current.multimediaURL(url);
                                commonHelper.modal.open(self.modals.anchorMultimedia);
                                commonHelper.buildValidationList(self.validation);
                            }
                        });
                    },
                    editor: function (file) {
                        $ajaxget({
                            url: '/api/media/hash/' + file.hash,
                            errors: self.errors,
                            successCallback: function(data){
                                window.location.href = '/admin/editor/anchor/' + self.current.discipline().id() + '/'
                                    + self.current.theme().id() + '/' + data()[0].id();
                            }
                        });
                    }
                },
                create: function () {
                    $ajaxpost({
                        url: '/api/mediable/create',
                        error: self.errors,
                        data: JSON.stringify({
                            mediable: {start: self.current.anchor().startTime(), stop: self.current.anchor().stopTime()},
                            disciplineId: self.current.discipline().id(),
                            mediaId: self.current.media().id(),
                            themeId: self.current.theme().id() == 0 ? null : self.current.theme().id()}),
                        successCallback: function () {
                            self.get.currentMedias();
                        }
                    });
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
                        overwriteUploadConfirm : false,
                        commands : [
                            'getfile', 'back', 'chmod', 'colwidth', 'copy', 'cut',
                            'edit', 'forward',  'help', 'home', 'info', 'reload',
                            'mkdir', 'netmount', 'netunmount', 'open', 'opendir', 'paste', 'places',
                            'quicklook', 'rename', 'resize', 'rm', 'search', 'sort', 'up', 'upload', 'view'
                        ],
                        uiOptions: {
                            toolbar: [
                                ['back', 'forward'],
                                ['reload'],
                                ['mkdir', 'upload'],
                                ['open', 'opendir'],
                                ['copy', 'cut', 'paste'],
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
                    self.elfinder.handlers.change(elf.elfinder('instance'));
                    self.current.elf(elf);
                },
                open: function (name) {
                    self.current.elf().dialog({
                        modal: true,
                        width : 1300,
                        resizable: true,
                        position: { my: "center top-70%", at: "center", of: window },
                        title: name
                    });
                },
                getFile: function (file) {
                    if(self.current.changeMode()) {
                        self.media.change(file);
                        self.current.changeMode(false);
                        return;
                    }
                    else if (self.current.anchorMode()){
                        self.anchor.open.common(file);
                        self.current.anchorMode(false);
                        return;
                    }
                    self.media.add(file.hash, self.current.discipline().id(), self.current.theme().id());
                },
                handlers: {
                    upload: function (elfinder) {
                        elfinder.bind('upload', function(event) {
                            if (event.data.removed[0] == event.data.removed[1]) return;
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
                                else if (file.mime == 'application/pdf') {
                                    media.type = 'pdf';
                                    self.media.createPdf(media);
                                }
                                else {
                                    media.type = 'text';
                                    self.media.createDocx(media);
                                }

                            });
                        });
                    },
                    open: function (elfinder) {
                        elfinder.bind('open', function (event) {
                          //  console.log(event);

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
                                        hash: event.data.added[0].hash,
                                        content: media.content()
                                    };
                                    $ajaxpost({
                                        url: '/api/media/update',
                                        error: self.errors,
                                        data: JSON.stringify({media: mediaJSON}),
                                        successCallback: function(){
                                            self.get.currentMedias();
                                        }
                                    });
                                }
                            });
                        });
                    },
                    change: function (elfinder) {
                        elfinder.bind('change', function (event) {
                            if (event.data.removed[0] == event.data.removed[1]) {
                                $ajaxget({
                                    url: '/api/media/hash/' + event.data.added[0].hash,
                                    errors: self.errors,
                                    successCallback: function(media){
                                        var type = event.data.added[0].mime.split('/')[0];
                                        if (type != 'audio' && type != 'video' && type != 'image')
                                            type = 'text';

                                        var mediaJSON = {
                                            id: media()[0].id(),
                                            type: type,
                                            name: event.data.added[0].name,
                                            path: event.data.added[0].url.substring(event.data.added[0].url.search('upload'),event.data.added[0].url.length),
                                            content: null,
                                            hash: event.data.added[0].hash
                                        };
                                        $ajaxpost({
                                            url: '/api/media/update',
                                            error: self.errors,
                                            data: JSON.stringify({media: mediaJSON}),
                                            successCallback: function(){
                                                self.get.currentMedias();
                                            }
                                        });
                                    }
                                });
                            }
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
                                        self.errors.show('Данный материал уже к чему-то прикреплен!');
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

                },
            };
            self.check = {
                // проверка повторного привязывания файла
                repeatAdd : function (mediables, mediaId) {
                    var repeat = false;
                    ko.utils.arrayForEach(mediables, function (mediable) {
                        if (mediaId == mediable.media.id() && mediable.media.start() == null && mediable.media.stop() == null) {
                            self.errors.show('Прикрепление данного материала уже сделано!');
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
                                self.confirm.show({
                                    message: 'Данный материал больше ни к чему не прикреплен. Удалить его из файловой системы?',
                                    approve: function(){
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
                                                        $('#elfinder').elfinder('instance').exec('reload');
                                                    }
                                                });
                                            }
                                        });
                                    }
                                });
                        }
                    })
                }
            };

            self.toHHMMSS = function (time) {
                var sec_num = parseInt(time, 10);
                var hours   = Math.floor(sec_num / 3600);
                var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
                var seconds = sec_num - (hours * 3600) - (minutes * 60);

                if (hours   < 10) hours   = "0" + hours;
                if (minutes < 10) minutes = "0" + minutes;
                if (seconds < 10) seconds = "0" + seconds;

                return hours + ':' + minutes + ':' + seconds;
            };
            self.toSeconds = function (time) {
                if(+time == 0) return 0;
                var timeArray = time.split(':');
                return +timeArray[2] + +timeArray[1] * 60 + +timeArray[0] * 3600;
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
                        var start, stop;
                        +data.start() ? start = self.toHHMMSS(+data.start()) : start = data.start();
                        +data.stop() ? stop = self.toHHMMSS(+data.stop()) : stop = data.stop();

                        self.current.media()
                            .id(data.id())
                            .type(data.type())
                            .content(data.content())
                            .path(data.path())
                            .name(data.name())
                            .hash(data.hash())
                            .mediableId(data.mediableId())
                            .stop(stop)
                            .start(start);
                    },
                    empty: function () {
                        self.current.media()
                            .id(0)
                            .type('')
                            .content('')
                            .path('')
                            .name('')
                            .hash('')
                            .mediableId(0)
                            .stop(null)
                            .start(null);
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
                                +mediable.start() ?
                                    mediable.media.start = ko.observable(self.toHHMMSS(+mediable.start()))
                                    : mediable.media.start = ko.observable(mediable.start());
                                +mediable.stop() ?
                                    mediable.media.stop = ko.observable(self.toHHMMSS(+mediable.stop()))
                                    : mediable.media.stop = ko.observable(mediable.stop());
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
                                +mediable.start() ?
                                    mediable.media.start = ko.observable(self.toHHMMSS(+mediable.start()))
                                    : mediable.media.start = ko.observable(mediable.start());
                                +mediable.stop() ?
                                    mediable.media.stop = ko.observable(self.toHHMMSS(+mediable.stop()))
                                    : mediable.media.stop = ko.observable(mediable.stop());
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