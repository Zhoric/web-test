$(document).ready(function () {
    var editorViewModel = function () {
        return new function () {
            var self = this;

            self.section = ko.observable({
                id: ko.observable(0),
                name: ko.observable(''),
                content: ko.observable(''),
                disciplineId: ko.observable(0)
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
            self.theme = ko.observable(null);

            self.mode = ko.observable('none');
            self.text = ko.observable('');


            self.getSection = function () {
                var currentUrl = window.location.href;
                var urlParts = currentUrl.split('/');
                var urlNewSection = urlParts[urlParts.length - 3];

                if (urlNewSection != 'new') {
                    self.mode('edit');
                    var sectionId = +urlParts[urlParts.length-1];
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

                            self.section()
                                .id(result.id())
                                .name(result.name())
                                .content(result.content())
                                .disciplineId(result.disciplineId());

                            if (ko.isObservable(result.theme))
                                self.theme(null);
                            else self.theme(result.theme);
                        }
                    }
                }
                else{
                    self.mode('create');
                }
            };

           self.getSection();

           self.toggleModal = function(selector, action){
               $(selector).arcticmodal(action);
           };

           self.create = function () {
               var currentUrl = window.location.href;
               var urlParts = currentUrl.split('/');
               var disciplineId = +urlParts[urlParts.length - 2];
               var themeId = +urlParts[urlParts.length - 1];
               if (themeId === 0) themeId = null;

               var section = {
                   name: self.section().name(),
                   content: CKEDITOR.instances.editor.getData()
               };

               var json = JSON.stringify({section: section, themeId: themeId, disciplineId: disciplineId});
               var url = '/api/sections/create';

               var xmlhttp = new XMLHttpRequest();
               xmlhttp.open('POST', url, true);
               xmlhttp.send(json);
               xmlhttp.onreadystatechange = function () {
                   self.text("Раздел был успешно создан!");
                   self.toggleModal('#cancel-modal', '');
                   setTimeout(function () {
                       window.location.href = '/admin/disciplines/';
                   }, 1500);
               };
           };
           self.update = function () {
               var section = {
                   id: self.section().id(),
                   name: self.section().name(),
                   content: CKEDITOR.instances.editor.getData()
               };

               var disciplineId = self.section().disciplineId();

               if (self.theme() != null)
                   var themeId = self.theme().id();
               else themeId = null;


               var json = JSON.stringify({section: section, themeId: themeId, disciplineId: disciplineId});
               url = '/api/sections/update';

               xmlhttp = new XMLHttpRequest();
               xmlhttp.open('POST', url, true);
               xmlhttp.send(json);
               xmlhttp.onreadystatechange = function () {
                   self.text("Раздел был успешно изменен!");
                   self.toggleModal('#cancel-modal', '');
                   setTimeout(function () {
                       window.location.href = '/admin/disciplines/';
                   }, 1500);
               };
           };

           self.approve = function () {
               if(self.mode() === 'create') {
                   self.create();
               }
               else if(self.mode() === 'edit'){
                   self.update();
               }
           };

           self.cancel = function () {
               window.location.href = '/admin/disciplines/';
           };


            return {
                section : self.section,
                approve: self.approve,
                text: self.text,
                cancel: self.cancel,
                errors: self.errors
            }
        };
    };

    ko.applyBindings(editorViewModel());
});
