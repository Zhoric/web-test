@extends('student.layout')
@section('title', 'Главная')
@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/site.css')}}" />
@endsection
@section('javascript')
    <script src="{{ URL::asset('js/student/home.js')}}"></script>
@endsection

@section('menu')
    @include('student.menu')
@endsection

@section('content')
    <div class="content">
        <div class="items">
            <!-- ko foreach: $root.current.rows -->
            <div class="row" data-bind="foreach: disciplines">
                {{--<div class="discipline">--}}
                    {{--<div class="discipline-head" data-bind="click: $root.actions.details">--}}
                        {{--<span data-bind="text: discipline.abbreviation"></span>--}}
                    {{--</div>--}}
                    {{--<div class="discipline-body">--}}
                        {{--<span data-bind="text: testsPassed() + '/' + testsCount()"></span>--}}
                    {{--</div>--}}
                {{--</div>--}}
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
                    <div class="footer"></div>
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
                <input class="custom-radio" type="radio" id="all-disciplines"/><label for="all-disciplines">Все</label>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" id="has-tests"/><label for="has-tests">Имеются не пройденные тесты</label>
            </div>
            <div class="filter-block">
                <span class="clear">Очистить</span>
            </div>
        </div>
    </div>
    @include('shared.error-modal')
@endsection