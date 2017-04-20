$(document).ready(function () {
    var sectionViewModel = function () {
        return new function () {
            var self = this;

            initializeViewModel.call(self, {page: ''});

            self.section = ko.observable({
                id: ko.observable(0),
                theme: ko.observable(0),
                name: ko.observable(''),
                content: ko.observable('')
            });

            self.getSection = function () {
             var currentUrl = window.location.href;
             var sectionId = +currentUrl.substr(currentUrl.lastIndexOf('/')+1);
             var url = '/api/sections/' + sectionId;

             $.get(url, function(response){
                var result = ko.mapping.fromJSON(response);
                if (result.Success()){
                    self.section(result.Data);
                    return;
                }
                self.errors.show(result.Message());
              });


             };

             self.getSection();

            return {
                section : self.section,
                getSection: self.getSection,
                errors: self.errors
            }
        };
    };

    ko.applyBindings(sectionViewModel());
});
