/**
 * Created by nyanjii on 11.10.16.
 */
$(document).ready(function(){
    var institutesViewModel = function(){
        return new function(){
            var self = this;

            self.institutes = ko.observableArray([]);
            self.currentProfiles = ko.observableArray([]);
            self.currentInstitute = ko.observable(0);

            self.getInstitutes = function(){
                $.get('/api/org/institutes', function(data){
                    var res = ko.mapping.fromJSON(data);
                    console.log(data);
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
                $.get('/api/org/profilesOf/'+ data.id(), function(response){
                    var res = ko.mapping.fromJSON(response);
                    self.currentProfiles(res());
                    console.log(response);
                });
            };

            return {
                institutes: self.institutes,
                currentProfiles: self.currentProfiles,
                currentInstitute: self.currentInstitute,

                getProfiles: self.getProfiles
            };
        };
    };

    ko.applyBindings(institutesViewModel());
});