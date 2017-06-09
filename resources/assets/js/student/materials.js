$(document).ready(function(){
    var materialsViewModel = function(){
        return new function(){
            var self = this;

            initializeViewModel.call(self, {
                page: menu.student.materials,
                mode: true,
                pagination: 5,
                multiselect: true
            });

            self.modals = {
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
                multimedias: ko.observableArray([]),
                others: ko.observableArray([]),
                media: ko.observable({
                    id: ko.observable(0),
                    type: ko.observable(''),
                    content: ko.observable(''),
                    path: ko.observable(''),
                    name: ko.observable(''),
                    hash: ko.observable(''),
                    start: ko.observable(null),
                    stop: ko.observable(null)
                }),
                multimediaURL: ko.observable(''),
                tests: ko.observableArray([])
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
                            self.get.tests();
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
                    move: function (data) {
                        self.alter.media.fill(data);
                        if (data.type() == 'text') {
                            if (data.start() != null) window.open('/media/' + data.id() + '#' + data.start());
                            else window.open('/media/' + data.id());
                        }
                        else  if (data.type() == 'audio' || data.type() == 'video') {
                            self.actions.multimedia.open(data);
                        }
                        else window.open(self.helpers.getEncodedUrl(data));
                    }
                },
                multimedia : {
                    open: function (data) {
                        //открытие модального окна с аудио/видео; загрузка аудио/видео
                        self.current.multimediaURL(self.helpers.getEncodedUrl(data));
                        commonHelper.modal.open(self.modals.multimedia);
                        $('#multimedia')[0].load();
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
                },
                test: {
                    start: function(data){
                        self.confirm.show({
                            message: 'Вы уверены, что хотите пройти выбранный тест?',
                            additionalHtml: '<p><span class="bold">Предупреждение: </span>' +
                            'Во время прохождения теста перезагрузка или переход на другую страницу приведёт к тому, ' +
                            'что текущая попытка прохождения теста будет считаться израсходованной.</p>',
                            approve: function(){
                                commonHelper.cookies.create({
                                    testId: data.test.id(),
                                    testName: data.test.subject(),
                                    disciplineName: data.test.disciplineName(),
                                    testType: data.test.type()
                                });
                                window.location.href = '/test';
                            }
                        });
                    }
                }
            };
            self.helpers = {
                toHHMMSS : function (time) {
                    var sec_num = parseInt(time, 10);
                    var hours = Math.floor(sec_num / 3600);
                    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
                    var seconds = sec_num - (hours * 3600) - (minutes * 60);

                    if (hours < 10) hours = "0" + hours;
                    if (minutes < 10) minutes = "0" + minutes;
                    if (seconds < 10) seconds = "0" + seconds;

                    return hours + ':' + minutes + ':' + seconds;
                },
                getEncodedUrl : function (data) {
                    var index = data.path().indexOf(data.name());
                    var path = data.path().substring(0,index);
                    return window.location.origin + '/' + encodeURI(path) + encodeURIComponent(data.name());
                },
                toSeconds : function (time) {
                    if(+time == 0) return 0;
                    var timeArray = time.split(':');
                    return +timeArray[2] + +timeArray[1] * 60 + +timeArray[0] * 3600;
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
                            self.get.medias(data);
                        }
                    });
                },
                themeMedias: function (themeId) {
                    $ajaxget({
                        url: '/api/mediable/theme/' + themeId,
                        errors: self.errors,
                        successCallback: function(data){
                            self.get.medias(data);
                        }
                    });
                },
                currentMedias: function () {
                    if(self.current.theme().id() == 0)
                        self.get.disciplineMedias(self.current.discipline().id());
                    else self.get.themeMedias(self.current.theme().id());
                },
                medias: function (data) {
                    self.current.medias.removeAll();
                    self.current.multimedias.removeAll();
                    self.current.others.removeAll();
                    ko.utils.arrayForEach(data(), function (mediable) {
                        +mediable.start() ?
                            mediable.media.start = ko.observable(self.helpers.toHHMMSS(+mediable.start()))
                            : mediable.media.start = ko.observable(mediable.start());
                        +mediable.stop() ?
                            mediable.media.stop = ko.observable(self.helpers.toHHMMSS(+mediable.stop()))
                            : mediable.media.stop = ko.observable(mediable.stop());
                        mediable.media.pureName = ko.observable(mediable.media.name().substring(0, mediable.media.name().lastIndexOf('.')));
                        if (mediable.media.type() == 'audio' || mediable.media.type() == 'video' || mediable.media.type() == 'image')
                            self.current.multimedias.push(mediable.media);
                        else if (mediable.media.type() == 'text') self.current.medias.push(mediable.media);
                        else self.current.others.push(mediable.media);
                    });
                },
                tests: function(){
                    $ajaxget({
                        url: '/api/tests/showForStudent?discipline=' + self.current.discipline().id(),
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.tests(data());
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