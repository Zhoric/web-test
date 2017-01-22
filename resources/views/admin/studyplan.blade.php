@extends('shared.layout')
@section('title', 'Учебные планы')
@section('javascript')
    <script src="{{ URL::asset('js/admin/studyplan.js')}}"></script>
@endsection
@section('content')
    <div class="content">
        <div class="items">
            <div class="items-head">
                <h1>Детализация учебного плана</h1>
                <label class="adder">Добавить дисциплину</label>
            </div>
            <!-- ko if: $root.mode() === state.create -->
            <div class="details" data-bind="template: {name: 'update-discipline', data: $root.current.discipline}"></div>
            <!-- /ko -->
            <div class="items-body" data-bind="foreach: $root.current.disciplines">
                <div class="item">
                    <span data-bind="text: discipline"></span>
                </div>
                <!-- ko if: id() === $root.current.discipline().id() -->
                    <!-- ko if: $root.mode() === state.info -->
                    <div class="details" data-bind="template: {name: 'show-discipline', data: $root.current.discipline}"></div>
                    <!-- /ko -->
                    <!-- ko if: $root.mode() === state.update -->
                    <div class="details" data-bind="template: {name: 'update-discipline', data: $root.current.discipline}"></div>
                    <!-- /ko -->
                <!-- /ko -->
            </div>
            @include('shared.pagination')
        </div>
        <div class="filter">
            <div class="filter-block">
                <label class="title">Название дисциплины</label>
                <input type="text" data-bind="value: $root.filter.discipline, valueUpdate: 'keyup'">
            </div>
        </div>

<script type="text/html" id="show-discipline">
    <div class="details-row">
        <div class="details-column">
            <label class="title">Начальный семестр</label>
            <span class="info" data-bind="text: startSemester"></span>
        </div>
        <div class="details-column">
            <label class="title">Количество семестров</label>
            <span class="info" data-bind="text: semestersCount"></span>
        </div>
        <div class="details-column">
            <label class="title">Количество часов</label>
            <span class="info" data-bind="text: hours"></span>
        </div>
        <div class="details-column">
            <label class="title">Дополнительные условия сдачи</label>
            <span class="info coloredin-patronus" data-bind="text: hasExam() ? 'Экзамен' : ''"></span>
            <span class="info coloredin-patronus" data-bind="text: hasExam() && hasProject() ? ';' : ''"></span>
            <span class="info coloredin-patronus" data-bind="text: hasProject() ? 'Курсовой' : ''"></span>
            <span class="info coloredin-patronus" data-bind="text: !hasExam() && !hasProject() ? 'Нет' : ''"></span>
        </div>
    </div>
    <div class="details-row float-buttons">
        <div class="details-column float-right width-70p">
            <button class="remove" data-bind="click: $root.actions.start.remove"><span class="fa">&#xf014;</span>&nbsp;Удалить</button>
            <button class="approve" data-bind="click: $root.actions.start.update"><span class="fa">&#xf040;</span>&nbsp;Редактировать</button>
        </div>
    </div>
</script>
<script type="text/html" id="update-discipline">
    <div class="details-row" data-bind="if: $root.mode() === state.create">
        <div class="details-column width-98p">
            <label class="title">Название дисциплины</label>
            <select data-bind="options: $root.initial.disciplines,
                       optionsText: 'name',
                       value: $root.current.selection,
                       optionsCaption: 'Выберите дисциплину'"></select>
        </div>
    </div>

    <div class="details-row">
        <div class="details-column width-31p">
            <label class="title">Начальный семестр</label>
            <input type="text" data-bind="value: startSemester"/>
        </div>
        <div class="details-column width-31p">
            <label class="title">Количество семестров</label>
            <input type="text" data-bind="value: semestersCount"/>
        </div>
        <div class="details-column width-31p">
            <label class="title">Количество часов</label>
            <input type="text" data-bind="value: hours"/>
        </div>
        <div class="details-column width-55p">

        </div>
    </div>
    <div class="details-row">
        <div class="details-column">
            <label class="title">Экзамен</label>
            <span class="radio" data-bind="css: {'radio-important' : hasExam()}">Есть</span>
            <span>|</span>
            <span class="radio" data-bind="css: {'radio-important' : !hasExam()}">Нет</span>
        </div>
        <div class="details-column">
            <label class="title">Курсовой проект</label>
            <span class="radio" data-bind="click: hasProject(true), css: {'radio-important' : hasProject()}">Есть</span>
            <span>|</span>
            <span class="radio" data-bind="click: hasProject(false), css: {'radio-important' : !hasProject()}">Нет</span>
        </div>
    </div>
    <div class="details-row float-buttons">
        <div class="details-column float-right width-100p">
            <button class="remove" data-bind="click: $root.actions.start.remove">Отмена</button>
            <button class="approve" data-bind="click: $root.actions.start.update">Сохранить</button>
        </div>
    </div>
