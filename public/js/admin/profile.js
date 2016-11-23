/**
 * Created by nyanjii on 29.09.16.
 */
$(document).ready(function() {
    var profileViewModel = function () {
        return new function () {
            var self = this;

            self.profiles = ko.observableArray([]);
            self.currentProfile = ko.observable("");
            self.currentProfile = ko.observable({
                id: ko.observable(0),
                name: ko.observable(''),
                groups: ko.observableArray([])
            });

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

            self.getProfiles = function () {
                $.get("/getProfiles", function (data) {
                    var res = ko.mapping.fromJSON(data);
                    self.profiles(res());
                    self.changeCurrentProfile(self.profiles()[0]);
                });
            };
            self.getProfiles();

            self.changeCurrentProfile = function (data) {
                console.log(data);
                self.currentProfile()
                    .id(data.id())
                    .name(data.name())
                    .groups(data.groups());
            };

            return {
                profiles: self.profiles,
                currentProfile: self.currentProfile,
                getProfiles: self.getProfiles,
                changeCurrentProfile: self.changeCurrentProfile,
                printData: self.printData,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(profileViewModel());
});