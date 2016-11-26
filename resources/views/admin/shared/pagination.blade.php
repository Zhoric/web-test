<!-- ko if: $root.pagination.itemsCount() > $root.pagination.pageSize() -->
<div class="pager-wrap">
    <!-- ko if: ($root.pagination.totalPages()) > 0 -->
    <div class="pager">
        <!-- ko ifnot: $root.pagination.currentPage() == 1 -->
        <button class="" data-bind="click: $root.pagination.selectPage.bind($data, 1)">&lsaquo;&lsaquo;</button>
        <button class="" data-bind="click: $root.pagination.selectPage.bind($data, ($root.pagination.currentPage() - 1))">&lsaquo;</button>
        <!-- /ko -->
        <!-- ko foreach: new Array($root.pagination.totalPages()) -->
        <span data-bind="visible: $root.pagination.dotsVisible($index() + 1)">...</span>
        <button class="" data-bind="click: $root.pagination.selectPage.bind($data, ($index()+1)), text: ($index()+1), visible: $root.pagination.pageNumberVisible($index() + 1), css: {current: ($index() + 1) == $root.pagination.currentPage()}"></button>
        <!-- /ko -->
        <!-- ko ifnot: $root.pagination.currentPage() == $root.pagination.totalPages() -->
        <button class="" data-bind="click: $root.pagination.selectPage.bind($data, ($root.pagination.currentPage() + 1))">&rsaquo;</button>
        <button class="" data-bind="click: $root.pagination.selectPage.bind($data, $root.pagination.totalPages())">&rsaquo;&rsaquo;</button>
        <!-- /ko -->
    </div>
    <!-- /ko -->
</div>
<!-- /ko -->




