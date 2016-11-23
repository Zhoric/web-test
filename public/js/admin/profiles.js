/**
 * Created by nyanjii on 12.10.16.
 */
$(document).ready(function(){
    var profilesViewModel = function(){
        return new function(){
            var self = this;

            self.profiles = ko.observableArray([]);
            self.currentProfile = ko.observable(0);
            self.currentGroups = ko.observableArray([]);
            self.currentPlan = ko.observable();
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

            self.getProfiles = function(){
                $.get('', function(data){
                    var res = ko.mapping.fromJSON(data);
                    self.profiles(res());
                });
            };
            self.getProfiles();

            self.getGroupsOfProfile = function(data){
                if (self.currentProfile() === data.id()){
                    self.currentProfile(0);
                    return;
                }
                self.currentProfile(data.id());
                $.get('' + data.id(), function(data){
                    var res = ko.mapping.fromJSON(data);
                    self.currentGroups(res());
                });
            };
            self.getPlanOfProfile = function(data){
                if (self.currentProfile() === data.id()){
                    self.currentProfile(0);
                    return;
                }
                self.currentProfile(data.id());
                $.get('' + data.id(), function(data){
                    var res = ko.mapping.fromJSON(data);
                    self.currentPlan(res());
                });
            };

            return {
                profiles: self.profiles,
                currentProfile: self.currentProfile,
                currentGroups: self.currentGroups,
                currentPlan: self.currentPlan,

                getGroupsOfProfile: self.getGroupsOfProfile,
                errors: self.errors
            };
        };
    };
    ko.applyBindings(profilesViewModel());
});