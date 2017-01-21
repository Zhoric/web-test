/**
 * Created by nyanjii on 11.10.16.
 */
$(document).ready(function(){
    var groupsViewModel = function(){
        return new function(){
            var self = this;
            self.errors = new errors();
            self.pagination = pagination();
            self.pagination.pageSize(10);
            self.mode = ko.observable(state.none);

            self.initial = {
                profileId: ko.observable(null),
                group: {
                    id: ko.observable(0),
                    name: ko.observable(''),
                    prefix: ko.observable(''),
                    number: ko.observable(1),
                    isFulltime: ko.observable(true),
                    course: ko.observable(1)
                },
                unUrlProfileId: function(){
                    var url = window.location.href;
                    var id = +url.substring(url.lastIndexOf('/'));
                    if ($.isNumeric(id)){
                        self.initial.profileId(id);
                    }
                    self.get.groups();
                }
            };

            self.current = {
                groups: ko.observableArray([]),
                group: ko.observable(self.initial.group),

                institutes: ko.observableArray([]),
                institute: ko.observable(null),

                profiles: ko.observableArray([]),
                profile: ko.observable(null),

                plans: ko.observableArray([]),
                plan: ko.observable(null),

                groupPlan: ko.observable(null)
            };
            self.filter = {
                name: ko.observable('')
            };
            self.alter = {

            };
            self.actions = {
                start: {
                    create: function(){
                        self.mode() === state.create
                            ? self.mode(state.none)
                            : self.mode(state.create);
                        self.current.group(self.initial.group);
                        self.current.groupPlan(null);
                    },
                    update: function(data){
                        console.log(data);
                        self.current.group.copy(data);
                    },
                    remove: function(data){
                        self.current.group.copy(data);
                        commonHelper.modal.open('#remove-group-modal');
                    }
                },
                end: {
                    create: function(){},
                    update: function(){
                        // self.current.group(self.initial.group);
                        //self.mode(state.none);
                    },
                    remove: {}
                },
                cancel: function(){
                    self.current.group(self.initial.group);
                },
                generate: function(){

                },
                moveTo: {
                    students: function(){}
                },
                switchForm: {
                    day: function(){
                        self.current.group().isFulltime(true);
                    },
                    night: function(){
                        self.current.group().isFulltime(false);
                    }
                },
                selectPlan: {
                    start: function(){
                        commonHelper.modal.open('#select-plan-modal');
                    },
                    cancel: function(){
                        self.current.institute(null);
                        commonHelper.modal.close('#select-plan-modal');
                    },
                    end: function(){
                        self.current.groupPlan.copy(self.current.plan);
                        console.log(self.current.groupPlan());
                        self.actions.selectPlan.cancel();
                    }
                }
            };
            self.get = {
                groups: function(){
                    var profileId = self.initial.profileId() ? 'profileId=' + self.initial.profileId() : '';
                    var name =  self.filter.name() ? '&name=' + self.filter.name() : '';

                    var url = '/api/groups/show' +
                        '?page=' + self.pagination.currentPage() +
                        '&pageSize=' + self.pagination.pageSize() +
                        name +  profileId;

                    var requestOptions = {
                        url: url,
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.groups(data.data());
                            self.pagination.itemsCount(data.count());
                        }
                    };

                    $ajaxget(requestOptions);
                },
                institutes: function(){
                    var requestOptions = {
                        url: '/api/institutes',
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.institutes(data());
                        }
                    };
                    $ajaxget(requestOptions);
                },
                profiles: function(){
                    var requestOptions = {
                        url: '/api/institute/' + self.current.institute().id() + '/profiles',
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.profiles(data());
                        }
                    };
                    $ajaxget(requestOptions);
                },
                plans: function(){
                    var requestOptions = {
                        url: '/api/profile/' + self.current.profile().id() + '/plans',
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.plans(data());
                        }
                    };
                    $ajaxget(requestOptions);
                }
            };
            self.post = {};

            self.current.institute.subscribe(function(value){
                if (value){
                    self.get.profiles();
                    return;
                }
                self.current.profiles([]);
                self.current.plans([]);
                self.current.profile(null);
                self.current.plan(null);
            });
            self.current.profile.subscribe(function(value){
                if (value){
                    self.get.plans();
                    return;
                }
                self.current.plans([]);
                self.current.plan(null);
            });
            self.pagination.itemsCount.subscribe(function(value){
                if (value){
                    self.pagination.totalPages(Math.ceil(
                        value/self.pagination.pageSize()
                    ));
                }
            });
            self.pagination.currentPage.subscribe(function(){
                self.get.groups();
            });
            self.filter.name.subscribe(function(){
                self.get.groups();
            });

            self.initial.unUrlProfileId();
            self.get.institutes();



            // self.groups = ko.observableArray([]);
            // self.groupStudyForm = ko.observable('Очная');
            //
            // self.current = ko.observable({
            //     groupStudents : ko.observable([]),
            //     profileId : ko.observable(''),
            //     group: ko.observable({
            //         id: ko.observable(0),
            //         name: ko.observable(''),
            //         prefix: ko.observable(''),
            //         number: ko.observable(1),
            //         isFullTime: ko.observable(true),
            //         course: ko.observable(1),
            //         studyplan: ko.observable('Указать'),
            //         studyplanId: ko.observable(0)
            //     }),
            //     student: ko.observable({
            //         id: ko.observable(0),
            //         firstName: ko.observable(''),
            //         lastName: ko.observable(''),
            //         patronymic: ko.observable(''),
            //         active: ko.observable(''),
            //         email: ko.observable('')
            //
            //     })
            //
            // });
            //
            // self.emptyCurrentGroup =  function () {
            //         self.current().group()
            //             .id(0)
            //             .name('')
            //             .prefix('')
            //             .number(1)
            //             .isFullTime(true)
            //             .course(1)
            //             .studyplan('Указать')
            //             .studyplanId(0);
            //         self.mode('none');
            // };
            // self.emptyCurrentStudent = function () {
            //         self.current().student()
            //             .id(0)
            //             .firstName('')
            //             .lastName('')
            //             .patronymic('')
            //             .email('')
            //             .active('');
            //
            // };
            //
            //
            //
            // self.institutes = ko.observableArray([]);
            // self.profiles = ko.observableArray([]);
            // self.studyplans = ko.observableArray([]);
            //
            // self.studyplanSelect = ko.observable({
            //     institute: ko.observable(),
            //     profile: ko.observable(),
            //     studyplan: ko.observable()
            // });
            // self.studyplanSelect().institute.subscribe(function(data){
            //     if (data){
            //         if (!self.studyplanSelect().profile() && !self.profiles().length){
            //             $.get('/api/institute/' + data.id() + '/profiles', function(response){
            //                 var result = ko.mapping.fromJSON(response);
            //                 if (result.Success()){
            //                     self.profiles(result.Data());
            //                     return;
            //                 }
            //                 self.errors.show(result.Message());
            //             });
            //         }
            //     }
            // });
            // self.studyplanSelect().profile.subscribe(function(data){
            //     if (data){
            //         $.get('/api/profile/' + data.id() + '/plans', function(response){
            //             var result = ko.mapping.fromJSON(response);
            //             if (result.Success()) {
            //                 self.studyplans(result.Data());
            //                 return;
            //             }
            //             self.errors.show(result.Message());
            //         });
            //     }
            // });
            //
            //
            // self.selectStudyPlan = function(){
            //     if (!self.studyplanSelect().institute() && !self.institutes().length) {
            //         $.get('/api/institutes', function(response){
            //             var result = ko.mapping.fromJSON(response);
            //             if (result.Success()){
            //                 self.institutes(result.Data());
            //                 return;
            //             }
            //             self.errors.show(result.Message());
            //         });
            //     }
            //     self.toggleModal('#select-plan-modal', '');
            // };
            // self.approveStudyPlan = function(){
            //     var select = self.studyplanSelect();
            //     self.current().group()
            //         .studyplan(select.studyplan().name())
            //         .studyplanId(select.studyplan().id());
            //     select.studyplan(undefined).institute(undefined).profile(undefined);
            //     self.toggleModal('#select-plan-modal', 'close');
            // };
            //
            // self.groupSelect = ko.observable();
            //
            // self.generateGroupName = function(){
            //     var group = self.current().group();
            //     var name = group.prefix() + '-' +
            //         group.course() +
            //         group.number();
            //     name += group.isFullTime() ? 'о' : 'з';
            //     group.name(name);
            // };
            //
            // self.fillCurrentGroup = function(data, mode){
            //     self.current().group()
            //         .id(data.id())
            //         .name(data.name())
            //         .prefix(data.prefix())
            //         .number(data.number())
            //         .isFullTime(data.isFullTime)
            //         .course(data.course())
            //         .studyplan(data.studyplan)
            //         .studyplanId(data.studyplanId);
            //     self.mode(mode);
            //     self.getStudents();
            // };
            //
            //
            // self.getGroups = function(){
            //     var url = window.location.href;
            //     var filter = self.filter;
            //     var profileId = +url.substr(url.lastIndexOf('/')+1);
            //     var name = 'name=' + filter.name();
            //     var page = 'page=' + self.pagination.currentPage();
            //     var pageSize = 'pageSize=' + self.pagination.pageSize();
            //     var url = '/api/groups/show?' + page + '&' + pageSize + '&' + name + '&' + profileId;
            //
            //     $.get(url, function(response){
            //         var result = ko.mapping.fromJSON(response);
            //         if (result.Success()){
            //             self.groups(result.Data.data());
            //             self.pagination.itemsCount(result.Data.count());
            //             return;
            //         }
            //         self.errors.show(result.Message());
            //     });
            // };
            // self.getAlterGroups = function(){
            //     var url = window.location.href;
            //     var filter = self.filter;
            //     var profileId = +url.substr(url.lastIndexOf('/')+1);
            //     /*
            //      url = url.substr(0, url.indexOf('/a'));
            //      if ($.isNumeric(profileId)){
            //      url += '/api/profile/' + profileId + '/groups';
            //      self.currentProfileId(profileId);
            //      }
            //      else{
            //      url += '/api/groups';
            //      } */
            //
            //     var name = 'name=' + filter.name();
            //     var page = 'page=' + self.pagination.currentPage();
            //     var pageSize = 'pageSize=' + self.pagination.pageSize();
            //     var url = '/api/groups/show?' + page + '&' + pageSize + '&' + name + '&' + profileId;
            //
            //     $.get(url, function(response){
            //         var result = ko.mapping.fromJSON(response);
            //         if (result.Success()){
            //             self.groups(result.Data.data());
            //             self.pagination.itemsCount(result.Data.count());
            //             return;
            //         }
            //         self.errors.show(result.Message());
            //     });
            // };
            // self.getGroups();
            // //self.getAlterGroups();
            //
            // self.getStudents = function(){
            //     var url = '/api/groups/' + self.current().group().id() + '/students';
            //     $.get(url, function(response){
            //         var result = ko.mapping.fromJSON(response);
            //         if (result.Success()){
            //             self.current().groupStudents(result.Data());
            //             return;
            //         }
            //         self.errors.show(result.Message());
            //     });
            // };
            // self.getAlterStudents = function(){
            //     var url = '/api/groups/' + self.current().group().id() + '/students';
            //     $.get(url, function(response){
            //         var result = ko.mapping.fromJSON(response);
            //         if (result.Success()){
            //             self.current().groupStudents(result.Data());
            //             return;
            //         }
            //         self.errors.show(result.Message());
            //     });
            // };
            //
            // self.startRemove = function(data) {
            //     self.toggleModal('#delete-student-modal', '');
            //     self.current().student(data);
            // };
            // self.startTransfer = function (data) {
            //     self.toggleModal('#transfer-student-modal', '');
            //     self.current().student(data);
            // };
            //
            //
            // self.addGroup = function(){
            //     self.emptyCurrentGroup();
            //     self.mode('add');
            // };
            // self.showGroup = function(data){
            //     (self.current().group().id() === data.id()) ?
            //         self.emptyCurrentGroup() :
            //         self.fillCurrentGroup(data, 'info');
            // };
            // self.editGroup = function(){
            //     self.mode('edit');
            // };
            // self.deleteGroup = function(){
            //     self.mode('delete');
            //     self.toggleModal('#delete-group-modal', '');
            // };
            //
            //
            // self.student = ko.observable({
            //     transfer: function () {
            //         self.toggleModal('#transfer-student-modal', 'close');
            //
            //         $.post(
            //             '/api/groups/student/setGroup',
            //             JSON.stringify({studentId: self.current().student().id(), groupId: self.groupSelect().id()}),
            //             function(response){
            //                 var result = ko.mapping.fromJSON(response);
            //                 if (result.Success()){
            //                     self.emptyCurrentStudent();
            //                     self.getStudents();
            //                     return;
            //                 }
            //                 self.errors.show(result.Message());
            //             });
            //
            //
            //     },
            //
            //     edit: function (data) {
            //         self.current().student()
            //             .id(data.id())
            //             .firstName(data.firstName())
            //             .lastName(data.lastName())
            //             .patronymic(data.patronymic())
            //             .email(data.email())
            //             .active(data.active());
            //
            //         self.mode('edit-student');
            //     },
            //
            //     delete: function () {
            //         self.toggleModal('#delete-student-modal', 'close');
            //         var url = '/api/groups/student/delete/' + self.current().student().id();
            //
            //         $.post(url, function(response){
            //             var result = ko.mapping.fromJSON(response);
            //             if (result.Success()){
            //                 self.emptyCurrentStudent();
            //                 self.getStudents();
            //                 return;
            //             }
            //             self.errors.show(result.Message());
            //         });
            //     },
            //
            //     cancel: function () {
            //         if(self.mode() === 'edit-student'){
            //             self.mode('info');
            //         }
            //     },
            //
            //     cancelDelete: function () {
            //         self.toggleModal('#delete-student-modal', 'close');
            //         //self.emptyCurrentStudent();
            //     },
            //
            //     cancelTransfer: function () {
            //         self.toggleModal('#transfer-student-modal', 'close');
            //         //self.emptyCurrentStudent();
            //     },
            //
            //     approve: function () {
            //         var edit = self.current().student();
            //
            //         var student = {
            //             id: edit.id(),
            //             firstname: edit.firstName(),
            //             lastname: edit.lastName(),
            //             patronymic: edit.patronymic(),
            //             active: edit.active(),
            //             email: edit.email()
            //         };
            //
            //         var url = '/api/groups/student/update';
            //         var json = JSON.stringify({student: student});
            //
            //         $.post(url, json, function(response){
            //             var result = ko.mapping.fromJSON(response);
            //             if (result.Success()){
            //                 self.getStudents();
            //                 return;
            //             }
            //             self.errors.show(result.Message());
            //         });
            //         self.mode('info');
            //     }
            //
            // });
            //
            // self.approve = function(){
            //     var edit = self.current().group();
            //     if (self.mode() === 'delete'){
            //         $.post(
            //             '/api/groups/delete/' + edit.id(),
            //             {},
            //             function(response){
            //                 var result = ko.mapping.fromJSON(response);
            //                 if (!result.Success()) self.errors.show(result.Message());
            //             });
            //         self.groups.remove(function(item){
            //             if (item.id() === edit.id())
            //                 return item;
            //         });
            //         self.toggleModal('#delete-group-modal', 'close');
            //         self.emptyCurrentGroup();
            //         self.getGroups();
            //         return;
            //     }
            //     var group = {
            //         prefix: edit.prefix(),
            //         course: edit.course(),
            //         name: edit.name(),
            //         number: edit.number(),
            //         isFulltime: edit.isFullTime()
            //     };
            //     var planId = edit.studyplanId();
            //
            //     if (self.mode() === 'edit'){
            //         group.id = edit.id();
            //         $.post(
            //             '/api/groups/update',
            //             JSON.stringify({group: group, studyPlanId: planId}),
            //             function(response){
            //                 var result = ko.mapping.fromJSON(response);
            //                 if (!result.Success()) self.errors.show(result.Message());
            //             });
            //         self.groups().find(function(item){
            //             if (item.id() === edit.id()){
            //                 item.prefix(edit.prefix())
            //                     .course(edit.course())
            //                     .name(edit.name())
            //                     .number(edit.number())
            //                     .isFullTime(edit.isFullTime());
            //                 return item;
            //             }
            //         });
            //         self.mode('info');
            //         return;
            //     }
            //     if (self.mode() === 'add'){
            //         var json = JSON.stringify({group: group, studyPlanId: planId});
            //         var url = '/api/groups/create';
            //
            //         $.post(url, json, function(response){
            //             var result = ko.mapping.fromJSON(response);
            //             if (!result.Success()) self.errors.show(result.Message());
            //         });
            //         self.groups([]);
            //         self.emptyCurrentGroup();
            //         self.getGroups();
            //     }
            //
            //
            // };
            // self.alterApprove = function(){
            //     var edit = self.current().group();
            //     if (self.mode() === 'delete'){
            //         $.post(
            //             '/api/groups/delete/' + edit.id(),
            //             {},
            //             function(response){
            //                 var result = ko.mapping.fromJSON(response);
            //                 if (!result.Success()) self.errors.show(result.Message());
            //             });
            //         self.groups.remove(function(item){
            //             if (item.id() === edit.id())
            //                 return item;
            //         });
            //         self.toggleModal('#delete-group-modal', 'close');
            //         self.emptyCurrentGroup();
            //         self.getGroups();
            //         return;
            //     }
            //     var group = {
            //         prefix: edit.prefix(),
            //         course: edit.course(),
            //         name: edit.name(),
            //         number: edit.number(),
            //         isFulltime: edit.isFullTime()
            //     };
            //     var planId = edit.studyplanId();
            //
            //     if (self.mode() === 'edit'){
            //         group.id = edit.id();
            //         $.post(
            //             '/api/groups/update',
            //             JSON.stringify({group: group, studyPlanId: planId}),
            //             function(response){
            //                 var result = ko.mapping.fromJSON(response);
            //                 if (!result.Success()) self.errors.show(result.Message());
            //             });
            //         self.groups().find(function(item){
            //             if (item.id() === edit.id()){
            //                 item.prefix(edit.prefix())
            //                     .course(edit.course())
            //                     .name(edit.name())
            //                     .number(edit.number())
            //                     .isFullTime(edit.isFullTime());
            //                 return item;
            //             }
            //         });
            //         self.mode('info');
            //         return;
            //     }
            //     if (self.mode() === 'add'){
            //         $.post(
            //             '/api/groups/create',
            //             JSON.stringify({group: group, studyPlanId: planId}),
            //             function(response){
            //                 var result = ko.mapping.fromJSON(response);
            //                 if (!result.Success()) self.errors.show(result.Message());
            //             });
            //         self.groups([]);
            //         self.emptyCurrentGroup();
            //         self.getGroups();
            //     }
            //
            //
            // };
            // self.cancel = function(){
            //     var m = self.mode();
            //     if (m === 'edit'){
            //         self.mode('info');
            //         return;
            //     }
            //     self.emptyCurrentGroup();
            //     self.toggleCurrentStudent();
            //
            //     if (m === 'delete'){
            //         $('#delete-group-modal').arcticmodal('close');
            //     }
            //
            // };
            //
            // self.toggleModal = function(selector, action){
            //     $(selector).arcticmodal(action);
            // };





            return {
                current: self.current,
                actions: self.actions,
                filter: self.filter,
                errors: self.errors,
                pagination: self.pagination,
                mode: self.mode
                // groups: self.groups,
                // current: self.current,
                // mode: self.mode,
                // groupStudyForm: self.groupStudyForm,
                // pagination: self.pagination,
                // filter: self.filter,
                //
                // institutes: self.institutes,
                // profiles: self.profiles,
                // studyplans: self.studyplans,
                // studyplanSelect: self.studyplanSelect,
                // groupSelect: self.groupSelect,
                //
                // showGroup: self.showGroup,
                // addGroup: self.addGroup,
                // editGroup: self.editGroup,
                // deleteGroup: self.deleteGroup,
                // approve: self.approve,
                // cancel: self.cancel,
                // selectStudyPlan: self.selectStudyPlan,
                // approveStudyPlan: self.approveStudyPlan,
                // generateGroupName: self.generateGroupName,
                //
                // startRemove: self.startRemove,
                // startTransfer: self.startTransfer,
                // student: self.student,
                // errors: self.errors
            };
        };
    };

    ko.applyBindings(groupsViewModel());
});