/**
 * Created by nyanjii on 28.10.16.
 */
$(document).ready(function(){

    var homeViewModel = function(){
        return new function(){
            var self = this;

            self.errors = errors();

            self.current = {
                user: ko.observable(),
                disciplineId: ko.observable(0),
                disciplines: ko.observableArray([]),
                tests: ko.observableArray([]),
                rows: ko.observableArray([]),
                rowId: ko.observable(0)
            };

            self.filter = {
                name: ko.observable(),
                clear: function(){

                },
            };

            self.actions = {
                details: function(data){
                    window.location.href = '/discipline/' + data.id();
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
                            console.log(result);
                            self.current.disciplines(result.Data());
                            self.actions.splitDisciplinesByRows();
                            return;
                        }
                        self.errors.show(result.Message());
                    });
                }
            };
            self.get.disciplines();

            return {
                current: self.current,
                filter: self.filter,
                actions: self.actions,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(homeViewModel());
});