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

            self.getInstitutes = function(){

                $.get('/api/institutes', function(response){
                    var result = ko.mapping.fromJSON(response);
                    if (result.Success()){
                        self.institutes(result.Data());
                        return
                    }
                    self.errors.show(result.Message());
                });
            };
            self.getInstitutes();

            self.getProfiles = function(data){
                if (self.currentInstitute() === data.id()){
                    self.currentInstitute(0);
                    return;
                }
                self.currentInstitute(data.id());
                $.get('/api/institute/'+ data.id() + '/profiles', function(response){
                    var result = ko.mapping.fromJSON(response);
                    if (result.Success()){
                        self.currentProfiles(result.Data());
                        return
                    }
                    self.errors.show(result.Message());
                });
            };
            self.getPlans = function(data){
                $.get('/api/profile/'+ data.id() + '/plans', function(response){
                    var result = ko.mapping.fromJSON(response);
                    if (result.Success()){
                        self.currentPlans(result.Data());
                        return
                    }
                    self.errors.show(result.Message());
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
                moveToPlan: self.moveToPlan,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(institutesViewModel());
});