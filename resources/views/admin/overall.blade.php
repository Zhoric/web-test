@extends('shared.layout')
@section('title', 'Результаты группы')
@section('javascript')
    <link rel="stylesheet" href="{{ URL::asset('css/datepicker.css')}}"/>
    <script src="{{ URL::asset('js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{ URL::asset('js/bootstrap.min.js')}}"></script>
    <script src="{{ URL::asset('js/helpers/datepicker.js')}}"></script>
    <script src="{{ URL::asset('js/admin/overall.js')}}"></script>
@endsection

@section('content')
<div class="content">
    <div class="items">
        <div class="items-head">
            <h1>Результаты по дисциплине</h1>
            <label class="adder" data-bind="click: $root.actions.results">Результаты&nbsp;по&nbsp;тесту</label>
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
                       optionsCaption: 'Выберите группу'"></select>
        </div>
        <div class="filter-block">
            <label class="title">Дисциплина</label>
            <select data-bind="options: $root.filter.disciplines,
                       optionsText: 'name',
                       value: $root.filter.discipline,
                       optionsCaption: 'Выберите дисциплину'"></select>
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
    @include('shared.error-modal')
</div>
@endsection