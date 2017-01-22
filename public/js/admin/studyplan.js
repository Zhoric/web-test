$(document).ready(function () {
    var studyplanViewModel = function () {
        return new function () {
            var self = this;

            self.errors = new errors();
            self.pagination = pagination();
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
            self.alter = {};
            self.actions = {
                show: function(data){
                    var isCurrent = self.current.discipline().id() === data.id();
                    if (isCurrent){
                        self.actions.cancel();
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
                    self.current.discipline(self.initial.discipline);
                    self.mode() === state.create
                        ? self.mode(state.none)
                        : self.mode(state.info);
                }
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
                        '?studyplan=' + self.current.planId() +
                        '&page=' + self.pagination.currentPage() +
                        'pageSize=' + self.pagination.pageSize() + name;

                    var requestOptions = {
                        url: url,
                        errors: self.errors,
                        data: null,
                        successCallback: function(data){
                            self.current.disciplines();
                            self.current.disciplines(data.data());
                            self.pagination.itemsCount(data.count());
                        }
                    };
                    $ajaxpost(requestOptions);
                }
            };
            self.post = {
                discipline: function(){},
                removal: function(){}
            };

            self.initial.unUrlPlanId();
            self.get.fullList();

            // self.current = {
            //     disciplineplans : ko.observableArray([]),
            //     disciplineplan: ko.observable({
            //         id: ko.observable(0),
            //         startSemester: ko.observable(0),
            //         semestersCount: ko.observable(0),
            //         hours: ko.observable(0),
            //         hasProject: ko.observable(true),
            //         hasExam: ko.observable(true),
            //         discipline: ko.observable(''),
            //         disciplineId: ko.observable(0)
            //     })
            // };
            //
            // self.emptyCurrentPlan = function () {
            //     self.current.disciplineplan()
            //         .id(0)
            //         .startSemester(0)
            //         .semestersCount(0)
            //         .hours(0)
            //         .hasProject(true)
            //         .hasExam(true)
            //         .discipline('')
            //         .disciplineId(0);
            //     self.mode('none');
            // };
            // self.disciplineSelected = ko.observable();
            //
            //
            // self.get = {
            //     disciplineplans: function() {
            //         var filter = self.filter;
            //         var currentUrl = window.location.href;
            //         var studyplan = 'studyplan=' + (+currentUrl.substr(currentUrl.lastIndexOf('/')+1));
            //         var name = 'name=' + filter.discipline();
            //         var page = 'page=' + self.pagination.currentPage();
            //         var pageSize = 'pageSize=' + self.pagination.pageSize();
            //         var url = '/api/plan/discipline/show?' + page + '&' + pageSize + '&' + name + '&' + studyplan;
            //
            //         $.post(url, function(response){
            //             var result = ko.mapping.fromJSON(response);
            //             if (result.Success()){
            //                 self.current.disciplineplans(result.Data.data());
            //                 self.pagination.itemsCount(result.Data.count());
            //                 return;
            //             }
            //             self.errors.show(result.Message());
            //         });
            //     }
            // };
            //
            // self.get.disciplineplans();
            //
            // self.plan = {
            //     create: function () {
            //
            //     },
            //     update: function () {
            //         var edit = self.current.disciplineplan();
            //
            //         var plan = {
            //             id: edit.id(),
            //             startSemester: edit.startSemester(),
            //             semestersCount: edit.semestersCount(),
            //             hours: edit.hours(),
            //             hasProject: edit.hasProject(),
            //             hasExam: edit.hasExam()
            //         };
            //
            //         var currentUrl = window.location.href;
            //         var studyplan = +currentUrl.substr(currentUrl.lastIndexOf('/')+1);
            //
            //
            //         var url = '/api/plan/discipline/update';
            //
            //         var json = JSON.stringify({
            //             disciplinePlan: plan,
            //             studyPlanId: studyplan,
            //             disciplineId: self.disciplineSelected().disciplineId()
            //         });
            //
            //         $.post(url, json, function(response){
            //             var result = ko.mapping.fromJSON(response);
            //             if (result.Success()){
            //                 self.emptyCurrentPlan();
            //                 self.get.disciplineplans();
            //                 return;
            //             }
            //             self.errors.show(result.Message());
            //         });
            //
            //     },
            //     delete: function () {
            //         self.toggleModal('#delete-plan-modal', 'close');
            //         var url = '/api/plan/discipline/delete/' + self.current.disciplineplan().id();
            //
            //         $.post(url, function(response){
            //             var result = ko.mapping.fromJSON(response);
            //             if (result.Success()){
            //                 self.emptyCurrentPlan();
            //                 self.get.disciplineplans();
            //                 self.mode('none');
            //                 return;
            //             }
            //             self.errors.show(result.Message());
            //         });
            //
            //     },
            //     startEdit: function (data) {
            //         self.mode('edit');
            //         self.disciplineSelected(data);
            //         self.current.disciplineplan(data);
            //     },
            //     startDelete: function (data) {
            //         self.toggleModal('#delete-plan-modal', '');
            //         self.current.disciplineplan(data);
            //         self.mode('delete');
            //     },
            //     cancelDelete: function () {
            //         self.toggleModal('#delete-plan-modal', 'close');
            //         self.mode('none');
            //     },
            //     cancelEdit: function () {
            //         self.mode('none');
            //     }
            // };

            self.initial.selection.subscribe(function(value){

            });
            self.pagination.itemsCount.subscribe(function(value){
                if (value){
                    self.pagination.totalPages(Math.ceil(
                        value/self.pagination.pageSize()
                    ));
                }
            });
            self.filter.discipline.subscribe(function(){
                self.get.disciplines();
            });

            return {
                current: self.current,
                initial: self.initial,
                filter: self.filter,
                pagination: self.pagination,
                mode: self.mode,
                errors: self.errors,
                actions: self.actions
            }
        };
    };

    ko.applyBindings(studyplanViewModel());
});
