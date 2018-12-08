@extends('layouts.manager')
@section('title', 'Учебные планы')
@section('javascript')
    <script src="{{ URL::asset('js/min/manager-studyplan.js')}}"></script>
@endsection
@section('content')
    <style>
        .modalDialog {
            position: fixed;
            font-family: Arial, Helvetica, sans-serif;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: rgba(0,0,0,0.8);
            z-index: 99999;
            -webkit-transition: opacity 400ms ease-in;
            -moz-transition: opacity 400ms ease-in;
            transition: opacity 400ms ease-in;
            display: none;
            pointer-events: none;
        }

        .modalDialog:target {
            display: block;
            pointer-events: auto;
        }

        .modalDialog > div {
            width: 400px;
            position: relative;
            margin: 10% auto;
            padding: 5px 20px 13px 20px;
            border-radius: 0px;
            background: #fff;
            backgroud-color: white;
        }
    </style>
    <div class="content">
        <div class="items">
            <div class="items-head">
                <h1>Детализация учебного плана</h1>
                <!-- ko if: $root.user.role() === role.admin.name -->
                <label class="adder" data-bind="click: $root.actions.start.create">Добавить дисциплину</label><br>
                <a href="#openModal"><label class="adder">Отобразить таблицу учебного плана</label></a><br>
                <!-- /ko -->
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
                <label class="title">Дисциплина</label>
                <input type="text" placeholder="Полное название дисциплины"
                       data-bind="value: $root.filter.discipline, valueUpdate: 'keyup'">
            </div>
            <div class="filter-block">
                <span class="clear" data-bind="click: $root.filter.clear">Очистить</span>
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
@endsection



