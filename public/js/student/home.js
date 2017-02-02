/**
 * Created by nyanjii on 28.10.16.
 */
$(document).ready(function(){

    var homeViewModel = function(){
        return new function(){
            var self = this;

            self.page = ko.observable(menu.student.main);
            self.errors = errors();

            self.current = {
                user: ko.observable(),
                disciplines: ko.observableArray([]),
                rows: ko.observableArray([])
            };

            self.filter = {
                name: ko.observable()
            };

            self.actions = {
                details: function(data){
                    window.location.href = '/discipline/' + data.discipline.id();
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
                }
            };
            self.get.disciplines();

            return {
                page: self.page,
                current: self.current,
                filter: self.filter,
                actions: self.actions,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(homeViewModel());
});