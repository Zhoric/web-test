@extends('shared.layout')
@section('title', 'Тесты')
@section('javascript')
    <link rel="stylesheet" href="{{ URL::asset('css/tooltipster.bundle.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/tooltipster-sideTip-light.min.css')}}"/>
    {{--<link rel="stylesheet" href="{{ URL::asset('css/knockout.autocomplete.css')}}"/>--}}
    <script src="{{ URL::asset('js/knockout.validation.js')}}"></script>
    <script src="{{ URL::asset('js/tooltipster.bundle.js')}}"></script>
    <script src="{{ URL::asset('js/knockout.autocomplete.js')}}"></script>
    <script src="{{ URL::asset('js/helpers/ko-multiselect.js')}}"></script>
    <script src="{{ URL::asset('js/admin/tests.js')}}"></script>
@endsection

@section('content')
<div class="content">
    <div class="items">
        <div class="items-head">
            <h1>Администрирование тестов</h1>
            <!-- ko if: $root.filter.discipline() -->
            <label class="adder" data-bind="click: $root.csed.test.toggleAdd">Добавить</label>
            <!-- /ko -->
        </div>
        <!-- ko if: !$root.filter.discipline() && !$root.current.tests.length -->
        <h3 class="text-center">Пожалуйста, выберите дисциплину</h3>
        <!-- /ko -->
        <!-- ko if: $root.mode() === 'add'-->
        <div data-bind="template: {name: 'show-details', data: $root.current.test}"></div>
        <!-- /ko -->
        <div class="items-body">
            <!-- ko foreach: $root.current.tests -->
            <div class="item" data-bind="text: subject, click: $root.csed.test.show"></div>
            <!-- ko if: $root.mode() !== 'none' && $root.current.test().id() === $data.id() -->
            <div data-bind="template: {name: 'show-details', data: $root.current.test}"></div>
            <!-- /ko -->
            <!-- /ko -->
        </div>

        @include('admin.shared.pagination')
    </div>
    <div class="filter">
        <div class="filter-block">
            <label class="title">Дициплина</label>
            <select data-bind="options: current.disciplines,
                       optionsText: 'name',
                       value: filter.discipline,
                       optionsCaption: 'Выберете дисциплину'"></select>
        </div>
        <div class="filter-block">
            <label class="title">Название теста</label>
            <input type="text" data-bind="value: filter.name, valueUpdate: 'keyup'">
        </div>
        <div class="filter-block">
            <span class="clear" data-bind="click: filter.clear">Очистить</span>
        </div>
    </div>
</div>

<script type="text/html" id="show-details">
    <div>
        <!-- ko if: $root.mode() === 'info' || $root.mode() === 'delete' -->
        <div class="width100" data-bind="template: {name: 'info-mode', data: $data}"></div>
        <!-- /ko -->
        <!-- ko if: $root.mode() === 'edit' || $root.mode() === 'add'-->
        <div class="width100" data-bind="template: {name: 'edit-mode', data: $data}"></div>
        <!-- /ko -->
    </div>
</script>
<script type="text/html" id="info-mode">
    <div class="details test">
        <div class="details-row">
            <div class="details-column width-50p">
                <label class="title">Название</label>
                <span class="info" data-bind="text: subject"></span>
            </div>
            <div class="details-column width-25p">
                <label class="title">Тип</label>
                <span class="info" data-bind="text: type() ? 'Контроль знаний' : 'Обучающий'"></span>
            </div>
            <div class="details-column width-15p">
                <label class="title">Время</label>
                <span class="info" data-bind="text: minutes() + ':' + minutes()"></span>
            </div>
        </div>
        <div class="details-row float-buttons">
            <div class="details-column width-100p">
                <button class="remove" data-bind="click: $root.csed.test.startRemove"><span class="fa">&#xf014;</span>&nbsp;Удалить</button>
                <button class="approve" data-bind="click: $root.csed.test.startEdit"><span class="fa">&#xf040;</span>&nbsp;Редактировать</button>
            </div>
        </div>
    </div>
