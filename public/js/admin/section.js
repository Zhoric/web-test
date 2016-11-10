$(document).ready(function () {
    var sectionViewModel = function () {
        return new function () {
            var self = this;

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


             var xmlhttp = new XMLHttpRequest();
             xmlhttp.open('GET', url, true);
             xmlhttp.send(null);
             xmlhttp.onreadystatechange = function() { // (3)
                 if (xmlhttp.readyState != 4) return;

                 if (xmlhttp.status != 200) {
                    alert(xmlhttp.status + ': ' + xmlhttp.statusText);
                 } else {
                 var result = ko.mapping.fromJSON(xmlhttp.responseText);
                     self.section(result);
                 }
             }
             };

             self.getSection();


            return {
                section : self.section,
                getSection: self.getSection
            }
        };
    };

    ko.applyBindings(sectionViewModel());
});
