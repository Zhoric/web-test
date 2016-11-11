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
                }
            };

            self.get = {
                disciplines: function(){
                    var url = '/api/disciplines/actual';
                    $.get(url, function(response){
                        self.current.disciplines(ko.mapping.fromJSON(response)());
                        self.actions.splitDisciplinesByRows();
                    });
                },
                tests: function(){
                    console.log('getting tests');
                    var url = '/api/tests/showForStudent?discipline=' + self.current.disciplineId();
                    $.get(url, function(response){
                        self.current.tests(ko.mapping.fromJSON(response)());
                        console.log(self.current.tests());
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
                mode: self.mode
            };
        };
    };

    ko.applyBindings(homeViewModel());
});