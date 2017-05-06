@extends('layouts.student')
@section('title', 'Результаты')
@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/site.css')}}" />
@endsection
@section('javascript')
    <script src="{{ URL::asset('js/min/student-results.js')}}"></script>
@endsection

@section('menu')
    @include('student.menu')
@endsection

@section('content')
    <div class="content">
        <div class="items">
            <div class="items-head">
                <h1>Просмотр результатов</h1>
            </div>
            <!-- ko if: $root.filter.discipline() &&  !$root.current.results().length -->
            <h3 class="text-center">По данному запросу ничего не найдено</h3>
            <!-- /ko -->
            <!-- ko if: !$root.filter.discipline() -->
            <h3 class="text-center">Пожалуйста, выберите дисциплину</h3>
            <!-- /ko -->
            <div class="items-body" data-bind="foreach: $root.current.results">
                <div class="result" data-bind="click: $root.actions.show.result, css: {'current': $root.current.id() === id() && $root.current.details()}">
                    <div class="details-row clear">
                        <div class="details-column float-right">
                            <div class="float-right">
                                <div class="details-row">
                                    <div class="details-column float-right">
                                        <label class="title">Оценка</label>
                                    </div>
                                </div>
                                <div class="details-row">
                                    <div class="details-column width-100p zero-margin">
                                        <!-- ko if: mark() !== null -->
                                        <span class="info coloredin-patronus" data-bind="text: mark() + '/' + $root.current.markScale()"></span>
                                        <!-- /ko -->
                                        <span class="info coloredin-crimson" data-bind="if: mark() === null">Ожидает&nbsp;проверки</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="details-column width-60p">
                            <label class="title">Название теста</label>
                            <span class="info" data-bind="text: testName"></span>
                        </div>
                    </div>
                    <!-- ko if: $root.current.id() === id() -->
                    <div class="details-row clear">
                        <div class="details-column float-right">
                            <div class="float-right">
                                <div class="details-row">
                                    <div class="details-column float-right zero-margin">
                                        <label class="title">Номер&nbsp;попытки</label>
                                    </div>
                                </div>
                                <div class="details-row">
                                    <div class="details-column width-100p">
                                        <span class="info float-right" data-bind="text: attempt"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="details-column width-60p">
                            <label class="title">Дата&nbsp;прохождения</label>
                            <span class="info" data-bind="text: commonHelper.parseDate(dateTime.date())"></span>
                        </div>
                    </div>
                    <!-- /ko -->
                </div>
                <!-- ko if: $root.current.details() && $root.current.id() === id() -->
                <div class="details result-details">
                    <div class="details-row" data-bind="with: $root.current.details">
                        <h3 class="text-center">Вопросы, на которые были даны неправильные ответы</h3>
                        <div class="" data-bind="foreach: answers">
                            <div class="item" data-bind="click: $root.actions.show.question">
                                <span data-bind="text: question.text.cut(87)"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /ko -->
            </div>
        </div>
        <div class="filter">
            <div class="filter-block">
                <label class="title">Дисциплина</label>
                <select data-bind="options: $root.initial.disciplines,
                       optionsText: 'name',
                       value: $root.filter.discipline,
                       optionsCaption: 'Выберите дисциплину'"></select>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" id="all-tests" value="all-tests"
                       data-bind="checked: $root.filter.state"/>
                <label for="all-tests">Все</label>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" id="checked-tests" value="checked-tests"
                       data-bind="checked: $root.filter.state"/>
                <label for="checked-tests">Проверен</label>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" id="unchecked-tests" value="unchecked-tests"
                       data-bind="checked: $root.filter.state"/>
                <label for="unchecked-tests">Ожидает проверки</label>
            </div>
            <div class="filter-block">
                <span class="clear" data-bind="click: $root.filter.clear">Очистить</span>
            </div>
        </div>
    </div>
@endsection
<div class="g-hidden">
    <div class="box-modal" id="question-details-modal">
        <div class="box-modal_close arcticmodal-close"><span class="fa modal-close">&#xf00d;</span></div>
        <div class="layer width-auto zero-margin">
            <div class="layer-body">
                <div class="details-row" data-bind="if: $root.current.question">
                    <!-- ko with: $root.current.question -->
                    <div class="details-column width-98p">
                        <label class="title">Вопрос</label>
                        <span class="info" data-bind="text: question.text"></span>
                    </div>
                    <div class="details-column width-98p">
                        <label class="title">Ваш ответ</label>
                        <span class="info" data-bind="text: answer.parseAnswer() "></span>
                    </div>
                    <!-- /ko -->
                </div>
            </div>
        </div>
    </div>
</div>

