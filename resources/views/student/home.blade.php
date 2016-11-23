@extends('student.layout')
@section('title', 'Главная')
@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/site.css')}}" />
@endsection
@section('javascript')
    <script src="{{ URL::asset('js/student/home.js')}}"></script>
@endsection

@section('menu')
    <div class="menu">
        <span>Главная</span>
        <span>Результаты</span>
        <span>Статистика</span>
        <span><i class="fa">&#xf29c;</i>&nbsp;FAQ</span>
        <span data-bind="click: $root.actions.logout">Выход</span>
    </div>
@endsection

@section('content')
    <div class="content">
        <!-- ko foreach: $root.current.rows -->
        <div class="row" data-bind="foreach: disciplines">
            <div class="discipline" data-bind="click: $root.actions.disciplineDetails.bind($data, $parent), css: {'current': $root.current.disciplineId() === id()},">
                <span data-bind="text: abbreviation"></span>
            </div>
        </div>
            <!-- ko if: $root.current.rowId() === rowId() && $root.mode() === 'details' -->
            <div class="details">
                <!-- ko if: $root.current.tests().length -->
                <table>
                    <thead>
                        <tr>
                            <th rowspan="2">Название теста</th>
                            <th colspan="2">Попытки</th>
                            <th rowspan="2">Действия</th>
                        </tr>
                        <tr>
                            <th>Использовано</th>
                            <th>Осталось</th>
                        </tr>
                    </thead>
                    <tbody data-bind="foreach: $root.current.tests">
                        <tr data-bind="template: {name: 'test-template', data: test}"></tr>
                    </tbody>
                </table>
                <!-- /ko -->
                <!-- ko if: !$root.current.tests().length -->
                    <h3>По данной дисциплине пока нет тестов</h3>
                <!-- /ko -->
            </div>
            <!-- /ko -->
        <!-- /ko -->
    </div>
@endsection

<script type="text/html" id="test-template">
    <td data-bind="text: subject"></td>
    <td data-bind="text: $parent.attemptsMade"></td>
    <td data-bind="text: $parent.attemptsLeft"></td>
    <td><button data-bind="click: $root.actions.startTest, disable: !$parent.attemptsLeft(), css: {'attempts-mid': ($parent.attemptsLeft() > $parent.attemptsMade()) && $parent.attemptsMade(), 'attempts-all': !$parent.attemptsMade(), 'attempts-little': $parent.attemptsLeft() == 1}">Пройти тест</button></td>
</script>

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