@extends('layouts.manager')
@section('title', 'Мониторинг тестирования')
@section('javascript')
    <script src="{{ URL::asset('js/helpers/ko-progressbar.js')}}"></script>
    <script src="{{ URL::asset('js/admin/monitoring.js')}}"></script>
@endsection

@section('content')
<div class="content">
    <div class="items">
        <div class="items-head">
            <h1>Мониторинг тестирования</h1>
        </div>
        <div class="items-body">
            <table class="monitoring">
                <thead>
                    <tr>
                        <td>ФИО студента</td>
                        <td>Предварительная оценка</td>
                        <td>Прогресс</td>
                    </tr>
                </thead>
                <tbody data-bind="foreach: current.results">
                    <tr>
                        <td class="width-40p" data-bind="text: studentName"></td>
                        <td class="text-center" data-bind="text: mark"></td>
                        <td class="width-40p text-center">
                            <div data-bind="progressBar: {value: precentage}"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="filter">
        <div class="filter-block">
            <label class="title">Направление</label>
            <select data-bind="options: $root.filter.profiles,
                       optionsText: 'name',
                       value: $root.filter.profile,
                       optionsCaption: 'Выберите направление'"></select>
        </div>
        <div class="filter-block">
            <label class="title">Группа</label>
            <select data-bind="options: $root.filter.groups,
                       optionsText: 'name',
                       value: $root.filter.group,
                       optionsCaption: 'Выберите группу',
                       enable: $root.filter.profile()"></select>
        </div>
        <div class="filter-block">
            <label class="title">Дисциплина</label>
            <select data-bind="options: $root.filter.disciplines,
                       optionsText: 'name',
                       value: $root.filter.discipline,
                       optionsCaption: 'Выберите дисциплину',
                       enable: $root.filter.profile()"></select>
        </div>
        <div class="filter-block">
            <label class="title">Тест</label>
            <select data-bind="options: $root.filter.tests,
                       optionsText: 'subject',
                       value: $root.filter.test,
                       optionsCaption: 'Выберите тест',
                       enable: $root.filter.group()"></select>
        </div>
        <div class="filter-block">
            <label class="title">Состояние</label>
            <input type="radio" class="custom-radio" name="state" checked value="any" id="any-state" data-bind="checked: filter.state"/>
            <label class="block" for="any-state">Любой</label>
            <input type="radio" class="custom-radio" name="state" value="process" id="in-process-state" data-bind="checked: filter.state"/>
            <label class="block" for="in-process-state">В процессе</label>
            <input type="radio" class="custom-radio" name="state" value="finished" id="finished-state" data-bind="checked: filter.state"/>
            <label class="block" for="finished-state">Завершен</label>
        </div>
        <div class="filter-block">
            <label class="title">Интервал обновления</label>
            <span class="interval" secs="5000" data-bind="click: $root.actions.setInterval, css: {'current' : filter.interval() == interval.fivesec}">5 сек.</span>
            <span class="interval" secs="30000" data-bind="click: $root.actions.setInterval, css: {'current' : filter.interval() == interval.thirtysec}">30 сек.</span>
            <span class="interval" secs="60000" data-bind="click: $root.actions.setInterval, css: {'current' : filter.interval() == interval.onemin}">1 мин.</span>
        </div>
        <div class="filter-block">
            <span class="clear" data-bind="click: filter.clear">Очистить</span>
        </div>
    </div>
    @include('shared.error-modal')
</div>
@endsection