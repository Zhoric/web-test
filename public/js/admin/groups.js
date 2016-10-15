/**
 * Created by nyanjii on 11.10.16.
 */
$(document).ready(function(){
    var groupsViewModel = function(){
        return new function(){
            var self = this;

            self.groups = ko.observableArray([]);
            self.currentGroupStudents = ko.observable([]);
            self.currentProfileId = ko.observable(0);
            self.currentGroup = ko.observable({
                id: ko.observable(0),
                name: ko.observable(''),
                prefix: ko.observable(''),
                number: ko.observable(0),
                isFullTime: ko.observable(false),
                course: ko.observable(0),
                studyplan: ko.observable(''),
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
                });


            };
            self.getGroups();

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
                    .number(0)
                    .isFullTime(false)
                    .course(0)
                    .studyplan('')
                    .studyplanId(0);
                self.mode('none');
            };

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


            self.changeForm = function(data, e){
                var form = $(e.target).text();
                self.groupStudyForm(form);
            };

            self.approve = function(){
                if (self.mode() === 'edit'){

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
                changeForm: self.changeForm,
                editGroup: self.editGroup,
                deleteGroup: self.deleteGroup,
                approve: self.approve,
                cancel: self.cancel,
                selectStudyPlan: self.selectStudyPlan,
                approveStudyPlan: self.approveStudyPlan
            };
        };
    };

    ko.applyBindings(groupsViewModel());
});