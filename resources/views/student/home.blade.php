@extends('layouts.student')
@section('title', 'Главная')
@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/site.css')}}" />
@endsection
@section('javascript')
    <script src="{{ URL::asset('js/min/student-home.js')}}"></script>
@endsection

@section('menu')
    @include('student.menu')
@endsection

@section('content')
    <div class="content">
        <div class="items">
            <!-- ko if: !$root.current.disciplines().length -->
            <h3 class="text-center">По данному запросу ничего не найдено</h3>
            <!-- /ko -->
            <div data-bind="foreach: $root.current.disciplines">
                <div class="test-block" data-bind="click: $root.actions.details">
                    <div class="head">
                        <span data-bind="text: discipline.abbreviation"></span>
                    </div>
                    <div class="body">
                        <div class="test">
                            <span data-bind="text: testsPassed() + '/' + testsCount()"></span>
                        </div>
                        <div class="sign">
                            <span>Пройдено тестов</span>
                        </div>
                    </div>
                    <div class="footer">
                        <div data-bind="progressBar: {value: $root.actions.percentage($data)}"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="filter">
            <div class="filter-block">
                <label class="title">Дисциплина</label>
                <input type="text" placeholder="Название/аббревиатура" data-bind="value: $root.filter.name, valueUpdate: 'keyup'"/>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" value="all-disciplines" id="all-disciplines" data-bind="checked: $root.filter.type"/>
                <label for="all-disciplines">Все</label>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" value="has-tests" id="has-tests" data-bind="checked: $root.filter.type"/>
                <label for="has-tests">Имеются не пройденные тесты</label>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" value="no-tests" id="no-tests" data-bind="checked: $root.filter.type"/>
                <label for="no-tests">Все тесты пройдены</label>
            </div>
            <div class="filter-block">
                <span class="clear" data-bind="click: filter.clear">Очистить</span>
            </div>
        </div>
    </div>
@endsection