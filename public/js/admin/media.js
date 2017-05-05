$(document).ready(function () {
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
                var currentUrl = window.location.href;
                var urlParts = currentUrl.split('/');
                var mediaId = +urlParts[urlParts.length-1];
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

            self.initialize();
            self.getMedia();
        };
    };

    ko.applyBindings(mediaViewModel());
});
