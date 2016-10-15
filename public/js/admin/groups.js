/**
 * Created by nyanjii on 11.10.16.
 */
$(document).ready(function(){
    var groupsViewModel = function(){
        return new function(){
            var self = this;

            self.groups = ko.observableArray([]);
            self.currentGroupStudents = ko.observable([]);
            self.currentProfileId = ko.observable('');
            self.currentGroup = ko.observable({
                id: ko.observable(0),
                name: ko.observable(''),
                prefix: ko.observable(''),
                number: ko.observable(1),
                isFullTime: ko.observable(true),
                course: ko.observable(1),
                studyplan: ko.observable('Указать'),
                studyplanId: ko.observable(0)
            });
            self.groupStudyForm = ko.observable('Очная');
            self.mode = ko.observable('none');

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

            self.toggleModal = function(selector, action){
                $(selector).arcticmodal(action);
            };

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
                self.currentGroup()
                    .studyplan(select.studyplan().name())
                    .studyplanId(select.studyplan().id());
                select.studyplan(undefined).institute(undefined).profile(undefined);
                self.toggleModal('#select-plan-modal', 'close');
            };

            self.generateGroupName = function(){
                var group = self.currentGroup();
                var name = group.prefix() + '-' +
                    group.course() +
                    group.number();
                name += group.isFullTime() ? 'о' : 'з';
                group.name(name);
            };

            self.fillCurrentGroup = function(data, mode){
                self.currentGroup()
                    .id(data.id())
                    .name(data.name())
                    .prefix(data.prefix())
                    .number(data.number())
                    .isFullTime(data.isFullTime())
                    .course(data.course())
                    .studyplan(data.studyplan())
                    .studyplanId(data.studyplanId());
                self.mode(mode);
                self.getStudents();
            };
            self.emptyCurrentGroup = function(){
                self.currentGroup()
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

            self.getGroups = function(){
                var url = window.location.href;
                var profileId = +url.substr(url.lastIndexOf('/')+1);
                url = url.substr(0, url.indexOf('/a'));
                if ($.isNumeric(profileId)){
                    url += '/api/profile/' + profileId + '/groups';
                    self.currentProfileId(profileId);
                }
                else{
                    url += '/api/groups';
                }

                $.get(url, function(data){
                    var res = ko.mapping.fromJSON(data);
                    self.groups(res());
                    console.log('loading groups');
                });


            };
            self.getGroups();



            self.getStudents = function(){
                var url = '/api/groups/' + self.currentGroup().id() + '/students';
                $.get(url, function(data){
                    var res = ko.mapping.fromJSON(data);
                    self.currentGroupStudents(res());
                });
            };


            self.addGroup = function(){
                self.emptyCurrentGroup();
                self.mode('add');
            };
            self.showGroup = function(data){
                (self.currentGroup().id() === data.id()) ?
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



            self.addStudent = function(){};
            self.editStudent = function(){};
            self.deleteStudent = function(){};

            self.approve = function(){
                var edit = self.currentGroup();
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
                if (m === 'delete'){
                    $('#delete-group-modal').arcticmodal('close');
                }
            };

            return {
                groups: self.groups,
                currentGroup: self.currentGroup,
                mode: self.mode,
                groupStudyForm: self.groupStudyForm,
                currentGroupStudents: self.currentGroupStudents,

                institutes: self.institutes,
                profiles: self.profiles,
                studyplans: self.studyplans,
                studyplanSelect: self.studyplanSelect,

                showGroup: self.showGroup,
                addGroup: self.addGroup,
                editGroup: self.editGroup,
                deleteGroup: self.deleteGroup,
                approve: self.approve,
                cancel: self.cancel,
                selectStudyPlan: self.selectStudyPlan,
                approveStudyPlan: self.approveStudyPlan,
                generateGroupName: self.generateGroupName
            };
        };
    };

    ko.applyBindings(groupsViewModel());
});