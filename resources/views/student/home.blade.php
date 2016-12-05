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
                <span class="clear">Очистить</span>
            </div>
        </div>
    </div>
    @include('shared.error-modal')
@endsection