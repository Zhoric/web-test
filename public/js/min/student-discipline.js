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

            self.initial = {
                tests: ko.observableArray([])
            };

            self.filter = {
                test: ko.observable(''),
                type: ko.observable(attemptsStatus.all),
                clear: function(){
                    self.filter.test('')
                        .type(attemptsStatus.all);
                }
            };

            self.current = {
                discipline: {
                    id: ko.observable(),
                    name: ko.observable()
                },
                test: null,
                tests: ko.computed(function() {
                    var initial = self.initial.tests();
                    var name = self.filter.test().toLowerCase();
                    return ko.utils.arrayFilter(initial, function(item) {
                        var type = false;
                        switch(self.filter.type()){
                            case attemptsStatus.all:
                                type = true;
                                break;
                            case attemptsStatus.some:
                                type = (item.attemptsLeft() > 0) && (item.attemptsMade() > 0);
                                break;
                            case attemptsStatus.left:
                                type = item.attemptsMade() === 0;
                                break;
                            case attemptsStatus.none:
                                type = item.attemptsLeft() === 0;
                                break;
                        }
                        return item.test.subject().toLowerCase().includes(name) && type;
                    });
                })
            };

            self.actions = {
                start: function(data){
                    self.confirm.show({
                        message: 'Вы уверены, что хотите пройти выбранный тест?',
                        additionalHtml: '<p><span class="bold">Предупреждение: </span>' +
                        'Во время прохождения теста перезагрузка или переход на другую страницу приведёт к тому, ' +
                        'что текущая попытка прохождения теста будет считаться израсходованной.</p>',
                        approve: function(){
                            commonHelper.cookies.create({
                                testId: data.test.id(),
                                testName: data.test.subject(),
                                disciplineName: data.test.disciplineName(),
                                testType: data.test.type()
                            });
                            window.location.href = '/test';
                        }
                    });
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
                            self.initial.tests(data());
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
//# sourceMappingURL=student-discipline.js.map
