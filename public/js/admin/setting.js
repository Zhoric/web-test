$(document).ready(function(){
    var settingViewModel = function(){
        return new function(){
            var self = this;

            initializeViewModel.call(self, {
                page: menu.admin.main
            });
            self.modals = {
                removeResults: '#remove-test-results-modal'
            };

            self.current = {
                resultsDate: ko.observable(new Date(Date.now()))
            };

            self.actions = {
                results: {
                    start: function(){
                        commonHelper.modal.open(self.modals.removeResults);
                    },
                    end: function(){
                        self.post.results();
                    },
                    cancel: function(){
                        commonHelper.modal.close(self.modals.removeResults);
                        self.current.resultsDate(new Date(Date.now()));
                    }
                }
            };

            self.get = {

            };

            self.post = {
                results: function(){
                    $ajaxpost({
                        url: '/api/results/deleteOld',
                        errors: self.errors,
                        data: JSON.stringify({
                            dateTime: commonHelper.parseDate(self.current.resultsDate())
                        }),
                        successCallback: function(){
                            self.actions.results.cancel();
                        },
                        errorCallback: function(){
                            self.actions.results.cancel();
                        }
                    });
                }
            };
            
            return returnStandart.call(self);
        };
    };

    ko.applyBindings(settingViewModel());
});