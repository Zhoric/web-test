@extends('shared.layout')
@section('title', 'Группы')
@section('javascript')
    <script src="{{ URL::asset('js/admin/groups.js')}}"></script>
@endsection

@section('content')
<div class="content">
    <div class="items">
        <div class="items-head">
            <h1>Администрирование групп</h1>
            <label class="adder">Добавить</label>
        </div>
        <div class="items-body" data-bind="foreach: $root.current.groups">
            <div class="item">
                <span data-bind="text: name"></span>
                <span class="fa tag float-right" title="Удалить группу">&#xf1f8;</span>
                <span class="fa tag float-right" data-bind="click: $root.actions.start.update" title="Редактировать">&#xf040;</span>
                <span class="fa tag float-right" title="Перейти к учетным записям студентов">&#xf007;</span>
            </div>
            <!-- ko if: id() === $root.current.group().id() -->
            <div class="details" data-bind="template: {name: 'show-group-info', data: $root.current.group}"></div>
            <!-- /ko -->
        </div>
        @include('shared.pagination')
    </div>
    <div class="filter">
        <div class="filter-block">
            <label class="title">Название группы </label>
            <input type="text" data-bind="value: $root.filter.name, valueUpdate: 'keyup'">
        </div>
    </div>

<script type="text/html" id="show-group-info">
    <div class="details-row">
        <div class="details-column width-45p">
            <div class="details-row">
                <div class="details-column width-50p">
                    <label class="title">Префикс</label>
                    <input type="text" data-bind="value: prefix"/>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-50p">
                    <label class="title">Курс</label>
                    <input type="text" data-bind="value: course"/>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-50p">
                    <label class="title">Номер группы</label>
                    <input type="text" data-bind="value: number"/>
                </div>
            </div>
        </div>
        <div class="details-column width-48p">
            <div class="details-row">
                <div class="details-column">
                    <label class="title">Форма обучения</label>
                    <span class="radio form-heights" data-bind="click: $root.actions.switchForm.day, css: {'radio-important' : isFulltime()}">Очная</span>
                    <span>|</span>
                    <span class="radio form-heights" data-bind="click: $root.actions.switchForm.night, css: {'radio-important' : !isFulltime()}">Заочная</span>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-70p">
                    <label class="title">
                        Полное наименование группы
                        <span>(Сгенерировать)</span>
                    </label>
                    <input type="text" data-bind="value: name"/>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-98p">
                    <label class="title">Учебный план</label>
                    <span class="form-heights">Изменить</span>

                </div>
            </div>
        </div>
    </div>
    <div class="details-row float-buttons">
        <div class="details-column width-100p">
            <button data-bind="click: $root.actions.cancel" class="cancel">Отмена</button>
            <button data-bind="click: $root.actions.end.update" class="approve">Сохранить</button>
        </div>
    </div>
