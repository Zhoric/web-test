$(document).ready(function () {
    var studyplanViewModel = function () {
        return new function () {
            var self = this;

            self.current = ko.observable({
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
            });
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
                self.current().disciplineplan()
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
                        self.current().disciplineplans(result.data());
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

            self.plan = ko.observable({
                create: function () {

                },
                update: function () {
                    var edit = self.current().disciplineplan();

                    var plan = {
                        id: edit.id(),
                        startSemester: edit.startSemester(),
                        semestersCount: edit.semestersCount(),
                        hours: edit.hours(),
                        hasProject: edit.hasProject(),
                        hasExam: edit.hasExam(),
                        discipline: self.disciplineSelected().disciplineId()
                    };

                    var currentUrl = window.location.href;
                    var studyplan = +currentUrl.substr(currentUrl.lastIndexOf('/')+1);

                    var url = '/api/plan/discipline/update';
                    var json = JSON.stringify({disciplinePlan: plan, studyPlanId: studyplan});

                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.open('POST', url, true);
                    xmlhttp.send(json);
                    xmlhttp.onreadystatechange = function() {
                        self.get.disciplineplans();
                        self.emptyCurrentPlan();
                    };

                },
                delete: function () {
                    self.toggleModal('#delete-plan-modal', 'close');
                    var url = '/api/plan/discipline/delete/' + self.current().disciplineplan().id();

                    $.post(url, function(result){
                        self.emptyCurrentPlan();
                        self.get.disciplineplans();
                    });

                },
                startEdit: function (data) {
                    self.mode('edit');
                    self.disciplineSelected(data);
                    self.current().disciplineplan(data);
                },
                startDelete: function (data) {
                    self.toggleModal('#delete-plan-modal', '');
                    self.current().disciplineplan(data);
                },
                cancelDelete: function () {
                    self.toggleModal('#delete-plan-modal', 'close');
                    self.emptyCurrentPlan();
                },
                cancelEdit: function () {
                    self.emptyCurrentPlan();
                }
            });

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
                disciplineSelected: self.disciplineSelected
            }
        };
    };

    ko.applyBindings(studyplanViewModel());
});
