@extends('shared.layout')
@section('title', 'Index')
@section('javascript')
    <script src="{{ URL::asset('js/admin/groups.js')}}"></script>
@endsection

@section('content')
<div class="content">
    <div class="institutes">
        <div class="institute" data-bind="click: $root.addGroup">
            <span class="fa">&#xf067;</span>
        </div>
        <!-- ko if: $root.mode() === 'add' -->
        <div class="info-group" data-bind="template: {name: 'edit-mode', data: $root.currentGroup}"></div>
        <!-- /ko -->
        <!-- ko foreach: groups -->
        <div class="institute font-settings" data-bind="click: $root.showGroup, css: {'institute-current': $root.currentGroup().id() === id()}">
            <span data-bind="text: name"></span>
        </div>
        <!-- ko if: $root.currentGroup().id() === id() && ($root.mode() === 'info' || $root.mode() === 'edit')-->
        <div data-bind="template: {name: 'group-info', data: $root.currentGroup}"></div>
        <div>
            <table class="students-table">
                <thead>
                <tr>
                    <th>№</th>
                    <th>ФИО студента</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                <!-- ko foreach: $root.currentGroupStudents-->
                <tr>
                    <td data-bind="text: ($index() + 1)"></td>
                    <td>
                        <span data-bind="text: lastName"></span>&nbsp;
                        <span data-bind="text: firstName"></span>&nbsp;
                        <span data-bind="text: patronymic"></span>&nbsp;
                    </td>
                    <td>
                        <button class="fa">&#xf0ec;</button>
                        <button class="fa">&#xf040;</button>
                        <button class="fa">&#xf014;</button>
                    </td>
                </tr>
                <!-- /ko -->
                </tbody>
            </table>
        </div>
        <!-- /ko -->
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
                <button data-bind="click: $root.cancel" class="fa">&#xf00d;</button>
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
                <button class="fa arcticmodal-close">&#xf00d;</button>
            </div>
        </div>
    </div>
</div>
<script type="text/html" id="group-info">
    <div class="info-group">
        <!-- ko if: $root.mode() === 'edit'-->
        {{--<div class="edit-group">--}}
            <div data-bind="template: {name: 'edit-mode', data: $root.currentGroup}"></div>
        {{--</div>--}}
        <!-- /ko -->
        <!-- ko if: $root.mode() === 'info' -->
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
            <button data-bind="click: $root.deleteGroup" class="fa">&#xf014;</button>
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
            <button data-bind="click: $root.cancel" class="fa">&#xf00d;</button>
        </div>
    </div>
</script>
@endsection