</script>
    {{--<div class="institutes">--}}
        {{--<div class="institute" data-bind="click: $root.addGroup">--}}
            {{--<span class="fa">&#xf067;</span>--}}
        {{--</div>--}}
        {{--<!-- ko if: $root.mode() === 'add' -->--}}
        {{--<div class="info-group" data-bind="template: {name: 'edit-mode', data: $root.current().group}"></div>--}}
        {{--<!-- /ko -->--}}
        {{--<!-- ko foreach: groups -->--}}
        {{--<div class="institute font-settings" data-bind="click: $root.showGroup, css: {'institute-current': $root.current().group().id === id}">--}}
            {{--<span data-bind="text: name"></span>--}}
        {{--</div>--}}
        {{--<!-- ko if: $root.current().group().id() === id() && ($root.mode() === 'info' || $root.mode() === 'edit' || $root.mode() === 'edit-student')-->--}}
        {{--<div data-bind="template: {name: 'group-info', data: $root.current().group}"></div>--}}
        {{--<div>--}}
            {{--<!-- ko if: $root.mode() === 'info' || $root.mode() === 'edit-student' || $root.mode() === 'edit' -->--}}
            {{--<table class="students-table">--}}
                {{--<thead>--}}
                {{--<tr>--}}
                    {{--<th>№</th>--}}
                    {{--<th>ФИО студента</th>--}}
                    {{--<th>Действия</th>--}}
                {{--</tr>--}}
                {{--</thead>--}}
                {{--<tbody>--}}
                {{--<!-- ko foreach: $root.current().groupStudents-->--}}
                    {{--<!-- ko if: $root.current().student().id() === id() && $root.mode() === 'edit-student'-->--}}
                    {{--<tr data-bind="template: {name: 'edit-student-mode', data: $root.current().student()}"></tr>--}}
                    {{--<!-- /ko -->--}}
                    {{--<!-- ko if: ($root.current().student().id()!= id() && $root.mode() === 'edit-student') || ($root.mode() === 'info') || ($root.mode() === 'edit')-->--}}
                    {{--<tr>--}}
                        {{--<td data-bind="text: ($index() + 1)"></td>--}}
                        {{--<td>--}}
                            {{--<span data-bind="text: lastName"></span>&nbsp;--}}
                            {{--<span data-bind="text: firstName"></span>&nbsp;--}}
                            {{--<span data-bind="text: patronymic"></span>&nbsp;--}}
                        {{--</td>--}}
                        {{--<td>--}}
                            {{--<button class="fa" data-bind="click: $root.startTransfer">&#xf0ec;</button>--}}
                            {{--<button class="fa" data-bind="click: $root.student().edit">&#xf040;</button>--}}
                            {{--<button class="fa danger" data-bind="click: $root.startRemove">&#xf014;</button>--}}
                        {{--</td>--}}
                    {{--</tr>--}}
                    {{--<!-- /ko -->--}}
                {{--<!-- /ko -->--}}
                {{--</tbody>--}}
            {{--</table>--}}
            {{--<!-- /ko -->--}}
        {{--</div>--}}
        {{--<!-- /ko -->--}}
        {{--<!-- /ko -->--}}
    {{--</div>--}}

</div>

<div class="g-hidden">
    <div class="box-modal" id="delete-group-modal">
        {{--<div class="box-modal_close arcticmodal-close">закрыть</div>--}}
        <div class="popup-delete">
            <div><h3>Вы действительно хотите удалить выбранную группу?</h3></div>
            <div>
                <button class="remove" data-bind="click: $root.actions.end.remove">Удалить</button>
                <button class="cancel arcticmodal-close" data-bind="click: $root.actions.cancel">Отмена</button>
            </div>
        </div>
    </div>
</div>

{{--<div class="g-hidden">--}}
    {{--<div class="box-modal" id="select-plan-modal">--}}
        {{--<div class="box-modal_close arcticmodal-close">закрыть</div>--}}
        {{--<div>--}}
            {{--<h3>Учебный план</h3>--}}
            {{--<div>--}}
                {{--<select data-bind="options: $root.institutes,--}}
                       {{--optionsText: 'name',--}}
                       {{--value: $root.studyplanSelect().institute,--}}
                       {{--optionsCaption: 'Институт'"></select>--}}
                {{--<select data-bind="options: $root.profiles,--}}
                       {{--optionsText: 'name',--}}
                       {{--value: $root.studyplanSelect().profile,--}}
                       {{--optionsCaption: 'Направление подготовки',--}}
                       {{--enable: $root.studyplanSelect().institute()"></select>--}}
                {{--<select data-bind="options: $root.studyplans,--}}
                       {{--optionsText: 'name',--}}
                       {{--value: $root.studyplanSelect().studyplan,--}}
                       {{--optionsCaption: 'Учебный план',--}}
                       {{--enable: $root.studyplanSelect().institute() && $root.studyplanSelect().profile()"></select>--}}
            {{--</div>--}}
            {{--<div>--}}
                {{--<button data-bind="click: $root.approveStudyPlan, enable: $root.studyplanSelect().studyplan()" class="fa">&#xf00c;</button>--}}
                {{--<button class="fa arcticmodal-close danger">&#xf00d;</button>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
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

    @include('shared.error-modal')
@endsection
