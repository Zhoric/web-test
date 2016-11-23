@extends('shared.layout')
@section('title', 'Результаты')
@section('javascript')
    {{--<link rel="stylesheet" href="{{ URL::asset('css/tooltipster.bundle.css')}}"/>--}}
    {{--<link rel="stylesheet" href="{{ URL::asset('css/tooltipster-sideTip-light.min.css')}}"/>--}}
    <script src="{{ URL::asset('js/tooltipster.bundle.js')}}"></script>
    <script src="{{ URL::asset('js/admin/results.js')}}"></script>
@endsection

@section('content')
<div class="content results">
    <div class="filter">
        <div>
            <label>Направление</label></br>
            <select data-bind="options: $root.filter.profiles,
                       optionsText: 'name',
                       value: $root.filter.profile,
                       optionsCaption: 'Выберите напраление'"></select>
        </div>
        <div>
            <label>Группа</label></br>
            <select data-bind="options: $root.filter.groups,
                       optionsText: 'name',
                       value: $root.filter.group,
                       optionsCaption: 'Выберите группу'"></select>
        </div>
        <div>
            <label>Дисциплина</label></br>
            <select data-bind="options: $root.filter.disciplines,
                       optionsText: 'name',
                       value: $root.filter.discipline,
                       optionsCaption: 'Выберите дисциплину'"></select>
        </div>
        <div>
            <label>Тест</label></br>
            <select data-bind="options: $root.filter.tests,
                       optionsText: 'subject',
                       value: $root.filter.test,
                       optionsCaption: 'Выберите тест'"></select>
        </div>
    </div>
    <div>
        <!-- ko foreach: current.results-->
        <a class="result" data-bind="attr: {href: '/admin/result/' + id()}">
            <div class="org-info">
                <div class="row">
                    <div class="name">
                        <label>ФИО студента</label></br>
                        <span data-bind="text: user.lastName"></span>
                        <span data-bind="text: user.firstName"></span>
                        <span data-bind="text: user.patronymic"></span>
                    </div>
                    <div class="mark">
                        <label>Оценка</label></br>
                        <!-- ko if: mark() -->
                        <span class="ok" data-bind="text: mark"></span><span class="ok">/100</span>
                        <!-- /ko -->
                        <span class="not-ok" data-bind="if: !mark()">Требуется проверка</span>
                    </div>
                    <div class="attempt">
                        <label>Номер попытки</label></br>
                        <span data-bind="text: attempt"></span>
                    </div>
                </div>
                <label class="date" data-bind="text: dateTime.date"></label>
            </div>
        </a>
        <!-- /ko -->
    </div>
</div>
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