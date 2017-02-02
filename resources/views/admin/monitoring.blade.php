@extends('shared.layout')
@section('title', 'Мониторинг тестирования')
@section('javascript')
    <script src="{{ URL::asset('js/helpers/common.js')}}"></script>

@endsection

@section('content')
<div class="content">
    <div class="items">
        <div class="items-head">
            <h1>Мониторинг тестирования</h1>
        </div>
        <div class="items-body">  </div>
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
            <label class="title">Тест</label>
            <select data-bind="options: $root.filter.tests,
                       optionsText: 'subject',
                       value: $root.filter.test,
                       optionsCaption: 'Выберите тест'"></select>
        </div>
        <div class="filter-block">
            <label class="title">Состояние</label>
            <input type="radio" class="custom-radio" group="state"/>
            <input type="radio" class="custom-radio" group="state"/>
            <input type="radio" class="custom-radio" group="state"/>
        </div>
        <div class="filter-block">
            <label class="title">Интервал обновления</label>
            <select></select>
        </div>
    </div>
    @include('admin.shared.error-modal')
</div>
@endsection