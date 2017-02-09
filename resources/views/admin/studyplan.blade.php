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
                <label class="adder" data-bind="click: $root.actions.start.create">Добавить дисциплину</label>
            </div>
            <!-- ko if: $root.mode() === state.create -->
            <div class="details" data-bind="template: {name: 'update-discipline', data: $root.current.discipline}"></div>
            <!-- /ko -->
            <div class="items-body" data-bind="foreach: $root.current.disciplines">
                <div class="item" data-bind="click: $root.actions.show">
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
        </div>
        <div class="filter">
            <div class="filter-block">
                <label class="title">Название дисциплины</label>
                <input type="text" data-bind="value: $root.filter.discipline, valueUpdate: 'keyup'">
            </div>
        </div>
    </div>
    <div class="g-hidden">
        <div class="box-modal" id="remove-discipline-plan-modal">
            <div class="popup-delete">
                <div><h3>Вы действительно хотите удалить выбранную дисциплину?</h3></div>
                <div>
                    <button class="remove arcticmodal-close" data-bind="click: $root.actions.end.remove">Удалить</button>
                    <button class="cancel arcticmodal-close" data-bind="click: $root.actions.cancel">Отмена</button>
                </div>
            </div>
        </div>
    </div>
    @include('shared.error-modal')
@endsection

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
        <div class="details-column float-right width-100p">
            <button class="remove" data-bind="click: $root.actions.start.remove"><span class="fa">&#xf014;</span>&nbsp;Удалить</button>
            <button class="approve" data-bind="click: $root.actions.start.update"><span class="fa">&#xf040;</span>&nbsp;Редактировать</button>
        </div>
    </div>
</script>
<script type="text/html" id="update-discipline">
    <div class="details-row" data-bind="if: $root.mode() === state.create">
        <div class="details-column width-98p">
            <label class="title">Название дисциплины <span class="required">*</span></label>
            <select id="sDisciplineSelection" validate
                    data-bind="options: $root.initial.disciplines,
                       optionsText: 'name',
                       value: $root.initial.selection,
                       optionsCaption: 'Выберите дисциплину',
                       validationElement: $root.initial.selection,
                       event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"></select>
        </div>
    </div>

    <div class="details-row">
        <div class="details-column width-31p">
            <label class="title">Начальный семестр <span class="required">*</span></label>
            <input id="iStartSemester" type="text" validate
                   data-bind="value: startSemester,
                   validationElement: startSemester,
                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
        </div>
        <div class="details-column width-31p">
            <label class="title">Количество семестров <span class="required">*</span></label>
            <input id="ISemestersCount" type="text" validate
                   data-bind="value: semestersCount,
                   validationElement: semestersCount,
                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
        </div>
        <div class="details-column width-31p">
            <label class="title">Количество часов <span class="required">*</span></label>
            <input id="iHours" type="text" validate
                   data-bind="value: hours,
                   validationElement: hours,
                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
        </div>
    </div>
    <div class="details-row">
        <div class="details-column width-20p">
            <label class="title">Экзамен</label>
            <span class="radio" data-bind="click: $root.actions.switchExam.on, css: {'radio-important' : hasExam()}">Есть</span>
            <span>|</span>
            <span class="radio" data-bind="click: $root.actions.switchExam.off, css: {'radio-important' : !hasExam()}">Нет</span>
        </div>
        <div class="details-column width-20p">
            <label class="title">Курсовой проект</label>
            <span class="radio" data-bind="click: $root.actions.switchProject.on, css: {'radio-important' : hasProject()}">Есть</span>
            <span>|</span>
            <span class="radio" data-bind="click: $root.actions.switchProject.off, css: {'radio-important' : !hasProject()}">Нет</span>
        </div>
    </div>
    <div class="details-row float-buttons">
        <div class="details-column float-right width-100p">
            <button class="cancel" data-bind="click: $root.actions.cancel">Отмена</button>
            <button id="update-studyplan-item" title="Проверьте правильность заполнения формы" class="approve" data-bind="click: $root.actions.end.update">Сохранить</button>
        </div>
    </div>
</script>