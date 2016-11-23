$(document).ready(function () {
    var studyplansViewModel = function () {
        return new function () {
            var self = this;

            self.studyplans = ko.observableArray([]);
            self.current = {
                studyplan: ko.observable({
                    id: ko.observable(0),
                    name: ko.observable(''),
                    profile: ko.observable(0)
                }),
                profile: ko.observable({
                    profiles: ko.observableArray([])
                })
            };
            self.filter = {
                profile : ko.observable()
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


            self.get = {
                studyplans: function (profileId) {
                    var filter = self.filter;
                    var profile = (filter.profile() ? filter.profile().id() : '');
                    var url = '/api/plan/profile/' + profile;

                    $.get(url, function (response) {
                        var result = ko.mapping.fromJSON(response);
                        self.studyplans(result());
                    });
                },
                profiles: function () {
                    $.get('/api/profiles', function (response) {
                        self.current.profile().profiles(ko.mapping.fromJSON(response)());
                    });
                }
            };

            self.get.profiles();

            self.move = function (data) {
                window.location.href = '/admin/studyplan/' + data.id();
            };


            self.filter.profile.subscribe(function(){
                self.get.studyplans();
            });


            return {
                studyplans : self.studyplans,
                current: self.current,
                filter: self.filter,
                get: self.get,
                move: self.move,
                errors: self.errors
            }
        };
    };

    ko.applyBindings(studyplansViewModel());
});