</script>
<script type="text/html" id="edit-mode">
    <div class="details">
        <div class="details-row">
            <div class="details-column width-98p">
                <label class="title">Название</label>
                <input tooltip-mark="subject_tooltip" type="text" data-bind="value: subject, event: {focusin: $root.events.focusin, focusout: $root.events.focusout}">
            </div>

        </div>
        <div class="details-row">
            <div class="details-column width-20p">
                <label class="title">Дительность теста</label>
                <input class="time" type="text" tooltip-mark="minutes_tooltip" data-bind="value: minutes, valueUpdate: 'keyup', event: {focusin: $root.events.focusin, focusout: $root.events.focusout} " placeholder="00">
                <span>:</span>
                <input class="time" type="text" tooltip-mark="seconds_tooltip" data-bind="value: seconds, valueUpdate: 'keyup', event: {focusin: $root.events.focusin, focusout: $root.events.focusout} " placeholder="00">
            </div>
            <div class="details-column width-40p">
                <label class="title">Тип теста</label>
                <span class="radio" data-bind="css: {'radio-important': type()}, click: $root.alter.set.type.asTrue">Контроль знаний</span>
                <span>|</span>
                <span class="radio" data-bind="css: {'radio-important': !type()}, click: $root.alter.set.type.asFalse">Обучающий</span>
            </div>
            <div class="details-column attempts width-19p">
                <label class="title">Количество попыток</label>
                <input tooltip-mark="tryouts_tooltip" class="attempts" type="text" data-bind="value: attempts, event: {focusin: $root.events.focusin, focusout: $root.events.focusout}">
            </div>
            <div class="details-column width-25p">
                <label class="title">Принцип подбора вопросов</label>
                <span class="radio" data-bind="css: { 'radio-important': isRandom() }, click: $root.alter.set.random.asTrue">Случайный</span>
                <span>|</span>
                <span class="radio" data-bind="css: { 'radio-important': !isRandom() }, click: $root.alter.set.random.asFalse">Адаптивный</span>
            </div>
        </div>
        <div class="details-row">
            <div class="details-column width-98p">
                <label class="title">Темы</label>
                <div class="multiselect-wrap">
                    <!-- ko if: $root.multiselect.tags().length -->
                    <div class="multiselect">
                        <ul data-bind="foreach: $root.multiselect.tags">
                            <li><span data-bind="click: $root.multiselect.remove" class="fa">&#xf00d;</span><span data-bind="text: name"></span></li>
                        </ul>
                    </div>
                    <!-- /ko -->
                    <input placeholder="Начните вводить"
                           data-bind="autocomplete: {
                           data: $root.multiselect.source, 
                           format: $root.multiselect.text,
                           onSelect: $root.multiselect.select,
                           after: true},
                           css: {'full': $root.multiselect.tags().length}" value=""/>
                </div>
            </div>
        </div>
        <div class="details-row float-buttons">
            <div class="details-column width-100p">
                <input class="custom-checkbox" id="test-is-active" type="checkbox" data-bind="checked: isActive"><label for="test-is-active">Активный</label>
                <button data-bind="click: $root.csed.test.cancel" class="cancel"><span class="fa">&#xf00d;</span>&nbsp;Отмена</button>
                <button data-bind="click: $root.csed.test.update" class="approve save-button"><span class="fa">&#xf00c;</span>&nbsp;Сохранить</button>
            </div>
        </div>

    </div>
</script>
    @include('admin.shared.error-modal')
@endsection

<div class="g-hidden">
    <div class="box-modal" id="delete-modal">
        <div class="popup-delete">
            <div><h3>Удалить выбранный тест?</h3></div>
            <div>
                <button data-bind="click: $root.csed.test.remove" class="fa">&#xf00c;</button>
                <button data-bind="click: $root.csed.test.cancel" class="fa danger arcticmodal-close">&#xf00d;</button>
            </div>
        </div>
    </div>
</div>

<div class="tooltip_templates">
    <span id="subject_tooltip">
        <span data-bind="validationMessage: $root.current.test().subject"></span>
    </span>
    <span id="minutes_tooltip">
        <span data-bind="validationMessage: $root.current.test().minutes"></span>
    </span>
    <span id="seconds_tooltip">
        <span data-bind="validationMessage: $root.current.test().seconds"></span>
    </span>
    <span id="tryouts_tooltip">
        <span data-bind="validationMessage: $root.current.test().attempts"></span>
    </span>
</div>