</script>
        {{--<div class="width100 studyplans">--}}
            {{--<!-- ko foreach: $root.current.disciplineplans-->--}}
                {{--<!--ko if: ($root.current.disciplineplan().id() != id() && ($root.mode() === 'none' ||  $root.mode() === 'edit')) || ($root.mode() === 'delete' || $root.mode() === 'none')-->--}}
                {{--<div class="plan-details">--}}
                    {{--<div><label>Дисциплина:</label><br>--}}
                        {{--<span data-bind="text: discipline"></span></div>--}}
                    {{--<div><label>Начальный семестр:</label><br>--}}
                        {{--<span data-bind="text: startSemester"></span></div>--}}
                    {{--<div><label>Количество семестров:</label><br>--}}
                        {{--<span data-bind="text: semestersCount"></span></div>--}}
                    {{--<div><label>Количество часов:</label><br>--}}
                        {{--<span data-bind="text: hours"></span></div>--}}
                    {{--<div><label>Курсовой проект:</label><br>--}}
                        {{--<!-- ko if: hasProject() === true -->--}}
                        {{--<span>Есть</span>--}}
                        {{--<!-- /ko -->--}}
                        {{--<!-- ko if: hasProject() === false -->--}}
                        {{--<span>Нет</span>--}}
                        {{--<!-- /ko -->--}}
                    {{--</div>--}}

                    {{--<div><label>Экзамен:</label><br>--}}
                        {{--<!-- ko if: hasExam() === true -->--}}
                        {{--<span>Есть</span>--}}
                        {{--<!-- /ko -->--}}
                        {{--<!-- ko if: hasExam() === false -->--}}
                        {{--<span>Нет</span>--}}
                        {{--<!-- /ko -->--}}
                    {{--</div>--}}
                    {{--<div><button data-bind="click: $root.plan.startEdit" class="fa success">&#xf040;</button>--}}
                        {{--<button data-bind="click: $root.plan.startDelete" class="fa danger">&#xf014;</button>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<!-- /ko -->--}}
                {{--<!--ko if: $root.mode() === 'edit' && $root.current.disciplineplan().id() === id() -->--}}
                {{--<div data-bind="template: {name: 'edit-mode', data: $root.current.disciplineplan()}"></div>--}}
                {{--<!-- /ko -->--}}
            {{--<!-- /ko -->--}}
        {{--</div>--}}
    </div>

<script type="text/html" id="edit-mode">
    <div class="plan-details-edit plan-details">
        <div><label>Дисциплина:</label><br>
            <select data-bind="options: $root.current.disciplineplans(),
                       optionsText: 'discipline',
                       value: $root.disciplineSelected(),
                       optionsCaption: 'Выберите дисциплину'"></select>
        </div>
        <div><label>Начальный семестр:</label><br>
            <input type="text" data-bind="value: startSemester">
        </div>
        <div><label>Количество семестров:</label><br>
            <input type="text" data-bind="value: semestersCount">
        </div>
        <div><label>Количество часов:</label><br>
            <input type="text" data-bind="value: hours">
        </div>
        <div><label>Курсовой проект:</label><br>
            <span data-bind="click: function(){$data.hasProject(true);}, css: {'plan-form-selected': hasProject()}" class="plan-form">Есть</span>
            <span> | </span>
            <span data-bind="click: function(){$data.hasProject(false);}, css: {'plan-form-selected': !hasProject()}" class="plan-form">Нет</span>
        </div>

        <div><label>Экзамен:</label><br>
            <span data-bind="click: function(){$data.hasExam(true);}, css: {'plan-form-selected': hasExam()}" class="plan-form">Есть</span>
            <span> | </span>
            <span data-bind="click: function(){$data.hasExam(false);}, css: {'plan-form-selected': !hasExam()}" class="plan-form">Нет</span>
        </div>
        <div><button data-bind="click: $root.plan.update" class="fa success">&#xf00c;</button>
            <button data-bind="click: $root.plan.cancelEdit" class="fa danger">&#xf00d;</button>
        </div>
    </div>
</script>
{{--<div class="g-hidden">--}}
    {{--<div class="box-modal" id="delete-plan-modal">--}}
        {{--<div class="box-modal_close arcticmodal-close">закрыть</div>--}}
        {{--<div class="popup-delete">--}}
            {{--<div><h3>Удалить выбранный план дисциплины?</h3></div>--}}
            {{--<div>--}}
                {{--<button data-bind="click: $root.plan.delete" class="fa">&#xf00c;</button>--}}
                {{--<button data-bind="click: $root.plan.cancelDelete" class="fa danger">&#xf00d;</button>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
@endsection
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