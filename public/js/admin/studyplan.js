$(document).ready(function () {
    var studyplanViewModel = function () {
        return new function () {
            var self = this;

            self.errors = new errors();
            self.mode = ko.observable(state.none);
            self.initial = {
                unUrlPlanId: function(){
                    var url = window.location.href;
                    var id = +url.substr(url.lastIndexOf('/')+1);
                    if ($.isNumeric(id)){
                        self.current.planId(id);
                        self.get.disciplines();
                        return;
                    }
                    self.errors.show('Учебный план не определен');
                    setTimeout(function(){
                        window.location.href = '/admin/main';
                    }, 5000);
                },
                disciplines: ko.observableArray([]),
                discipline: {
                    id: ko.observable(0),
                    startSemester: ko.observable(0),
                    semestersCount: ko.observable(0),
                    hours: ko.observable(0),
                    hasProject: ko.observable(true),
                    hasExam: ko.observable(true),
                    discipline: ko.observable(''),
                    disciplineId: ko.observable(0)
                },
                selection: ko.observable(null)
            };
            self.filter = {
                discipline: ko.observable('')
            };
            self.current = {
                disciplines: ko.observableArray([]),
                discipline: ko.observable(self.initial.discipline),
                planId: ko.observable()
            };
            self.alter = {
              stringify: function(){
                  var discipline = ko.mapping.toJS(self.current.discipline);
                  if (self.mode() === state.create) delete discipline.id;
                  return JSON.stringify({
                      disciplinePlan: discipline,
                      studyPlanId: self.current.planId(),
                      disciplineId: self.current.discipline().disciplineId()
                  })
              }
            };
            self.actions = {
                show: function(data){
                    var isCurrent = self.current.discipline().id() === data.id();
                    if (isCurrent){
                        self.current.discipline(self.initial.discipline);
                        self.mode(state.none);
                        return;
                    }
                    self.current.discipline.copy(data);
                    self.mode(state.info);
                },
                start: {
                    create: function(){
                        self.mode() === state.create
                            ? self.mode(state.none)
                            : self.mode(state.create);
                        self.current.discipline(self.initial.discipline);
                    },
                    update: function(){
                        self.mode(state.update);
                    },
                    remove: function(){
                        self.mode(state.remove);
                        commonHelper.modal.open('#remove-discipline-plan-modal');
                    }
                },
                end: {
                    update: function(){
                        self.post.discipline();
                    },
                    remove: function(){
                        self.post.removal();
                    }
                },
                cancel: function(){
                    console.log(self.mode());
                    if (self.mode() === state.create){
                        self.current.discipline(self.initial.discipline);
                        self.mode(state.none);
                    }
                    self.mode(state.info);
                },
                switchExam: {
                    on: function(data){
                        data.hasExam(true);
                    },
                    off: function(data){
                        data.hasExam(false);
                    }
                },
                switchProject: {
                    on: function(data){
                        data.hasProject(true);
                    },
                    off: function(data){
                        data.hasProject(false);
                    }
                },
            };
            self.get = {
                fullList: function(){
                    var requestOptions = {
                        url: '/api/disciplines/',
                        errors: self.errors,
                        successCallback: function(data){
                            self.initial.disciplines(data());
                        }
                    };
                    $ajaxget(requestOptions);
                },
                disciplines: function(){
                    var name = self.filter.discipline() ? '&name=' + self.filter.discipline() : '';
                    var url = '/api/plan/discipline/show' +
                        '?studyplan=' + self.current.planId() + name;

                    var requestOptions = {
                        url: url,
                        errors: self.errors,
                        data: null,
                        successCallback: function(data){
                            self.current.disciplines(data.data());
                        }
                    };
                    $ajaxpost(requestOptions);
                }
            };
            self.post = {
                discipline: function(){
                    var requestOptions = {
                        url: self.mode() === state.create ? '/api/plan/discipline/create' : '/api/plan/discipline/update',
                        data: self.alter.stringify(),
                        errors: self.errors,
                        successCallback: function(){
                            self.mode(state.none);
                            self.current.discipline(self.initial.discipline);
                            self.initial.selection(null);
                            self.get.disciplines();
                        }
                    };
                    $ajaxpost(requestOptions);
                },
                removal: function(){
                    var url = '/api/plan/discipline/delete/' + self.current.discipline().id();
                    var requestOptions = {
                        url: url,
                        data: null,
                        errors: self.errors,
                        successCallback: function(){
                            self.current.discipline(self.initial.discipline);
                            self.mode(state.none);
                            self.get.disciplines();
                        }
                    };
                    $ajaxpost(requestOptions);
                }
            };

            self.initial.unUrlPlanId();
            self.get.fullList();

            self.initial.selection.subscribe(function(value){
                if (value){
                    self.current.discipline().disciplineId(value.id());
                    self.current.discipline().discipline(value.name());
                    return;
                }
                self.current.discipline().disciplineId(0);
                self.current.discipline().discipline('');
            });
            self.filter.discipline.subscribe(function(){
                self.get.disciplines();
            });

            return {
                current: self.current,
                initial: self.initial,
                filter: self.filter,
                mode: self.mode,
                errors: self.errors,
                actions: self.actions
            }
        };
    };

    ko.applyBindings(studyplanViewModel());
});
