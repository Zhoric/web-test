@extends('shared.layout')
@section('title', 'Дисциплины')
@section('javascript')
    <script src="{{ URL::asset('js/admin/disciplines.js')}}"></script>
@endsection

@section('content')
<div class="content">
    <div class="filter">
        <div>
            <label>Название дисциплины</label></br>
            <input type="text" data-bind="value: $root.filter.discipline, valueUpdate: 'keyup'">
        </div>
        <div>
            <label>Направление</label></br>
            <select data-bind="options: $root.current.profile().profiles,
                       optionsText: 'name',
                       value: $root.filter.profile,
                       optionsCaption: 'Выберите профиль'"></select>
        </div>
    </div>
    <div class="org-accordion">
        <div data-bind="click: $root.csed.startAdd" class="org-item">
            <span class="fa">&#xf067;</span>
        </div>
        <!-- ko if: $root.mode() === 'add'-->
            <div data-bind="template: {name: 'show-details', data: $root.current.discipline}"></div>
        <!-- /ko -->
        <!-- ko foreach: disciplines -->
            <div class="org-item" data-bind="text: name, click: $root.csed.show"></div>
            <!-- ko if: $root.mode() !== 'none' && $data.id() === $root.current.discipline().id()-->
                <div data-bind="template: {name: 'show-details', data: $root.current.discipline}"></div>
            <!-- /ko -->
        <!-- /ko -->
    </div>
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
</div>

<div class="g-hidden">
    <div class="box-modal" id="delete-modal">
        <div>
            <div><span>Удалить выбранную дисциплину?</span></div>
            <div>
                <button data-bind="click: $root.csed.remove" class="fa">&#xf00c;</button>
                <button data-bind="click: $root.csed.cancel" class="fa danger arcticmodal-close">&#xf00d;</button>
            </div>
        </div>
    </div>
</div>
<div class="g-hidden">
    <div class="box-modal" id="remove-theme-modal">
        <div>
            <div><span>Удалить выбранную тему?</span></div>
            <div>
                <button data-bind="click: $root.csed.theme.remove" class="fa">&#xf00c;</button>
                <button class="fa danger arcticmodal-close">&#xf00d;</button>
            </div>
        </div>
    </div>
</div>
<div class="g-hidden">
    <div class="box-modal" id="add-theme-modal">
        <div>
            <div><span>Добавление темы</span></div>
            <div>
                <label>Название</label>
                <input type="text" data-bind="value: $root.current.theme().name">
            </div>
            <div>
                <button data-bind="click: $root.csed.theme.add" class="fa">&#xf00c;</button>
                <button class="fa danger arcticmodal-close">&#xf00d;</button>
            </div>
        </div>
    </div>
</div>

<script type="text/html" id="show-details">
    <div class="org-info">
        <!-- ko if: $root.mode() === 'info' || $root.mode() === 'delete' -->
        <div class="width100" data-bind="template: {name: 'info-mode', data: $data}"></div>
        <!-- /ko -->
        <!-- ko if: $root.mode() === 'edit' || $root.mode() === 'add'-->
        <div class="width100" data-bind="template: {name: 'edit-mode', data: $data}"></div>
        <!-- /ko -->
        <!-- ko if: $root.mode() !== 'add' -->
        <div class="width100">
            <table class="theme">
                <thead>
                <tr><th>#</th><th>Темы</th><th>Действия</th></tr>
                </thead>
                <tbody>
                <!-- ko foreach: $root.current.themes-->
                <tr>
                    <td data-bind="text: $index()+1"></td>
                    <td><a data-bind="text: name, click: $root.moveTo.theme"></a></td>
                    <td><button data-bind="click: $root.csed.theme.startRemove" class="fa danger">&#xf014;</button></td>
                </tr>
                <!-- /ko -->
                </tbody>
            </table>
        </div>
        <!-- /ko -->
    </div>
</script>
<script type="text/html" id="info-mode">
    <div class="org-info-details width100 discipline-info">
        <div>
            <label>Аббревиатура</label></br>
            <span data-bind="text: abbreviation"></span>
        </div>
        <div>
            <label>Полное название дисциплины</label></br>
            <span data-bind="text: name"></span>
        </div>
        <div>
        <i>
            <button class="move" data-bind="click: $root.csed.theme.startAdd"><span class="fa">&#xf067;</span>&nbsp;Добавить тему</button>
            <button class="move" data-bind="click: $root.moveTo.tests"><span class="fa">&#xf044;</span>&nbsp;Тесты</button>
        </i>
        <i>
            <button data-bind="click: $root.csed.startUpdate" class="fa">&#xf040;</button>
            <button data-bind="click: $root.csed.startRemove" class="fa danger">&#xf014;</button>
        </i>
        </div>
    </div>
</script>
<script type="text/html" id="edit-mode">
    <div class="org-info-edit width100 discipline-edit">
        <div>
            <label>Аббревиатура</label></br>
            <input type="text" data-bind="value: abbreviation">
        </div>
        <div>
            <label>Полное название дисциплины</label></br>
            <input type="text" data-bind="value: name">
        </div>
        <div>
            <label>Профили</label></br>
            <!-- ko with: $root.current.profile() -->
            <select data-bind="options: profiles, optionsText: 'fullname',  selectedOptions: selected" size="4" multiple="true"></select>
            <!-- /ko -->
        </div>
        <div class="float-btn-group">
            <button data-bind="click: $root.csed.update" class="fa">&#xf00c;</button>
            <button data-bind="click: $root.csed.cancel" class="fa danger">&#xf00d;</button>
        </div>
    </div>
</script>
@endsection