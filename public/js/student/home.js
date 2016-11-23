/**
 * Created by nyanjii on 28.10.16.
 */
$(document).ready(function(){

    var homeViewModel = function(){
        return new function(){
            var self = this;

            self.current = {
                user: ko.observable(),
                disciplineId: ko.observable(0),
                disciplines: ko.observableArray([]),
                tests: ko.observableArray([]),
                rows: ko.observableArray([]),
                rowId: ko.observable(0)
            };
            self.mode = ko.observable('none');
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

            self.actions = {
                disciplineDetails: function(parent, data){
                    var c = self.current;
                    if (c.disciplineId() === data.id()){
                        self.current.disciplineId(0);
                        self.current.rowId(0);
                        self.mode('none');
                        self.current.tests([]);
                        return;
                    }
                    self.mode('details');
                    self.current.disciplineId(data.id());
                    self.current.rowId(parent.rowId());
                    console.log('web');
                    self.get.tests();
                },
                startTest: function(data){
                    window.location.href = '/test/' + data.id();
                },
                splitDisciplinesByRows: function(){
                    var row = [];
                    var rowCounter = 1;
                    self.current.disciplines().find(function(item, i){
                        row.push(item);
                        if ((i+1) % 4 === 0){
                            self.current.rows.push({
                                rowId: ko.observable(rowCounter++),
                                disciplines: ko.observableArray(row)
                            });
                            row = [];
                        }
                    });
                    if (row.length){
                        self.current.rows.push({
                            rowId: ko.observable(rowCounter),
                            disciplines: ko.observableArray(row)
                        });
                    }
                },
                logout: function(){
                    window.location.href = '/logout';
                }
            };

            self.get = {
                disciplines: function(){
                    var url = '/api/disciplines/actual';
                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            self.current.disciplines(result.Data());
                            self.actions.splitDisciplinesByRows();
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                },
                tests: function(){
                    var url = '/api/tests/showForStudent?discipline=' + self.current.disciplineId();
                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        if (result.Success()){
                            self.current.tests(result.Data());
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                }
            };
            self.get.disciplines();

            self.toggleModal = function(selector, action){
                $(selector).arcticmodal(action);
            };

            return {
                current: self.current,
                actions: self.actions,
                mode: self.mode,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(homeViewModel());
});