$(document).ready(function(){
    commonHelper.tooltip({selector: '.tagged', side: 'left'});
    var settingViewModel = function(){
        return new function(){
            var self = this;

            initializeViewModel.call(self, {
                page: menu.admin.main
            });
            self.modals = {
                removeResults: '#remove-test-results-modal'
            };
            self.initialSettings = null;
            self.current = {
                resultsDate: ko.observable(new Date(Date.now())),
                settings: ko.validatedObservable({
                    cacheExpiration: ko.observable().extend({
                        required: true,
                        maxLength: 20
                    }),
                    firstSemesterMonth: ko.observable().extend({
                        required: true,
                        digit: true,
                        min: 1, max: 12
                    }),
                    maxMarkValue: ko.observable().extend({
                        required: true,
                        digit: true,
                        min: 1, max: 1000
                    }),
                    complexQuestionPoints: ko.observable().extend({
                        required: true,
                        number: true,
                        min: 1, max: 10
                    }),
                    questionEndTolerance: ko.observable().extend({
                        required: true,
                        digit: true,
                        min: 0, max: 1000
                    }),
                    secondSemesterMonth: ko.observable().extend({
                        required: true,
                        digit: true,
                        min: 1, max: 12
                    }),
                    testEndTolerance: ko.observable().extend({
                        required: true,
                        digit: true,
                        min: 0, max: 5000
                    }),
                    testSessionTrackingCacheExpiration: ko.observable().extend({
                        required: true,
                        maxLength: 20
                    })
                }),
                editSettingsAllowed: ko.observable(false)
            };

            self.alter = {
                settings: function(data){
                    var settings = self.current.settings();
                    for (var prop in settings){
                        settings[prop](data[prop]());
                    }
                }
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
                },
                settings: {
                    allow: function(){
                        self.current.editSettingsAllowed(true);
                    },
                    save: function(){
                        self.current.settings.isValid()
                            ? self.post.settings()
                            : self.validation[$('[accept-validation]').attr('id')].open();
                    },
                    default: function(){
                        self.get.default();
                    },
                    cancel: function(){
                        self.alter.settings(self.initialSettings);
                        $('input.tooltipstered').focus();
                        self.current.editSettingsAllowed(false);

                    }
                }
            };

            self.get = {
                settings: function(){
                    $ajaxget({
                        url: '/api/settings/getAll',
                        errors: self.errors,
                        successCallback: function(data){
                            self.initialSettings = data;
                            self.actions.settings.cancel();
                        }
                    })
                },
                default: function(){
                    $ajaxget({
                        url: '/api/settings/getDefaults',
                        errors: self.errors,
                        successCallback: function(data){
                            self.initialSettings = data;
                            self.alter.settings(data);
                        }
                    });
                }
            };
            commonHelper.buildValidationList(self.validation);
            self.get.settings();

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
                },
                settings: function(){
                    $ajaxpost({
                        url: '/api/settings/set',
                        data: JSON.stringify({settings: ko.mapping.toJS(self.current.settings)}),
                        errors: self.errors,
                        successCallback: self.get.settings,
                        errorCallback: self.get.settings
                    });
                }
            };
            
            return returnStandart.call(self);
        };
    };

    ko.applyBindings(settingViewModel());
});