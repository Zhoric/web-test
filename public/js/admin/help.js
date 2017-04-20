$(document).ready(function(){
    var helpViewModel = function(){
        return new function(){
            var self = this;

            initializeViewModel.call(self, {
                page: menu.admin.main
            });

            self.current = {
                image: ko.observable()
            };

            self.actions = {
                expand: function(data, e){
                    self.current.image($(e.currentTarget).attr('src'));
                    $('.image-expander').fadeIn();
                    wheelzoom(document.querySelector('img.zoom'));
                },
                hide: function () {
                    $('.image-expander').fadeOut();
                    document.querySelector('img.zoom')
                        .dispatchEvent(new CustomEvent('wheelzoom.destroy'));
                }
            };
            
            return returnStandart.call(self);
        };
    };

    ko.applyBindings(helpViewModel());
});