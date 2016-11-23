$(document).ready(function () {
    var studyplanViewModel = function () {
        return new function () {
            var self = this;

            self.current = {
                disciplineplans : ko.observableArray([]),
                disciplineplan: ko.observable({
                    id: ko.observable(0),
                    startSemester: ko.observable(0),
                    semestersCount: ko.observable(0),
                    hours: ko.observable(0),
                    hasProject: ko.observable(true),
                    hasExam: ko.observable(true),
                    discipline: ko.observable(''),
                    disciplineId: ko.observable(0)
                })
            };
            self.errors = {
                message: ko.observable(),
                show: function(message){
                    self.errors.message(message);
                    self.toggleModal('#errors-modal', '');
                },
                accept: function(){
                    self.toggleModal('#errors-modal', 'close');
                }
            };
            self.mode = ko.observable('none');
            self.filter = {
                discipline : ko.observable('')
            };
            self.pagination = {
                currentPage: ko.observable(1),
                pageSize: ko.observable(10),
                itemsCount: ko.observable(1),
                totalPages: ko.observable(1),

                selectPage: function(page){
                    self.pagination.currentPage(page);
                    self.get.disciplines();
                },
                dotsVisible: function(index){
                    var total = self.pagination.totalPages();
                    var current = self.pagination.currentPage();
                    if (total > 11 && index == total-1 && index > current + 2  ||total > 11 && index == current - 1 && index > 3)  {
                        return true;
                    }
                    return false;
                },
                pageNumberVisible: function(index){
                    var total = self.pagination.totalPages();
                    var current = self.pagination.currentPage();
                    if (total < 12 ||
                        index > (current - 2) && index < (current + 2) ||
                        index > total - 2 ||
                        index < 3) {
                        return true;
                    }
                    return false;
                },
            };

           // self.mode('edit');

            self.emptyCurrentPlan = function () {
                self.current.disciplineplan()
                    .id(0)
                    .startSemester(0)
                    .semestersCount(0)
                    .hours(0)
                    .hasProject(true)
                    .hasExam(true)
                    .discipline('')
                    .disciplineId(0);
                self.mode('none');
            };
            self.disciplineSelected = ko.observable();


            self.get = {
                disciplineplans: function() {
                    var filter = self.filter;
                    var currentUrl = window.location.href;
                    var studyplan = 'studyplan=' + (+currentUrl.substr(currentUrl.lastIndexOf('/')+1));
                    var name = 'name=' + filter.discipline();
                    var page = 'page=' + self.pagination.currentPage();
                    var pageSize = 'pageSize=' + self.pagination.pageSize();
                    var url = '/api/plan/discipline/show?' + page + '&' + pageSize + '&' + name + '&' + studyplan;

                    $.post(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        self.current.disciplineplans(result.data());
                        self.pagination.itemsCount(result.count());
                    });
                    //console.log(discipline);
                    /*var url = '/api/plan/profile/' + profile;

                    $.get(url, function (response) {
                        var result = ko.mapping.fromJSON(response);
                        self.disciplineplans(result());
                    }); */
                }
            };

            self.get.disciplineplans();

            self.plan = {
                create: function () {

                },
                update: function () {
                    var edit = self.current.disciplineplan();

                    var plan = {
                        id: edit.id(),
                        startSemester: edit.startSemester(),
                        semestersCount: edit.semestersCount(),
                        hours: edit.hours(),
                        hasProject: edit.hasProject(),
                        hasExam: edit.hasExam()
                    };

                    var currentUrl = window.location.href;
                    var studyplan = +currentUrl.substr(currentUrl.lastIndexOf('/')+1);


                    var url = '/api/plan/discipline/update';

                    var json = JSON.stringify({
                        disciplinePlan: plan,
                        studyPlanId: studyplan,
                        disciplineId: self.disciplineSelected().disciplineId()
                    });

                    $.post(url, json, function(){
                        self.emptyCurrentPlan();
                        self.get.disciplineplans();

                    });

                },
                delete: function () {
                    self.toggleModal('#delete-plan-modal', 'close');
                    var url = '/api/plan/discipline/delete/' + self.current.disciplineplan().id();

                    $.post(url, function(result){
                        self.emptyCurrentPlan();
                        self.get.disciplineplans();
                        self.mode('none');
                    });

                },
                startEdit: function (data) {
                    self.mode('edit');
                    self.disciplineSelected(data);
                    self.current.disciplineplan(data);
                },
                startDelete: function (data) {
                    self.toggleModal('#delete-plan-modal', '');
                    self.current.disciplineplan(data);
                    self.mode('delete');
                },
                cancelDelete: function () {
                    self.toggleModal('#delete-plan-modal', 'close');
                    self.mode('none');
                },
                cancelEdit: function () {
                    self.mode('none');
                }
            };

            self.toggleModal = function(selector, action){
                $(selector).arcticmodal(action);
            };

            self.pagination.itemsCount.subscribe(function(value){
                if (value){
                    self.pagination.totalPages(Math.ceil(
                        value/self.pagination.pageSize()
                    ));
                }
            });
            self.filter.discipline.subscribe(function(){
                self.get.disciplineplans();
            });

            return {
                current: self.current,
                filter: self.filter,
                get: self.get,
                pagination: self.pagination,
                mode: self.mode,
                plan: self.plan,
                disciplineSelected: self.disciplineSelected,
                errors: self.errors
            }
        };
    };

    ko.applyBindings(studyplanViewModel());
});
