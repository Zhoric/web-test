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
            self.page = ko.observable(menu.admin.disciplines);
            self.errors = errors();
            self.user = new user();
            self.user.read(self.errors);
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


                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            self.section()
                                .id(result.Data.id())
                                .name(result.Data.name())
                                .content(result.Data.content())
                                .disciplineId(result.Data.disciplineId());

                            if (ko.isObservable(result.Data.theme))
                                self.theme(null);
                            else self.theme(result.Data.theme);
                            return;
                        }
                        self.errors.show(result.Message());
                    });

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

               $.post(url, json, function(response){
                   var result = ko.mapping.fromJSON(response);
                   if (result.Success()){
                       self.text("Раздел был успешно создан!");
                       self.toggleModal('#cancel-modal', '');
                       setTimeout(function () {
                           window.location.href = '/admin/disciplines/';
                       }, 1500);
                       return;
                   }
                   self.errors.show(result.Message());
               });

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
               var url = '/api/sections/update';

               $.post(url, json, function(response){
                   var result = ko.mapping.fromJSON(response);
                   if (result.Success()){
                       self.text("Раздел был успешно изменен!");
                       self.toggleModal('#cancel-modal', '');
                       setTimeout(function () {
                           window.location.href = '/admin/disciplines/';
                       }, 1500);
                       return;
                   }
                   self.errors.show(result.Message());
               });

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
                page: self.page,
                user: self.user,
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
