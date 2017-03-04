@extends('layouts.student')
@section('title', 'Тесты')
@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/site.css')}}" />
@endsection
@section('javascript')
    <script src="{{ URL::asset('js/student/discipline.js')}}"></script>
@endsection

@section('menu')
    @include('student.menu')
@endsection

@section('content')

    <div class="content">

        <div class="items">
            <h1 data-bind="text: current.discipline.name"></h1>
            <div class="progress-bar">

            </div>
            <div class="items-body" data-bind="foreach: current.tests">
                <div class="item test" data-bind="click: $root.actions.start">
                    <span data-bind="text: test.subject"></span>
                    <span class="attempts" data-bind="text: attemptsLeft() +'/' + (attemptsLeft() + attemptsMade()) + ' попыток'"></span>
                    <span class="start">Пройти тест</span>
                </div>
            </div>
        </div>
        <div class="filter">
            <div class="filter-block">
                <label class="title">Тест</label>
                <input class="height-35px" type="text" placeholder="Название"/>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" id="all-tests"/><label for="all-tests">Все</label>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" id="has-attempts-left"/><label for="has-attempts-left">Остались попытки</label>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" id="no-attempt-used"/><label for="no-attempt-used">Ни одной попытки не использовано</label>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" id="has-no-attempts"/><label for="has-no-attempts">Не осталось попыток</label>
            </div>
            <div class="filter-block">
                <span class="clear">Очистить</span>
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

<div class="g-hidden">
    <div class="box-modal removal-modal" id="confirm-test-start-modal">
        <div class="layer zero-margin width-auto">
            <div class="layer-head">
                <h3>Вы уверены, что хотите пройти выбранный тест?</h3>
            </div>
            <div class="layer-body">
                <div class="details-row">
                    <p><span class="bold">Предупреждение: </span>
                        Во время прохождения теста перезагрузка или переход на другую страницу приведёт к тому, что
                        текущая попытка прохождения теста будет считаться израсходованной.
                    </p>
                </div>
                <div class="details-row float-buttons">
                    <button class="cancel arcticmodal-close zero-margin width-80">Отмена</button>
                    <button class="approve arcticmodal-close width-80" data-bind="click: $root.actions.end">ОК</button>
                </div>
            </div>
        </div>
    </div>
</div>