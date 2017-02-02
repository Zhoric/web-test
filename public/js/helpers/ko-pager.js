/**
 * Created by nyanjii on 26.11.16.
 */
function pagination() {
    var self = this;

    self.currentPage = ko.observable(1);
    self.pageSize = ko.observable(3);
    self.itemsCount = ko.observable(1);
    self.totalPages = ko.observable(1);

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


    return {
        currentPage : self.currentPage,
        pageSize: self.pageSize,
        itemsCount: self.itemsCount,
        totalPages: self.totalPages,
        selectPage: self.selectPage,
        dotsVisible: self.dotsVisible,
        pageNumberVisible: self.pageNumberVisible
    };
};