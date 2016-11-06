/**
 * Created by nyanjii on 11.10.16.
 */
$(document).ready(function(){
    var groupsViewModel = function(){
        return new function(){
            var self = this;

            self.groups = ko.observableArray([]);
            self.groupStudyForm = ko.observable('Очная');
            self.mode = ko.observable('none');

            self.current = ko.observable({
                groupStudents : ko.observable([]),
                profileId : ko.observable(''),
                group: ko.observable({
                    id: ko.observable(0),
                    name: ko.observable(''),
                    prefix: ko.observable(''),
                    number: ko.observable(1),
                    isFullTime: ko.observable(true),
                    course: ko.observable(1),
                    studyplan: ko.observable('Указать'),
                    studyplanId: ko.observable(0)
                }),
                student: ko.observable({
                    id: ko.observable(0),
                    firstName: ko.observable(''),
                    lastName: ko.observable(''),
                    patronymic: ko.observable(''),
                    active: ko.observable(''),
                    email: ko.observable('')

                })

            });

            self.emptyCurrentGroup =  function () {
                    self.current().group()
                        .id(0)
                        .name('')
                        .prefix('')
                        .number(1)
                        .isFullTime(true)
                        .course(1)
                        .studyplan('Указать')
                        .studyplanId(0);
                    self.mode('none');
            };
            self.emptyCurrentStudent = function () {
                    self.current().student()
                        .id(0)
                        .firstName('')
                        .lastName('')
                        .patronymic('')
                        .email('')
                        .active('');

            };

            self.filter = ko.observable({
               group: ko.observable('')
            });
            self.pagination = ko.observable({
                currentPage: ko.observable(1),
                pageSize: ko.observable(10),
                itemsCount: ko.observable(1),
                totalPages: ko.observable(1),

                selectPage: function(page){
                    self.pagination().currentPage(page);
                    self.getGroups();
                },
                dotsVisible: function(index){
                    var total = self.pagination().totalPages();
                    var current = self.pagination().currentPage();
                    if (total > 11 && index == total-1 && index > current + 2  ||total > 11 && index == current - 1 && index > 3)  {
                        return true;
                    }
                    return false;
                },
                pageNumberVisible: function(index){
                    var total = self.pagination().totalPages();
                    var current = self.pagination().currentPage();
                    if (total < 12 ||
                        index > (c12urrent - 2) && index < (current + 2) ||
                        index > total - 2 ||
                        index < 3) {
                        return true;
                    }
                    return false;
                }
            });

            self.institutes = ko.observableArray([]);
            self.profiles = ko.observableArray([]);
            self.studyplans = ko.observableArray([]);

            self.studyplanSelect = ko.observable({
                institute: ko.observable(),
                profile: ko.observable(),
                studyplan: ko.observable()
            });
            self.studyplanSelect().institute.subscribe(function(data){
                if (data){
                    if (!self.studyplanSelect().profile() && !self.profiles().length){
                        $.get('/api/institute/' + data.id() + '/profiles', function(data){
                            self.profiles(ko.mapping.fromJSON(data)())
                        });
                    }
                }
            });
            self.studyplanSelect().profile.subscribe(function(data){
                if (data){
                    $.get('/api/profile/' + data.id() + '/plans', function(data){
                        self.studyplans(ko.mapping.fromJSON(data)());
                    });
                }
            });


            self.selectStudyPlan = function(){
                if (!self.studyplanSelect().institute() && !self.institutes().length) {
                    $.get('/api/institutes', function(data){
                        self.institutes(ko.mapping.fromJSON(data)())
                    });
                }
                self.toggleModal('#select-plan-modal', '');
            };
            self.approveStudyPlan = function(){
                var select = self.studyplanSelect();
                self.current().group()
                    .studyplan(select.studyplan().name())
                    .studyplanId(select.studyplan().id());
                select.studyplan(undefined).institute(undefined).profile(undefined);
                self.toggleModal('#select-plan-modal', 'close');
            };

            self.groupSelect = ko.observable();

            self.generateGroupName = function(){
                var group = self.current().group();
                var name = group.prefix() + '-' +
                    group.course() +
                    group.number();
                name += group.isFullTime() ? 'о' : 'з';
                group.name(name);
            };

            self.fillCurrentGroup = function(data, mode){
                console.log(data);
                self.current().group()
                    .id(data.id())
                    .name(data.name())
                    .prefix(data.prefix())
                    .number(data.number())
                    .isFullTime(data.isFullTime)
                    .course(data.course())
                    .studyplan(data.studyplan)
                    .studyplanId(data.studyplanId);
                self.mode(mode);
                self.getStudents();
            };


            self.getGroups = function(){
                var url = window.location.href;
                var filter = self.filter();
                var profileId = +url.substr(url.lastIndexOf('/')+1);
                var name = 'name=' + filter.group();
                var page = 'page=' + self.pagination().currentPage();
                var pageSize = 'pageSize=' + self.pagination().pageSize();
                var url = '/api/groups/show?' + page + '&' + pageSize + '&' + name + '&' + profileId;

                var xmlhttp = new XMLHttpRequest();
                xmlhttp.open('GET', url, true);
                xmlhttp.send(null);
                xmlhttp.onreadystatechange = function() { // (3)
                    if (xmlhttp.readyState != 4) return;

                    if (xmlhttp.status != 200) {
                        alert(xmlhttp.status + ': ' + xmlhttp.statusText);
                    } else {
                        var result = ko.mapping.fromJSON(xmlhttp.responseText);
                        self.groups(result.data());
                        self.pagination().itemsCount(result.count());
                    }

                }
            };
            self.getAlterGroups = function(){
                var url = window.location.href;
                var filter = self.filter();
                var profileId = +url.substr(url.lastIndexOf('/')+1);
                /*
                 url = url.substr(0, url.indexOf('/a'));
                 if ($.isNumeric(profileId)){
                 url += '/api/profile/' + profileId + '/groups';
                 self.currentProfileId(profileId);
                 }
                 else{
                 url += '/api/groups';
                 } */

                var name = 'name=' + filter.group();
                var page = 'page=' + self.pagination().currentPage();
                var pageSize = 'pageSize=' + self.pagination().pageSize();
                var url = '/api/groups/show?' + page + '&' + pageSize + '&' + name + '&' + profileId;

                $.get(url, function(response){
                    var result = ko.mapping.fromJSON(response);
                    self.groups(result.data());
                    self.pagination().itemsCount(result.count());
                });
            };
            self.getGroups();
            //self.getAlterGroups();

            self.getStudents = function(){
                var xmlhttp = new XMLHttpRequest();
                var url = '/api/groups/' + self.current().group().id() + '/students';
                xmlhttp.open('GET', url, true);
                xmlhttp.send(null);
                xmlhttp.onreadystatechange = function() { // (3)
                    if (xmlhttp.readyState != 4) return;

                    if (xmlhttp.status != 200) {
                        alert(xmlhttp.status + ': ' + xmlhttp.statusText);
                    } else {
                        var result = ko.mapping.fromJSON(xmlhttp.responseText);
                        self.current().groupStudents(result());
                    }

                }
            };
            self.getAlterStudents = function(){
                var url = '/api/groups/' + self.current().group().id() + '/students';
                $.get(url, function(data){
                    var res = ko.mapping.fromJSON(data);
                    self.current().groupStudents(res());
                });
            };

            self.startRemove = function(data) {
                self.toggleModal('#delete-student-modal', '');
                self.current().student(data);
            };
            self.startTransfer = function (data) {
                self.toggleModal('#transfer-student-modal', '');
                self.current().student(data);
            };


            self.addGroup = function(){
                self.emptyCurrentGroup();
                self.mode('add');
            };
            self.showGroup = function(data){
                (self.current().group().id() === data.id()) ?
                    self.emptyCurrentGroup() :
                    self.fillCurrentGroup(data, 'info');
            };
            self.editGroup = function(){
                self.mode('edit');
            };
            self.deleteGroup = function(){
                self.mode('delete');
                self.toggleModal('#delete-group-modal', '');
            };


            self.student = ko.observable({
                transfer: function () {
                    self.toggleModal('#transfer-student-modal', 'close');

                    $.post(
                        '/api/groups/student/setGroup',
                        JSON.stringify({studentId: self.current().student().id(), groupId: self.groupSelect().id()}),
                        function(result){
                            self.emptyCurrentStudent();
                            self.getStudents();
                        });


                },
                
                edit: function (data) {
                    self.current().student()
                        .id(data.id())
                        .firstName(data.firstName())
                        .lastName(data.lastName())
                        .patronymic(data.patronymic())
                        .email(data.email())
                        .active(data.active());

                    self.mode('edit-student');
                },
                
                delete: function () {
                    self.toggleModal('#delete-student-modal', 'close');
                    var url = '/api/groups/student/delete/' + self.current().student().id();

                    $.post(url, function(result){
                        self.emptyCurrentStudent();
                        self.getStudents();
                    });                    
                },
                
                cancel: function () {
                    if(self.mode() === 'edit-student'){
                        self.mode('info');
                    }
                },

                cancelDelete: function () {
                    self.toggleModal('#delete-student-modal', 'close');
                    //self.emptyCurrentStudent();
                },

                cancelTransfer: function () {
                    self.toggleModal('#transfer-student-modal', 'close');
                    //self.emptyCurrentStudent();
                },
                
                approve: function () {
                    var edit = self.current().student();

                    var student = {
                        id: edit.id(),
                        firstname: edit.firstName(),
                        lastname: edit.lastName(),
                        patronymic: edit.patronymic(),
                        active: edit.active(),
                        email: edit.email()
                    };

                    var url = '/api/groups/student/update';
                    var json = JSON.stringify({student: student});

                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.open('POST', url, true);
                    xmlhttp.send(json);
                    xmlhttp.onreadystatechange = function() {
                        self.getStudents();
                    };

                    // $.post(
                    //     '/api/groups/student/update',
                    //     JSON.stringify({student: student}),
                    //     function(result){
                    //         self.getStudents();
                    //     });

                    self.mode('info');
                }

            });

            self.approve = function(){
                var edit = self.current().group();
                if (self.mode() === 'delete'){
                    $.post(
                        '/api/groups/delete/' + edit.id(),
                        {},
                        function(result){});
                    self.groups.remove(function(item){
                        if (item.id() === edit.id())
                            return item;
                    });
                    self.toggleModal('#delete-group-modal', 'close');
                    self.emptyCurrentGroup();
                    self.getGroups();
                    return;
                }
                var group = {
                    prefix: edit.prefix(),
                    course: edit.course(),
                    name: edit.name(),
                    number: edit.number(),
                    isFulltime: edit.isFullTime()
                };
                var planId = edit.studyplanId();

                if (self.mode() === 'edit'){
                    group.id = edit.id();
                    $.post(
                        '/api/groups/update',
                        JSON.stringify({group: group, studyPlanId: planId}),
                        function(result){});
                    self.groups().find(function(item){
                        if (item.id() === edit.id()){
                            item.prefix(edit.prefix())
                                .course(edit.course())
                                .name(edit.name())
                                .number(edit.number())
                                .isFullTime(edit.isFullTime());
                            return item;
                        }
                    });
                    self.mode('info');
                    return;
                }
                if (self.mode() === 'add'){
                    var json = JSON.stringify({group: group, studyPlanId: planId});
                    var url = '/api/groups/create';

                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.open('POST', url, true);
                    xmlhttp.send(json);
                    xmlhttp.onreadystatechange = function() {
                        self.groups([]);
                        self.emptyCurrentGroup();
                        self.getGroups();
                    };

                    // $.post(
                    //     '/api/groups/create',
                    //     JSON.stringify({group: group, studyPlanId: planId}),
                    //     function(result){});
                    // self.groups([]);
                    // self.emptyCurrentGroup();
                    // self.getGroups();
                }


            };
            self.alterApprove = function(){
                var edit = self.current().group();
                if (self.mode() === 'delete'){
                    $.post(
                        '/api/groups/delete/' + edit.id(),
                        {},
                        function(result){});
                    self.groups.remove(function(item){
                        if (item.id() === edit.id())
                            return item;
                    });
                    self.toggleModal('#delete-group-modal', 'close');
                    self.emptyCurrentGroup();
                    self.getGroups();
                    return;
                }
                var group = {
                    prefix: edit.prefix(),
                    course: edit.course(),
                    name: edit.name(),
                    number: edit.number(),
                    isFulltime: edit.isFullTime()
                };
                var planId = edit.studyplanId();

                if (self.mode() === 'edit'){
                    group.id = edit.id();
                    $.post(
                        '/api/groups/update',
                        JSON.stringify({group: group, studyPlanId: planId}),
                        function(result){});
                    self.groups().find(function(item){
                        if (item.id() === edit.id()){
                            item.prefix(edit.prefix())
                                .course(edit.course())
                                .name(edit.name())
                                .number(edit.number())
                                .isFullTime(edit.isFullTime());
                            return item;
                        }
                    });
                    self.mode('info');
                    return;
                }
                if (self.mode() === 'add'){
                    $.post(
                        '/api/groups/create',
                        JSON.stringify({group: group, studyPlanId: planId}),
                        function(result){});
                    self.groups([]);
                    self.emptyCurrentGroup();
                    self.getGroups();
                }


            };
            self.cancel = function(){
                var m = self.mode();
                if (m === 'edit'){
                    self.mode('info');
                    return;
                }
                self.emptyCurrentGroup();
                self.toggleCurrentStudent();

                if (m === 'delete'){
                    $('#delete-group-modal').arcticmodal('close');
                }

            };

            self.toggleModal = function(selector, action){
                $(selector).arcticmodal(action);
            };


            self.pagination().itemsCount.subscribe(function(value){
                if (value){
                    self.pagination().totalPages(Math.ceil(
                        value/self.pagination().pageSize()
                    ));
                }
            });

            self.filter().group.subscribe(function(){
                self.getGroups();
            });

            return {
                groups: self.groups,
                current: self.current,
                mode: self.mode,
                groupStudyForm: self.groupStudyForm,
                pagination: self.pagination,
                filter: self.filter,

                institutes: self.institutes,
                profiles: self.profiles,
                studyplans: self.studyplans,
                studyplanSelect: self.studyplanSelect,
                groupSelect: self.groupSelect,

                showGroup: self.showGroup,
                addGroup: self.addGroup,
                editGroup: self.editGroup,
                deleteGroup: self.deleteGroup,
                approve: self.approve,
                cancel: self.cancel,
                selectStudyPlan: self.selectStudyPlan,
                approveStudyPlan: self.approveStudyPlan,
                generateGroupName: self.generateGroupName,

                startRemove: self.startRemove,
                startTransfer: self.startTransfer,
                student: self.student
            };
        };
    };

    ko.applyBindings(groupsViewModel());
});