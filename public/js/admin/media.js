$(document).ready(function () {
    var anchor;
    var mediaViewModel = function () {
        return new function () {
            var self = this;

            initializeViewModel.call(self, {
                page: menu.admin.materials,
                mode: true
            });

            self.media = ko.observable({
                id: ko.observable(0),
                type: ko.observable(''),
                content: ko.observable(''),
                path: ko.observable(''),
                name: ko.observable(''),
                hash: ko.observable('')
            });

            self.name = ko.observable('');
            self.anchor = ko.observable('');

            self.initialize = function () {
                $('body').addClass('media');
            };

            self.fill = function (data) {
                self.media()
                    .id(data.id())
                    .type(data.type())
                    .content(data.content())
                    .path(data.path())
                    .name(data.name())
                    .hash(data.hash());
            };

            self.getMedia = function () {
                var mediaId;
                var currentUrl = window.location.href;
                var urlParts = currentUrl.split('/');
                if (urlParts[urlParts.length-1].indexOf('#') != -1) {
                    mediaId = urlParts[urlParts.length-1].split('#')[0];
                    self.anchor(urlParts[urlParts.length-1].split('#')[1]);
                }
                else mediaId = +urlParts[urlParts.length-1];

                $ajaxget({
                    url: '/api/media/' + mediaId,
                    errors: self.errors,
                    successCallback: function(data){
                        self.fill(data);
                        self.name(data.name().substring(0, data.name().lastIndexOf('.')));
                        document.title = self.name();
                    }
                });
            };

            self.goToAnchor = function () {
                if (self.anchor().length > 0 && $('#' + self.anchor()).length) {
                   $('html, body').animate({
                       scrollTop: $('#' + self.anchor()).offset().top
                   }, 0);
                }

            };

            self.initialize();
            self.getMedia();

            ko.bindingHandlers.afterHtmlRender = {
                update: function(element, valueAccessor, allBindings){
                    allBindings().html && valueAccessor()(allBindings().html());
                }
            };

        };
    };

    ko.applyBindings(mediaViewModel());

});