<script type="text/html" id="show-discipline">
    <div class="details-row">
        <div class="details-column">
            <label class="title">Семестр</label>
            <span class="info" data-bind="text: semester"></span>
        </div>
        <div class="details-column">
            <label class="title">Количество часов</label>
            <span class="info" data-bind="text: hoursAll"></span>
        </div>
        <div class="details-column">
            <label class="title">Часов лекций</label>
            <span class="info" data-bind="text: hoursLecture"></span>
        </div>
        <div class="details-column">
            <label class="title">Часов лабораторных занятий</label>
            <span class="info" data-bind="text: hoursLaboratory"></span>
        </div>
        <div class="details-column">
            <label class="title">Часов практических занятий</label>
            <span class="info" data-bind="text: hoursPractical"></span>
        </div>
        <div class="details-column">
            <label class="title">Часов самостоятельной работы</label>
            <span class="info" data-bind="text: hoursSolo"></span>
        </div>
        <div class="details-column">
            <label class="title">Количество лекций</label>
            <span class="info" data-bind="text: countLecture"></span>
        </div>
        <div class="details-column">
            <label class="title">Количество практич. занятий</label>
            <span class="info" data-bind="text: countLaboratory"></span>
        </div>
        <div class="details-column">
            <label class="title">Количество лаб. работ</label>
            <span class="info" data-bind="text: countPractical"></span>
        </div>
        <div class="details-column">
            <label class="title">Дополнительные условия сдачи</label>
            <span class="info coloredin-patronus" data-bind="text: hasExam() ? 'Экзамен ' : ''"></span>
            <span class="info coloredin-patronus" data-bind="text: hasCoursework() ? 'Курс.работа ' : ''"></span>
            <span class="info coloredin-patronus" data-bind="text: hasCourseProject() ? 'Курс.проект ' : ''"></span>
            <span class="info coloredin-patronus" data-bind="text: hasDesignAssignment() ? 'РГЗ ' : ''"></span>
            <span class="info coloredin-patronus" data-bind="text: hasEssay() ? 'Реферат ' : ''"></span>
            <span class="info coloredin-patronus" data-bind="text: hasHomeTest() ? 'Дом.КР ' : ''"></span>
            <span class="info coloredin-patronus" data-bind="text: hasAudienceTest() ? 'Ауд.КР ' : ''"></span>



            <span class="info coloredin-patronus" data-bind="text: hasExam() && hasProject() ? ';' : ''"></span>
            <span class="info coloredin-patronus" data-bind="text: hasProject() ? 'Курсовой' : ''"></span>
            <span class="info coloredin-patronus" data-bind="text: !hasExam() && !hasProject() ? 'Нет' : ''"></span>
        </div>
    </div>
    <!-- ko if: $root.user.role() === role.admin.name -->
    <div class="details-row float-buttons">
        <div class="details-column float-right width-100p">
            <button class="remove" data-bind="click: $root.actions.start.remove"><span class="fa">&#xf014;</span>&nbsp;Удалить</button>
            <button class="approve" data-bind="click: $root.actions.start.update"><span class="fa">&#xf040;</span>&nbsp;Редактировать</button>
        </div>
    </div>
    <!-- /ko -->
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
            <label class="title">Семестр обучения <span class="required">*</span></label>
            <input id="iSemester" type="text" validate
                   data-bind="value: semester,
                   validationElement: semester,
                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
        </div>
        <div class="details-column width-31p">
            <label class="title">Количество часов <span class="required">*</span></label>
            <input id="iHours" type="text" validate
                   data-bind="value: hoursAll,
                   validationElement: hoursAll,
                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
        </div>
        <div class="details-column width-31p">
            <label class="title">Часов лекций <span class="required">*</span></label>
            <input id="iHoursLect" type="text" validate
                   data-bind="value: hoursLecture,
                   validationElement: hoursLecture,
                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
        </div>
        <div class="details-column width-31p">
            <label class="title">Часов практ. занятий <span class="required">*</span></label>
            <input id="iHoursPrakt" type="text" validate
                   data-bind="value: hoursPractical,
                   validationElement: hoursPractical,
                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
        </div>
        <div class="details-column width-31p">
            <label class="title">Часов лабор. занятий <span class="required">*</span></label>
            <input id="iHoursLabour" type="text" validate
                   data-bind="value: hoursLaboratory,
                   validationElement: hoursLaboratory,
                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
        </div>
        <div class="details-column width-31p">
            <label class="title">Часов cамост. изучения <span class="required">*</span></label>
            <input id="iHoursSamost" type="text" validate
                   data-bind="value: hoursSolo,
                   validationElement: hoursSolo,
                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
        </div>
        <br><br><br>
        <div class="details-column width-31p">
            <label class="title">Количество лекций <span class="required">*</span></label>
            <input id="iCountLekt" type="text" validate
                   data-bind="value: countLecture,
                   validationElement: countLecture,
                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
        </div>
        <div class="details-column width-31p">
            <label class="title">Количество практ. занятий <span class="required">*</span></label>
            <input id="iCountPrakt" type="text" validate
                   data-bind="value: countPractical,
                   validationElement: countPractical,
                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
        </div>
        <div class="details-column width-31p">
            <label class="title">Количество лабор. работ <span class="required">*</span></label>
            <input id="iCountLab" type="text" validate
                   data-bind="value: countLaboratory,
                   validationElement: countLaboratory,
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
            <label class="title">Курсовая работа</label>
            <span class="radio" data-bind="click: $root.actions.switchCoursework.on, css: {'radio-important' : hasCoursework()}">Есть</span>
            <span>|</span>
            <span class="radio" data-bind="click: $root.actions.switchCoursework.off, css: {'radio-important' : !hasCoursework()}">Нет</span>
        </div>
        <div class="details-column width-20p">
            <label class="title">Курсовой проект</label>
            <span class="radio" data-bind="click: $root.actions.switchCourseProject.on, css: {'radio-important' : hasCourseProject()}">Есть</span>
            <span>|</span>
            <span class="radio" data-bind="click: $root.actions.switchCourseProject.off, css: {'radio-important' : !hasCourseProject()}">Нет</span>
        </div>
        <div class="details-column width-20p">
            <label class="title">РГЗ</label>
            <span class="radio" data-bind="click: $root.actions.switchDesignAssignment.on, css: {'radio-important' : hasDesignAssignment()}">Есть</span>
            <span>|</span>
            <span class="radio" data-bind="click: $root.actions.switchDesignAssignment.off, css: {'radio-important' : !hasDesignAssignment()}">Нет</span>
        </div>
        <div class="details-column width-20p">
            <label class="title">Реферат</label>
            <span class="radio" data-bind="click: $root.actions.switchEssay.on, css: {'radio-important' : hasEssay()}">Есть</span>
            <span>|</span>
            <span class="radio" data-bind="click: $root.actions.switchEssay.off, css: {'radio-important' : !hasEssay()}">Нет</span>
        </div>
        <div class="details-column width-20p">
            <label class="title">Аудит. КР</label>
            <span class="radio" data-bind="click: $root.actions.switchAudienceTest.on, css: {'radio-important' : hasAudienceTest()}">Есть</span>
            <span>|</span>
            <span class="radio" data-bind="click: $root.actions.switchAudienceTest.off, css: {'radio-important' : !hasAudienceTest()}">Нет</span>
        </div>
        <div class="details-column width-20p">
            <label class="title">Домаш. КР</label>
            <span class="radio" data-bind="click: $root.actions.switchHomeTest.on, css: {'radio-important' : hasHomeTest()}">Есть</span>
            <span>|</span>
            <span class="radio" data-bind="click: $root.actions.switchHomeTest.off, css: {'radio-important' : !hasHomeTest()}">Нет</span>
        </div>
    </div>
    <div class="details-row float-buttons">
        <div class="details-column float-right width-100p">
            <button class="cancel" data-bind="click: $root.actions.cancel">Отмена</button>
            <button id="bUpdateStudyplanItem" accept-validation title="Проверьте правильность заполнения полей" class="approve" data-bind="click: $root.actions.end.update">Сохранить</button>
        </div>
    </div>
</script>