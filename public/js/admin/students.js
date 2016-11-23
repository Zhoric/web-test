/**
 * Created by nyanjii on 02.10.16.
 */
$(document).ready(function(){
    var studentsViewModel = function(){
        return new function(){
            var self = this;

            self.students = ko.observableArray(new Array(10));


            self.currentPage = ko.observable(1);
            self.totalPages = ko.observable(20);
            self.onPageCount = ko.observable(10);
            self.itemsAmount = ko.observable(300);
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


            self.selectPage = function(page){
                self.currentPage(page);
            };
            self.dotsVisible = function(index){
                var total = self.totalPages();
                var current = self.currentPage();
                if (total > 11 && index == total-1 && index > current + 2  ||total > 11 && index == current - 1 && index > 3)  {
                    return true;
                }
                return false;
            };
            self.pageNumberVisible = function(index){

                var total = self.totalPages();
                var current = self.currentPage();
                if (total < 12 ||
                    index > (current - 2) && index < (current + 2) ||
                    index > total - 2 ||
                    index < 3) {
                    return true;
                }
                return false;
            };


            self.getStudents = function(){};

            self.getStudents = function(){};


            return {
                currentPage: self.currentPage,
                totalPages: self.totalPages,
                onPageCount: self.onPageCount,

                pageNumberVisible: self.pageNumberVisible,
                selectPage: self.selectPage,
                dotsVisible: self.dotsVisible,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(studentsViewModel());
});