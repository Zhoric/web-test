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
        <div class="current">
            <span><i class="fa">&#xf015;</i>Главная</span>
        </div>
        <div>
            <span><i class="fa">&#xf123;</i>Результаты</span>
        </div>
        <div>
            <span><i class="fa">&#xf080;</i>Статистика</span>
        </div>
        <div>
            <span><i class="fa">&#xf29c;</i>&nbsp;FAQ</span>
        </div>
        <div>
            <span data-bind="click: $root.actions.logout">Выход<i class="fa">&#xf08b;</i></span>
        </div>
    </div>
@endsection

@section('content')
    <div class="content">
        <div class="items">
            <!-- ko foreach: $root.current.rows -->
            <div class="row" data-bind="foreach: disciplines">
                <div class="discipline">
                    <div class="discipline-head" data-bind="click: $root.actions.details, css: {'current': $root.current.disciplineId() === id()},">
                        <span data-bind="text: abbreviation"></span>
                    </div>
                    <div class="discipline-body">
                        <span>5/10</span>
                    </div>
                </div>
            </div>
            <!-- /ko -->
        </div>
        <div class="filter">
            <div class="filter-block">
                <label class="title">Дисциплина</label>
                <input class="custom-radio" type="text" placeholder="Название/аббревиатура"/>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" id="all-discipines"/><label for="all-discipines">Все</label>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" id="has-tests"/><label for="has-tests">Имеются не пройденные тесты</label>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" id="has-no-tests"/><label for="has-no-tests">Все тесты пройдены</label>
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