$(document).ready(function () {
    var editorViewModel = function () {
        return new function () {
            var self = this;

            self.section = ko.observable({
                id: ko.observable(0),
                theme: ko.observable(0),
                name: ko.observable(''),
                content: ko.observable('')
            });
            self.mode = ko.observable('none');
            self.text = ko.observable('');


            self.getSection = function () {
                var currentUrl = window.location.href;
                var urlNewSection = currentUrl.substr(currentUrl.lastIndexOf('/') - 3, 3);
                if (urlNewSection != 'new') {
                    self.mode('edit');
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
                            self.section().theme(result.themeId())
                                .id(result.id())
                                .name(result.name())
                                .content(result.content());
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


           self.approve = function () {
               if(self.mode() === 'create') {
                   var section = {
                       name: self.section().name(),
                       content: CKEDITOR.instances.editor.getData()
                   };
                   var currentUrl = window.location.href;
                   var themeId = +currentUrl.substr(currentUrl.lastIndexOf('/') + 1);

                   var json = JSON.stringify({section: section, themeId: themeId});
                   var url = '/api/sections/create';

                   var xmlhttp = new XMLHttpRequest();
                   xmlhttp.open('POST', url, true);
                   xmlhttp.send(json);
                   xmlhttp.onreadystatechange = function () {
                       self.text("Секция была успешно создана!");
                       self.toggleModal('#cancel-modal', '');
                       setTimeout(function () {
                           window.location.href = '/admin/disciplines/';
                       }, 1500);
                   };
               }
               else if(self.mode() === 'edit'){
                   section = {
                       id: self.section().id(),
                       name: self.section().name(),
                       content: CKEDITOR.instances.editor.getData()
                   };
                   themeId = self.section().theme();
                   json = JSON.stringify({section: section, themeId: themeId});
                   url = '/api/sections/update';

                   xmlhttp = new XMLHttpRequest();
                   xmlhttp.open('POST', url, true);
                   xmlhttp.send(json);
                   xmlhttp.onreadystatechange = function () {
                       self.text("Секция была успешно изменена!");
                       self.toggleModal('#cancel-modal', '');
                       setTimeout(function () {
                           window.location.href = '/admin/disciplines/';
                       }, 1500);
                   };
               }

           };

           self.cancel = function () {
               window.location.href = '/admin/disciplines/';
           };


            return {
                section : self.section,
                approve: self.approve,
                text: self.text,
                cancel: self.cancel
            }
        };
    };

    ko.applyBindings(editorViewModel());
});
