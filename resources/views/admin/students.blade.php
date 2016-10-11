@extends('shared.layout')
@section('title', 'Index')
@section('javascript')
    <script src="{{ URL::asset('js/admin/students.js')}}"></script>
@endsection

@section('content')
    <div class="content">
        <div>
        ﻿﻿<div class="pager-wrap">
            <!-- ko if: ($root.totalPages()) > 0 -->
            <div class="pager">
                <!-- ko ifnot: $root.currentPage() == 1 -->
                <button class="" data-bind="click: $root.selectPage.bind($data, 1)">&lsaquo;&lsaquo;</button>
                <button class="" data-bind="click: $root.selectPage.bind($data, ($root.currentPage() - 1))">&lsaquo;</button>
                <!-- /ko -->
                <!-- ko foreach: new Array($root.totalPages()) -->
                <span data-bind="visible: $root.dotsVisible($index() + 1)">...</span>
                <button class="" data-bind="click: $root.selectPage.bind($data, ($index()+1)), text: ($index()+1), visible: $root.pageNumberVisible($index() + 1), css: {current: ($index() + 1) == $root.currentPage()}"></button>
                <!-- /ko -->
                <!-- ko ifnot: $root.currentPage() == $root.totalPages() -->
                <button class="" data-bind="click: $root.selectPage.bind($data, ($root.currentPage() + 1))">&rsaquo;</button>
                <button class="" data-bind="click: $root.selectPage.bind($data, $root.totalPages())">&rsaquo;&rsaquo;</button>
                <!-- /ko -->
            </div>
            <!-- /ko -->
            </div>
        </div>
    </div>
@endsection

<script type="text/html" id="student">

</script>

<script type="text/html" id="edit-student">

</script>