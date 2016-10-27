@extends('shared.layout')
@section('title', 'Группы')
@section('javascript')
    <script src="{{ URL::asset('js/admin/groups.js')}}"></script>
@endsection

@section('content')
<div class="content">
    <div class="filter">
        <div>
            <label>Название группы </label>
            <input type="text" data-bind="value: $root.filter().group, valueUpdate: 'keyup'">
        </div>
    </div>
    <div class="institutes">
        <div class="institute" data-bind="click: $root.addGroup">
            <span class="fa">&#xf067;</span>
        </div>
        <!-- ko if: $root.mode() === 'add' -->
        <div class="info-group" data-bind="template: {name: 'edit-mode', data: $root.current().group}"></div>
        <!-- /ko -->
        <!-- ko foreach: groups -->
        <div class="institute font-settings" data-bind="click: $root.showGroup, css: {'institute-current': $root.current().group().id === id}">
            <span data-bind="text: name"></span>
        </div>
        <!-- ko if: $root.current().group().id() === id() && ($root.mode() === 'info' || $root.mode() === 'edit' || $root.mode() === 'edit-student')-->
        <div data-bind="template: {name: 'group-info', data: $root.current().group}"></div>
        <div>
            <!-- ko if: $root.mode() === 'info' || $root.mode() === 'edit-student' || $root.mode() === 'edit' -->
            <table class="students-table">
                <thead>
                <tr>
                    <th>№</th>
                    <th>ФИО студента</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                <!-- ko foreach: $root.current().groupStudents-->
                    <!-- ko if: $root.current().student().id() === id() && $root.mode() === 'edit-student'-->
                    <tr data-bind="template: {name: 'edit-student-mode', data: $root.current().student()}"></tr>
                    <!-- /ko -->
                    <!-- ko if: ($root.current().student().id() != id() && $root.mode() === 'edit-student') || ($root.mode() === 'info') || ($root.mode() === 'edit')-->
                    <tr>
                        <td data-bind="text: ($index() + 1)"></td>
                        <td>
                            <span data-bind="text: lastName"></span>&nbsp;
                            <span data-bind="text: firstName"></span>&nbsp;
                            <span data-bind="text: patronymic"></span>&nbsp;
                        </td>
                        <td>
                            <button class="fa" data-bind="click: $root.startTransfer">&#xf0ec;</button>
                            <button class="fa" data-bind="click: $root.student().edit">&#xf040;</button>
                            <button class="fa danger" data-bind="click: $root.startRemove">&#xf014;</button>
                        </td>
                    </tr>
                    <!-- /ko -->
                <!-- /ko -->
                </tbody>
            </table>
            <!-- /ko -->
        </div>
        <!-- /ko -->
        <!-- /ko -->
    </div>
    <div class="pager-wrap">
        <!-- ko if: ($root.pagination().totalPages()) > 0 -->
        <div class="pager">
            <!-- ko ifnot: $root.pagination().currentPage() == 1 -->
            <button class="" data-bind="click: $root.pagination().selectPage.bind($data, 1)">&lsaquo;&lsaquo;</button>
            <button class="" data-bind="click: $root.pagination().selectPage.bind($data, (currentPage() - 1))">&lsaquo;</button>
            <!-- /ko -->
            <!-- ko foreach: new Array($root.pagination().totalPages()) -->
            <span data-bind="visible: $root.pagination().dotsVisible($index() + 1)">...</span>
            <button class="" data-bind="click: $root.pagination().selectPage.bind($data, ($index()+1)), text: ($index()+1), visible: $root.pagination().pageNumberVisible($index() + 1), css: {current: ($index() + 1) == $root.pagination().currentPage()}"></button>
            <!-- /ko -->
            <!-- ko ifnot: $root.pagination().currentPage() == $root.pagination().totalPages() -->
            <button class="" data-bind="click: $root.pagination().selectPage.bind($data, ($root.pagination().currentPage() + 1))">&rsaquo;</button>
            <button class="" data-bind="click: $root.pagination().selectPage.bind($data, $root.pagination().totalPages())">&rsaquo;&rsaquo;</button>
            <!-- /ko -->
        </div>
        <!-- /ko -->
    </div>
</div>

<div class="g-hidden">
    <div class="box-modal" id="delete-group-modal">
        {{--<div class="box-modal_close arcticmodal-close">закрыть</div>--}}
        <div>
            <div><span>Удалить выбранную группу?</span></div>
            <div>
                <button data-bind="click: $root.approve" class="fa">&#xf00c;</button>
                <button data-bind="click: $root.cancel" class="fa danger">&#xf00d;</button>
            </div>
        </div>
    </div>
