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
                delete: '#delete-modal',
                anchorMultimedia: '#anchor-multimedia-modal',
                multimedia: '#multimedia-modal',
                textAnchors: '#text-anchors-modal'
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
                anchor: ko.validatedObservable({
                    hourStart: ko.observable('').extend({digit: true}),
                    hourStop: ko.observable('').extend({digit: true}),
                    minuteStart: ko.observable('').extend({digit: true, max: 59}),
                    minuteStop: ko.observable('').extend({digit: true, max: 59}),
                    secondStart: ko.observable('').extend({ digit: true, required: true, max: 59 }),
                    secondStop: ko.observable('').extend({ digit: true, required: true, max: 59 }),
                    timeStart: ko.observable(''),
                    timeStop: ko.observable(0),
                    maxTime: ko.observable(0),
                    request: ko.observable('start'),
                    init: function() {
                        this.timeStop = this.timeStop.extend({min: this.timeStart, max: this.maxTime});
                        return this;
                    }
                }.init()),
                textAnchors: ko.observableArray([]),
                multimediaURL: ko.observable(''),
                mode: ko.observable('')
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
                            self.current.theme().id(0);
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
                    open: {
                        add: function () {
                            self.current.mode('');
                            self.elfinder.open("Добавление материала");
                        },
                        anchor: function () {
                            self.current.mode('anchor');
                            self.elfinder.open("Выделение отрывка");
                        },
                        replacement: function (data) {
                            self.alter.media.fill(data);
                            self.confirm.show({
                                message: 'Заменить данный материал во всех вхождениях (старая версия материала, а также все отрывки будут удалены)?',
                                approve: function(){
                                    self.current.mode('replace');
                                    self.elfinder.open("Замена материала");
                                }
                            });
                        },
                        editor: function (data) {
                            window.location.href = '/admin/editor/' + data.id();
                        }
                    },
                    move: function (data) {
                        self.alter.media.fill(data);
                        if (data.type() == 'text') {
                            if (data.start() != null) window.open('/admin/media/' + data.id() + '#' + data.start());
                            else window.open('/admin/media/' + data.id());
                        }
                        else  if (data.type() == 'audio' || data.type() == 'video') {
                            self.actions.multimedia.open(data);
                        }
                        else window.open(self.helpers.getEncodedUrl(data));
                    },
                    remove: function (data) { //удаление связи
                        self.alter.media.fill(data);
                        self.confirm.show({
                            message: 'Вы уверены, что хотите удалить выбранный материал?',
                            approve: function(){
                                self.post.remove.mediable();
                            }
                        });
                    },
                    removeFromSystem: function () { //удаление файла из ФС и БД
                        $ajaxpost({
                            url: '/api/media/delete/' + self.current.media().id(),
                            data: null,
                            errors: self.errors,
                            successCallback: function(){
                                self.post.remove.file();
                            }
                        });
                    },
                    replacement: function (file) { //замена файла
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
                                            self.post.update.media(file); // поменять заменяемый файл
                                            self.post.remove.cleanAfterUpdate(mediaId, self.current.media().path()); // удалить старый заменяемый файл
                                            self.actions.media.removeAnchorMediables();
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
                    removeAnchorMediables: function () { //удаление связей с якорями
                        $ajaxget({
                            url: '/api/mediable/media/' + self.current.media().id(),
                            error: self.errors,
                            successCallback: function (data) {
                                if (data().length == 0) return;

                                ko.utils.arrayForEach(data(), function (mediable) {
                                    if (mediable.start() != null) self.post.remove.mediableById(mediable.id());
                                })
                            }
                        })
                    },
                    createMediable: function (hash) { //создание связи при выборе файла в elfinder
                        $ajaxget({
                            url: '/api/media/hash/' + hash,
                            errors: self.errors,
                            successCallback: function(data){
                                var mediaId = data()[0].id();

                                //проверка повторного добавления данного файла к текущей теме или дисциплине
                                if((self.current.theme().id() != 0 && (self.check.repeatAdd(self.current.themeMediables(), mediaId) == true)) ||
                                    (self.current.theme().id() == 0 && (self.check.repeatAdd(self.current.disciplineMediables(), mediaId) == true))) return;
                                self.post.create.mediable(mediaId, null, null);
                            }
                        });
                    }
                },
                anchor: {
                    create: {
                        multimedia: function () {
                            //проверка валидности якоря и вызов функции его создания
                            //перевод из ЧЧ:ММ:СС в секунды
                            self.current.anchor().timeStop(+self.current.anchor().secondStop() +
                                +self.current.anchor().minuteStop() * 60 +
                                +self.current.anchor().hourStop() * 3600);

                            self.current.anchor().timeStart(+self.current.anchor().secondStart() +
                                +self.current.anchor().minuteStart() * 60 +
                                +self.current.anchor().hourStart() * 3600);

                            if (self.current.anchor.isValid()){
                                self.post.create.mediable(self.current.media().id(), self.current.anchor().timeStart(), self.current.anchor().timeStop());
                                commonHelper.modal.close(self.modals.anchorMultimedia);
                            }
                            else self.validation['bAddAnchor'].open();
                        },
                        disciplineTextAnchor: function (mediable) {
                            //проверка существования связи с выбранным якорем;
                            // её создание при отсутствии связи

                            var isExist = false;
                            var discipline = 'discipline=' + self.current.discipline().id();
                            var media = 'media=' + mediable.media.id();

                            $ajaxget({
                                url: '/api/mediable/disciplineMedia?' + discipline + '&' + media,
                                error: self.errors,
                                successCallback: function (data) {
                                    ko.utils.arrayForEach(data(), function (elem) {
                                        if (elem.start() == mediable.start() && elem.media.id() == mediable.media.id())
                                            isExist = true;
                                    });
                                    if (!isExist) self.post.create.mediable(mediable.media.id(), mediable.start(), mediable.stop());
                                }
                            });
                        },
                        themeTextAnchor: function (mediable) {
                            //проверка существования связи с выбранным якорем;
                            // её создание при отсутствии связи

                            var isExist = false;
                            var theme = 'theme=' + self.current.theme().id();
                            var media = 'media=' + mediable.media.id();

                            $ajaxget({
                                url: '/api/mediable/themeMedia?' + theme + '&' + media,
                                error: self.errors,
                                successCallback: function (data) {
                                    ko.utils.arrayForEach(data(), function (elem) {
                                        if (elem.start() == mediable.start() && elem.media.id() == mediable.media.id())
                                            isExist = true;
                                    });
                                    if (!isExist) self.post.create.mediable(mediable.media.id(), mediable.start(), mediable.stop());
                                }
                            });
                        }

                    },
                    open: {
                        common: function (file) {
                            var type = file.mime.split('/')[0];
                            if (type == 'video' || type == 'audio')
                                self.actions.anchor.open.multimedia(file);
                            else if (file.mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
                                self.actions.anchor.open.editor(file);
                            else {
                                self.errors.show('В данном файле нельзя выделить отрывок!');
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

                                    var index = file.path.indexOf(file.name);
                                    var path = file.path.substring(0,index);
                                    var url = window.location.origin + '/' + encodeURI(path) + encodeURIComponent(file.name);
                                    self.current.multimediaURL(url);
                                    commonHelper.modal.open(self.modals.anchorMultimedia);
                                    commonHelper.buildValidationList(self.validation);

                                    $('#multimediaAnchor')[0].load();
                                }
                            });
                        },
                        editor: function (file) {
                            $ajaxget({
                                url: '/api/media/hash/' + file.hash,
                                errors: self.errors,
                                successCallback: function(data){
                                    window.location.href = '/admin/editor/anchor/' + data()[0].id();
                                }
                            });
                        }
                    },
                    remove: {
                        disciplineTextAnchor: function (mediable) {
                            var existedMediableId = 0;
                            var discipline = 'discipline=' + self.current.discipline().id();
                            var media = 'media=' + mediable.media.id();

                            $ajaxget({
                                url: '/api/mediable/disciplineMedia?' + discipline + '&' + media,
                                error: self.errors,
                                successCallback: function (data) {
                                    ko.utils.arrayForEach(data(), function (elem) {
                                        if (elem.start() == mediable.start() && elem.media.id() == mediable.media.id()){
                                            existedMediableId = elem.id();
                                        }
                                    });
                                    if (existedMediableId != 0) self.post.remove.mediableById(existedMediableId);
                                }
                            });
                        },
                        themeTextAnchor: function (mediable) {
                            var existedMediableId = 0;
                            var theme = 'theme=' + self.current.theme().id();
                            var media = 'media=' + mediable.media.id();

                            $ajaxget({
                                url: '/api/mediable/themeMedia?' + theme + '&' + media,
                                error: self.errors,
                                successCallback: function (data) {
                                    ko.utils.arrayForEach(data(), function (elem) {
                                        if (elem.start() == mediable.start() && elem.media.id() == mediable.media.id()){
                                            existedMediableId = elem.id();
                                        }
                                    });
                                    if (existedMediableId != 0) self.post.remove.mediableById(existedMediableId);
                                }
                            });
                        }
                    },
                    show: function (data) {
                        $ajaxget({
                            url: '/api/mediable/media/' + data.id(),
                            error: self.errors,
                            successCallback: function (mediables) {
                                self.current.textAnchors.removeAll();
                                ko.utils.arrayForEach(mediables(), function (mediable) {
                                    var isInArray = false;
                                    if (mediable.start() != null) {
                                        if ((self.current.theme().id() != 0 && typeof mediable.theme == 'object' && self.current.theme().id() == mediable.theme.id()) ||
                                            (self.current.theme().id() == 0 && typeof mediable.discipline == 'object' && self.current.discipline().id() == mediable.discipline.id())){
                                            mediable.isChecked = ko.observable(true);
                                            ko.utils.arrayForEach(self.current.textAnchors(), function (txtAnc) {
                                                if (txtAnc.start() == mediable.start()) {
                                                    txtAnc.isChecked(true);
                                                    isInArray = true;
                                                }
                                            })
                                        }
                                        else if (typeof mediable.discipline != 'object' && mediable.discipline() == null &&
                                            typeof mediable.theme != 'object' && mediable.theme() == null) {
                                            mediable.isChecked = ko.observable(false);
                                        }
                                        else isInArray = true;
                                        if (!isInArray) self.current.textAnchors.push(mediable);
                                    }
                                });
                                commonHelper.modal.open(self.modals.textAnchors);
                            }
                        })
                    },
                    attachTextAnchor: function () {
                        //прикрепление связи с якорем текста
                        ko.utils.arrayForEach(self.current.textAnchors(), function (textAnchor) {
                            if (textAnchor.isChecked() == true){
                                if (self.current.theme().id() != 0)
                                    self.actions.anchor.create.themeTextAnchor(textAnchor);
                                else self.actions.anchor.create.disciplineTextAnchor(textAnchor);
                            }
                            else {
                                if (self.current.theme().id() != 0)
                                    self.actions.anchor.remove.themeTextAnchor(textAnchor);
                                else self.actions.anchor.remove.disciplineTextAnchor(textAnchor);
                            }
                        })
                    }
                },
                multimedia : {
                    open: function (data) {
                        //открытие модального окна с аудио/видео; загрузка аудио/видео
                        self.current.multimediaURL(self.helpers.getEncodedUrl(data));
                        commonHelper.modal.open(self.modals.multimedia);
                        $('#multimedia')[0].load();
                    },
                    anchorUpdate: function () {
                        //обновление значения якоря при передвижении ползунка
                        var multimedia = $('#multimediaAnchor')[0];
                        var currentTime = multimedia.currentTime;

                        self.current.anchor().maxTime(Math.floor(multimedia.duration));
                        self.helpers.convertAnchorTime(currentTime);
                    },
                    loadeddata: function () {
                        //проверка наличия якорей у аудио/видео при загрузке;
                        //если есть, то перевести ползунок в начальное время
                        if (self.current.media().start() == null && self.current.media().stop() == null) return;

                        var multimedia = $('#multimedia')[0];
                        multimedia.currentTime = self.helpers.toSeconds(self.current.media().start());
                    },
                    play: function () {
                        //проверка выхода за границы якорей ползунка при проигрывании аудио/видео
                        if (self.current.media().start() == null && self.current.media().stop() == null) return;

                        var multimedia = $('#multimedia')[0];
                        var stopTime = self.helpers.toSeconds(self.current.media().stop());
                        var startTime = self.helpers.toSeconds(self.current.media().start());
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
                }
            };

            self.post = {
                create: {
                    simpleMedia: function (media) {
                        $ajaxpost({
                            url: '/api/media/create',
                            error: self.errors,
                            data: JSON.stringify({media: media})
                        });
                    },
                    docxMedia: function (media) {
                        $ajaxpost({
                            url: '/api/media/createdocx',
                            error: self.errors,
                            data: JSON.stringify({media: media})
                        });
                    },
                    mediable: function (mediaId, start, stop) {
                        $ajaxpost({
                            url: '/api/mediable/create',
                            error: self.errors,
                            data: JSON.stringify({
                                mediable: {start: start, stop: stop},
                                discipline: self.current.discipline().id(),
                                mediaId: mediaId,
                                themeId: self.current.theme().id() == 0 ? null : self.current.theme().id()
                            }),
                            successCallback: function () {
                                self.get.currentMedias();
                            }
                        });
                    }
                },
                remove: {
                    mediable: function () {
                        $ajaxpost({
                            url: '/api/mediable/delete/' + self.current.media().mediableId(),
                            data: null,
                            errors: self.errors,
                            successCallback: function(){
                                self.get.currentMedias();
                                self.check.lastDelete(self.current.media().id());
                            }
                        });
                    },
                    mediableById: function (id) {
                        $ajaxpost({
                            url: '/api/mediable/delete/' + id,
                            data: null,
                            errors: self.errors,
                            successCallback: function () {
                                self.get.currentMedias();
                            }
                        });
                    },
                    file: function () {
                        $ajaxpost({
                            url: '/api/media/deletefile',
                            data: JSON.stringify({path: self.current.media().path()}),
                            errors: self.errors,
                            successCallback: function () {
                                commonHelper.modal.close(self.modals.delete);
                                $('#elfinder').elfinder('instance').exec('reload');
                            }
                        });
                    },
                    cleanAfterUpdate: function (mediaId, path) {
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
                },
                update: {
                    media: function (file) {
                        var mime = file.mime.split('/')[0];
                        var type;
                        if (mime == 'audio' || mime == 'video' || mime == 'image') type = mime;
                        else if (file.mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') type = "text";
                        else type = "other";

                        $ajaxpost({
                            url: '/api/media/update',
                            error: self.errors,
                            data: JSON.stringify({
                                media: {
                                    id: self.current.media().id(),
                                    type: type,
                                    path: decodeURIComponent(file.url.substring(file.url.search('upload'), file.url.length)),
                                    name: file.name,
                                    hash: file.hash
                                }
                            }),
                            successCallback: function(){
                                self.get.currentMedias();
                                $('#elfinder').elfinder('instance').exec('reload');
                            }
                        });
                    }
                }
            };

            self.check = {
                repeatAdd : function (mediables, mediaId) {
                    // проверка повторного привязывания файла
                    var repeat = false;
                    ko.utils.arrayForEach(mediables, function (mediable) {
                        if (mediaId == mediable.media.id() && mediable.media.start() == null && mediable.media.stop() == null) {
                            self.errors.show('Прикрепление данного материала уже сделано!');
                            repeat = true;
                        }
                    });
                    return repeat;
                },
                lastDelete: function (mediaId) {
                    // проверка на последнее удаление файла
                    $ajaxget({
                        url: '/api/mediable/media/' + mediaId,
                        error: self.errors,
                        successCallback: function (data) {
                            if (data().length == 0) commonHelper.modal.open(self.modals.delete);
                        }
                    })
                }
            };
            self.helpers = {
                toHHMMSS : function (time) {
                    return self.helpers.toHH(time) + ':' + self.helpers.toMM(time) + ':' + self.helpers.toSS(time);
                },
                toHH : function (time) {
                    var sec_num = parseInt(time, 10);
                    var hours = Math.floor(sec_num / 3600);
                    if (hours < 10) hours = "0" + hours;
                    return hours;
                },
                toMM : function (time) {
                    var sec_num = parseInt(time, 10);
                    var hours   = Math.floor(sec_num / 3600);
                    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
                    if (minutes < 10) minutes = "0" + minutes;
                    return minutes;
                },
                toSS : function (time) {
                    var sec_num = parseInt(time, 10);
                    var hours   = Math.floor(sec_num / 3600);
                    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
                    var seconds = sec_num - (hours * 3600) - (minutes * 60);
                    if (seconds < 10) seconds = "0" + seconds;
                    return seconds;
                },
                toSeconds : function (time) {
                    if(+time == 0) return 0;
                    var timeArray = time.split(':');
                    return +timeArray[2] + +timeArray[1] * 60 + +timeArray[0] * 3600;
                },
                convertAnchorTime : function (time) {
                    if (self.current.anchor().request() == 'start'){
                        self.current.anchor().timeStart(time);
                        self.current.anchor().hourStart(self.helpers.toHH(time));
                        self.current.anchor().minuteStart(self.helpers.toMM(time));
                        self.current.anchor().secondStart(self.helpers.toSS(time));
                    }
                    else {
                        self.current.anchor().timeStop(time);
                        self.current.anchor().hourStop(self.helpers.toHH(time));
                        self.current.anchor().minuteStop(self.helpers.toMM(time));
                        self.current.anchor().secondStop(self.helpers.toSS(time));
                    }
                },
                getEncodedUrl : function (data) {
                    var index = data.path().indexOf(data.name());
                    var path = data.path().substring(0,index);
                    return window.location.origin + '/' + encodeURI(path) + encodeURIComponent(data.name());
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

            self.elfinder = {
                currant: ko.observable(''),
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
                            'quicklook', 'rename', 'resize', 'search', 'sort', 'up', 'upload', 'view'
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
                    self.elfinder.handlers.change(elf.elfinder('instance'));
                    self.elfinder.currant(elf);
                },
                open: function (name) {
                    self.elfinder.currant().dialog({
                        modal: true,
                        width : 1300,
                        resizable: true,
                        position: { my: "center top-70%", at: "center", of: window },
                        title: name
                    });
                },
                getFile: function (file) {
                    if(self.current.mode() == 'replace') {
                        self.actions.media.replacement(file);
                        self.current.mode('');
                        return;
                    }
                    else if (self.current.mode() == 'anchor'){
                        self.actions.anchor.open.common(file);
                        self.current.mode('');
                        return;
                    }
                    self.actions.media.createMediable(file.hash);
                },
                handlers: {
                    upload: function (elfinder) {
                        elfinder.bind('upload', function(event) {
                            if (event.data.removed[0] == event.data.removed[1]) return;

                            ko.utils.arrayForEach(event.data.added, function(file) {
                                var path = file.url.substring(file.url.search('upload'),file.url.length).split('/');
                                var decodedPath = '';
                                ko.utils.arrayForEach(path, function (part) {
                                    decodedPath += decodeURIComponent(part) + "/";
                                });
                                decodedPath = decodedPath.slice(0, -1);

                                var media = {
                                    name: file.name,
                                    path: decodedPath,
                                    hash: file.hash
                                };

                                var type = file.mime.split('/')[0];
                                if (type == 'audio' || type == 'video' || type == 'image'){
                                    media.type = type;
                                    self.post.create.simpleMedia(media);
                                }
                                else if (file.mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                    media.type = 'text';
                                    self.post.create.docxMedia(media);
                                }
                                else {
                                    media.type = 'other';
                                    self.post.create.simpleMedia(media);
                                }

                            });
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
                                    mediable.media.start = ko.observable(self.helpers.toHHMMSS(+mediable.start()))
                                    : mediable.media.start = ko.observable(mediable.start());
                                +mediable.stop() ?
                                    mediable.media.stop = ko.observable(self.helpers.toHHMMSS(+mediable.stop()))
                                    : mediable.media.stop = ko.observable(mediable.stop());
                                mediable.media.pureName = ko.observable(mediable.media.name().substring(0, mediable.media.name().lastIndexOf('.')));
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
                                    mediable.media.start = ko.observable(self.helpers.toHHMMSS(+mediable.start()))
                                    : mediable.media.start = ko.observable(mediable.start());
                                +mediable.stop() ?
                                    mediable.media.stop = ko.observable(self.helpers.toHHMMSS(+mediable.stop()))
                                    : mediable.media.stop = ko.observable(mediable.stop());
                                mediable.media.pureName = ko.observable(mediable.media.name().substring(0, mediable.media.name().lastIndexOf('.')));
                                self.current.medias.push(mediable.media);
                            });
                        }
                    });
                },
                currentMedias: function (){
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