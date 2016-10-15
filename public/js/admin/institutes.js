/**
 * Created by nyanjii on 11.10.16.
 */
$(document).ready(function(){
    var institutesViewModel = function(){
        return new function(){
            var self = this;

            self.institutes = ko.observableArray([]);
            self.currentProfiles = ko.observableArray([]);
            self.currentProfile = ko.observable();
            self.currentPlans = ko.observableArray([]);
            self.currentInstitute = ko.observable(0);



            self.getInstitutes = function(){
                $.get('/api/org/institutes', function(data){
                    var res = ko.mapping.fromJSON(data);
                    self.institutes(res());
                });
            };
            self.getInstitutes();

            self.getProfiles = function(data){
                if (self.currentInstitute() === data.id()){
                    self.currentInstitute(0);
                    return;
                }
                self.currentInstitute(data.id());
                $.get('/api/org/institute/'+ data.id() + '/profiles', function(response){
                    var res = ko.mapping.fromJSON(response);
                    self.currentProfiles(res());
                });
            };
            self.getPlans = function(data){
                $.get('/api/org/profile/'+ data.id() + '/plans', function(response){
                    var res = ko.mapping.fromJSON(response);
                    self.currentPlans(res());
                });
            };

            self.moveToGroup = function(data){
                var url = 'groups/' + data.id();
                window.location.href = url;
            };
            self.moveToPlan = function(data){

            };
            self.showPlans = function(data){
                self.getPlans(data);
                $('#plans-modal').arcticmodal();
            };

            return {
                institutes: self.institutes,
                currentProfiles: self.currentProfiles,
                currentInstitute: self.currentInstitute,
                currentPlans: self.currentPlans,

                getProfiles: self.getProfiles,
                moveToGroup: self.moveToGroup,
                showPlans: self.showPlans,
                moveToPlan: self.moveToPlan
            };
        };
    };

    ko.applyBindings(institutesViewModel());
});