</div>
<div class="g-hidden">
    <div class="box-modal" id="delete-student-modal">
        {{--<div class="box-modal_close arcticmodal-close">закрыть</div>--}}
        <div>
            <div><span>Удалить выбранного студента?</span></div>
            <div>
                <button data-bind="click: $root.student().delete" class="fa">&#xf00c;</button>
                <button data-bind="click: $root.student().cancel" class="fa danger">&#xf00d;</button>
            </div>
        </div>
    </div>
</div>
<div class="g-hidden">
    <div class="box-modal" id="transfer-student-modal">
        {{--<div class="box-modal_close arcticmodal-close">закрыть</div>--}}
        <div>
            <div><span>Выберите группу для перевода студента:</span></div>
            <div>
                <!--<select data-bind="options: function(item) {
                       return item.countryName + ' (pop: ' + item.countryPopulation + ')'
                   }
\"></select> -->
            </div>
            <div>
                <button data-bind="click: $root.student().transfer" class="fa">&#xf00c;</button>
                <button data-bind="click: $root.student().cancel" class="fa danger">&#xf00d;</button>
            </div>

        </div>
    </div>
</div>

<div class="g-hidden">
    <div class="box-modal" id="select-plan-modal">
        {{--<div class="box-modal_close arcticmodal-close">закрыть</div>--}}
        <div>
            <h3>Учебный план</h3>
            <div>
                <select data-bind="options: $root.institutes,
                       optionsText: 'name',
                       value: $root.studyplanSelect().institute,
                       optionsCaption: 'Институт'"></select>
                <select data-bind="options: $root.profiles,
                       optionsText: 'name',
                       value: $root.studyplanSelect().profile,
                       optionsCaption: 'Направление подготовки',
                       enable: $root.studyplanSelect().institute()"></select>
                <select data-bind="options: $root.studyplans,
                       optionsText: 'name',
                       value: $root.studyplanSelect().studyplan,
                       optionsCaption: 'Учебный план',
                       enable: $root.studyplanSelect().institute() && $root.studyplanSelect().profile()"></select>
            </div>
            <div>
                <button data-bind="click: $root.approveStudyPlan, enable: $root.studyplanSelect().studyplan()" class="fa">&#xf00c;</button>
                <button class="fa arcticmodal-close danger">&#xf00d;</button>
            </div>
        </div>
    </div>
</div>
<script type="text/html" id="group-info">
    <div class="info-group">

        <!-- ko if: $root.mode() === 'edit'-->
        {{--<div class="edit-group">--}}
            <div data-bind="template: {name: 'edit-mode', data: $root.current().group}"></div>
        {{--</div>--}}
        <!-- /ko -->
        <!-- ko if: $root.mode() === 'info' || $root.mode() == 'edit-student' -->
        <div class="details-group">
            <div>
                <label>Название группы</label></br>
                <span data-bind="text: name"></span>
            </div>
            <div>
                <label>Учебный план</label></br>
                <span data-bind="text: studyplan"></span>
            </div>
            <button data-bind="click: $root.editGroup" class="fa">&#xf040;</button>
            <button data-bind="click: $root.deleteGroup" class="fa danger">&#xf014;</button>
        </div>
        <!-- /ko -->
    </div>
</script>
<script type="text/html" id="edit-mode">
    <div class="edit-group">
        <div>
            <label>Префикс</label></br>
            <input type="text" data-bind="value: prefix">
        </div>
        <div>
            <label>Курс</label></br>
            <input type="text" data-bind="value: course">
        </div>
        <div>
            <label>Номер группы</label></br>
            <input type="text" data-bind="value: number">
        </div>
        <div>
            <label>Форма обучения</label></br>
            <span data-bind="click: function(){$data.isFullTime(true);}, css: {'study-form-selected': isFullTime()}" class="study-form">Очная</span>
            <span> | </span>
            <span data-bind="click: function(){$data.isFullTime(false);}, css: {'study-form-selected': !isFullTime()}" class="study-form">Заочная</span>
        </div>
        <div>
            <label>Учебный план</label></br>
            <span class="study-form-selected" data-bind="text: studyplan, click: $root.selectStudyPlan"></span>
        </div>
        <div class="group-name">
            <label>Название группы </label>
            <a class="active-link" data-bind="click: $root.generateGroupName"> (Сгенерировать)</a></br>
            <input type="text" data-bind="value: name">
        </div>
        <div>
            <button data-bind="click: $root.approve" class="fa">&#xf00c;</button>
            <button data-bind="click: $root.cancel" class="fa danger">&#xf00d;</button>
        </div>
    </div>
</script>
<script type="text/html" id="edit-student-mode">
    <td></td>
    <td>
        <input type="text" data-bind="value: lastName">
        <input type="text" data-bind="value: firstName">
        <input type="text" data-bind="value: patronymic">
    </td>
    <td>
        <button data-bind="click: $root.student().approve" class="fa">&#xf00c;</button>
        <button data-bind="click: $root.student().cancel" class="fa danger">&#xf00d;</button>
    </td>
</script>
@endsection