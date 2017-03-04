$(document).ready(function(){
    var disciplineTestsViewModel = function(){
        return new function(){
            var self = this;

            initializeViewModel.call(self, {
                page: menu.student.main
            });

            self.modals = {
                messageBox: '#confirm-test-start-modal'
            };

            self.current = {
                discipline: {
                    id: ko.observable(),
                    name: ko.observable()
                },
                test: null,
                tests: ko.observableArray([])
            };

            self.filter = {
                test: ko.observable(''),
                clear: function(){}
            };

            self.actions = {
                start: function(data){
                    self.current.test = data;
                    commonHelper.modal.open(self.modals.messageBox);
                },
                end: function(){
                    var data = self.current.test;
                    commonHelper.cookies.create({
                        testId: data.test.id(),
                        testName: data.test.subject(),
                        disciplineName: data.test.disciplineName(),
                        testType: data.test.type()
                    });
                    window.location.href = '/test';
                }
            };

            self.get = {
                discipline: function(){
                    var url = window.location.href;
                    var id = +url.substr(url.lastIndexOf('/')+1);

                    $ajaxget({
                        url: '/api/disciplines/' + id,
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.discipline.id(data.id());
                            self.current.discipline.name(data.name());
                            self.get.tests();
                        }
                    });
                },
                tests: function(){
                    $ajaxget({
                        url: '/api/tests/showForStudent?discipline=' + self.current.discipline.id(),
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.tests(data());
                        }
                    });
                }
            };
            self.get.discipline();

            return returnStandart.call(self);
        };
    };

    ko.applyBindings(disciplineTestsViewModel());
});