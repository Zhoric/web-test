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

<div class="g-hidden">
    <div class="box-modal" id="errors-modal">
        <div>
            <div>
                <span class="fa">&#xf071;</span>
                <h3>Произошла ошибка</h3>
                <h4 data-bind="text: $root.errors.message"></h4>
            </div>
            <div class="button-holder">
                <button data-bind="click: $root.errors.accept">OK</button>
            </div>
        </div>
    </div>
</div>