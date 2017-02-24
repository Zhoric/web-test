@extends('shared.layout')
@section('title', 'Результаты')
@section('javascript')
    <script src="{{ URL::asset('js/helpers/common.js')}}"></script>
    <script src="{{ URL::asset('js/admin/results.js')}}"></script>
@endsection

@section('content')
<div class="content">
    <div class="items">
        <div class="items-head">
            <h1>Поиск результатов</h1>
        </div>
        <!-- ko if: !$root.filter.test()-->
            <h3 class="text-center">Пожалуйста, заполните все поля фильтра</h3>
        <!-- /ko -->
        <!-- ko if: !$root.current.results().length && $root.filter.test() -->
            <h3 class="text-center">По данному запросу ничего не найдено</h3>
        <!-- /ko -->
        <!-- ko if: $root.current.results().length -->
        <div class="items-body">
            <table class="werewolf results">
                <thead>
                    <tr>
                        <th>Попытка</th>
                        <th>ФИО студента</th>
                        <th>Дата</th>
                        <th>Оценка</th>
                    </tr>
                </thead>
                <tbody data-bind="foreach: current.results">
                <tr>
                    <td data-bind="text: attempt"></td>
                    <td data-bind="text: user.lastName() + ' ' +
                    user.firstName() + ' ' + user.patronymic()"></td>
                    <td data-bind="text: dateTime.date"></td>
                    <td>
                        <!-- ko if: mark() !== null -->
                        <span class="coloredin-patronus" data-bind="text: mark() +'/100'"></span>
                        <!-- /ko -->
                        <span class="coloredin-crimson" data-bind="if: mark() === null">Требуется проверка</span>
                    </td>

                </tr>
                </tbody>
            </table>
        </div>
        <!-- /ko -->
    </div>

    <div class="filter">
        <div class="filter-block">
            <a href="/admin/monitoring"><button class="action-button">Мониторинг тестирования</button></a>
        </div>
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
    </div>
    @include('admin.shared.error-modal')
</div>
@endsection