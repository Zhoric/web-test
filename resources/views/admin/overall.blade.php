@extends('layouts.manager')
@section('title', 'Результаты группы')
@section('javascript')
    <link rel="stylesheet" href="{{ URL::asset('css/datepicker.css')}}"/>
    <script src="{{ URL::asset('js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{ URL::asset('js/bootstrap.min.js')}}"></script>
    <script src="{{ URL::asset('js/d3.v2.min.js')}}"></script>
    <script src="{{ URL::asset('js/timeknots-min.js')}}"></script>
    <script src="{{ URL::asset('js/helpers/datepicker.js')}}"></script>
    <script src="{{ URL::asset('js/helpers/timeline.js')}}"></script>
    <script src="{{ URL::asset('js/admin/overall.js')}}"></script>
@endsection

@section('content')
<div class="content">
    <div class="items">
        <div class="items-head">
            <h1>Результаты по дисциплине</h1>
            <label class="adder" data-bind="click: $root.actions.results">Результаты&nbsp;по&nbsp;тесту</label>
        </div>
        <!-- ko if: !$root.filter.group() && !$root.filter.discipline() -->
        <h3 class="text-center">Пожалуйста, заполните все поля фильтра</h3>
        <!-- /ko -->
        <div class="items-body" data-bind="if: $root.filter.group() && $root.filter.discipline()">
            <table class="werewolf">
                <thead>
                    <tr>
                        <th>ФИО студента</th>
                        <th>Хронология прохождения тестов</th>
                        <th>Средний балл</th>
                    </tr>
                </thead>
                <tbody data-bind="foreach: $root.current.results">
                    <tr>
                        <td data-bind="text: name" class="minw-200"></td>
                        <td class="text-center width-100p" data-bind="timeline: results.knot($root.filter.startDate(), $root.filter.endDate())"></td>
                        <td data-bind="text: mark" class="text-center"></td>
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
            <label class="title">Дисциплина</label>
            <select data-bind="options: $root.filter.disciplines,
                       optionsText: 'name',
                       value: $root.filter.discipline,
                       optionsCaption: 'Выберите дисциплину',
                       enable: $root.filter.profile"></select>
        </div>
        <div class="filter-block">
            <label class="title">Группа</label>
            <select data-bind="options: $root.filter.groups,
                       optionsText: 'name',
                       value: $root.filter.group,
                       optionsCaption: 'Выберите группу',
                       enable: $root.filter.profile"></select>
        </div>
        <div class="filter-block">
            <label class="title">Начало&nbsp;интервала</label>
            <span class="fa pointer date-ico" data-bind="datePicker: $root.filter.startDate">&#xf073;</span>
            <span data-bind="text: $root.filter.startDate.parseDay()"></span>
        </div>
        <div class="filter-block">
            <label class="title">Конец&nbsp;интервала</label>
            <span class="fa pointer date-ico" data-bind="datePicker: $root.filter.endDate">&#xf073;</span>
            <span data-bind="text: $root.filter.endDate.parseDay()"></span>
        </div>
        <div class="filter-block">
            <label class="title">Критерий&nbsp;выбора&nbsp;результатов</label>
            <input type="radio" class="custom-radio" data-bind="checked: $root.filter.criterion" value="maxMark" id="max-mark"/><label for="max-mark">Максимальная оценка</label>
            <input type="radio" class="custom-radio" data-bind="checked: $root.filter.criterion" value="firstTry" id="first-try" /><label for="first-try">Первая попытка</label>
            <input type="radio" class="custom-radio" data-bind="checked: $root.filter.criterion" value="secondTry" id="last-try" /><label for="last-try">Последняя попытка</label>
        </div>
        <div class="filter-block">
            <span class="clear" data-bind="click: filter.clear">Очистить</span>
        </div>
    </div>
</div>
@endsection