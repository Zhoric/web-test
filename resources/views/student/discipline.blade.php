@extends('layouts.student')
@section('title', 'Тесты')
@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/site.css')}}" />
@endsection
@section('javascript')
    <script src="{{ URL::asset('js/min/student-discipline.js')}}"></script>
@endsection

@section('menu')
    @include('student.menu')
@endsection

@section('content')

    <div class="content">

        <div class="items">
            <h1 data-bind="text: current.discipline.name"></h1>
            <!-- ko if: !$root.current.tests().length -->
            <h3 class="text-center">По данному запросу ничего не найдено</h3>
            <!-- /ko -->
            <div class="progress-bar">

            </div>
            <div class="items-body" data-bind="foreach: current.tests">
                <div class="item test" data-bind="click: $root.actions.start">
                    <span class="start">Пройти тест</span>
                    <span data-bind="text: test.subject"></span>
                    <span class="attempts" data-bind="text: 'Попыток осталось: ' + attemptsLeft()"></span>
                </div>
            </div>
        </div>
        <div class="filter">
            <div class="filter-block">
                <label class="title">Тест</label>
                <input class="height-35px" type="text" placeholder="Название теста"
                       data-bind="value: $root.filter.test, valueUpdate: 'keyup'"/>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" id="all-tests" value="all-tests"
                       data-bind="checked: $root.filter.type"/>
                <label for="all-tests">Все</label>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" id="started-not-finished" value="started-not-finished"
                       data-bind="checked: $root.filter.type"/>
                <label for="started-not-finished">Остались попытки</label>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" id="not-started-yet" value="not-started-yet"
                       data-bind="checked: $root.filter.type"/>
                <label for="not-started-yet">Ни одной попытки не использовано</label>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" id="finished" value="finished"
                       data-bind="checked: $root.filter.type"/>
                <label for="finished">Не осталось попыток</label>
            </div>
            <div class="filter-block">
                <span class="clear" data-bind="click: $root.filter.clear">Очистить</span>
            </div>
        </div>
    </div>
@endsection

<script type="text/html" id="test-template">
    <td data-bind="text: subject"></td>
    <td data-bind="text: $parent.attemptsMade"></td>
    <td data-bind="text: $parent.attemptsLeft"></td>
    <td><button data-bind="click: $root.actions.startTest, disable: !$parent.attemptsLeft(), css: {'attempts-mid': ($parent.attemptsLeft() > $parent.attemptsMade()) && $parent.attemptsMade(), 'attempts-all': !$parent.attemptsMade(), 'attempts-little': $parent.attemptsLeft() == 1}">Пройти тест</button></td>
</script>