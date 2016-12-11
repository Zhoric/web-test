@extends('student.layout')
@section('title', 'Результаты')
@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/site.css')}}" />
@endsection
@section('javascript')
    <script src="{{ URL::asset('js/student/results.js')}}"></script>
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
                                    <div class="details-column width-100p">
                                        <!-- ko if: mark() !== null -->
                                        <span class="radio-important" data-bind="text: mark() + '/100'"></span>
                                        <!-- /ko -->
                                        <span class="radio-negative" data-bind="if: mark() === null">Ожидает&nbsp;проверки</span>
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
                                    <div class="details-column float-right">
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
                            <label class="title">Дата прохождения</label>
                            <span class="info" data-bind="text: commonHelper.parseDate(dateTime.date())"></span>
                        </div>
                    </div>
                    <!-- /ko -->
                </div>
                <!-- ko if: $root.current.details() && $root.current.id() === id() -->
                <div class="details">
                    <div class="details-row result-details" data-bind="with: $root.current.details">
                        <div class="" data-bind="foreach: answers">
                            <div class="item" data-bind="click: $root.actions.show.question">
                                <span class="float-right radio-neutral" data-bind="if: rightPercentage() === null">&nbsp;Ожидает&nbsp;проверки</span>
                                <!-- ko if: rightPercentage() !== null -->
                                <span class="float-right radio-important" data-bind="text: rightPercentage() + '/100'"></span>
                                <!-- /ko -->
                                <span data-bind="text: question.text"></span>
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
                <select data-bind="options: $root.filter.disciplines,
                       optionsText: 'name',
                       value: $root.filter.discipline,
                       optionsCaption: 'Выберите дисциплину'"></select>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" id="checked-tests"/><label for="checked-tests">Проверен</label>
            </div>
            <div class="filter-block">
                <input class="custom-radio" type="radio" id="unchecked-tests"/><label for="unchecked-tests">Ожидает проверки</label>
            </div>
            <div class="filter-block">
                <span class="clear">Очистить</span>
            </div>
        </div>
    </div>
    @include('shared.error-modal')
@endsection
<div class="g-hidden">
    <div class="box-modal" id="question-details-modal">
        <div class="box-modal_close arcticmodal-close">закрыть</div>
        <div class="details-row" data-bind="if: $root.current.question">
            <!-- ko with: $root.current.question -->
            <div class="details-column width-98p">
                <label class="title">Оценка за вопрос</label>
                <span class="radio-negative" data-bind="if: rightPercentage() === null">Ожидает&nbsp;проверки</span>
                <!-- ko if: rightPercentage() !== null -->
                <span class="radio-important" data-bind="text: rightPercentage() + '/100'"></span>
                <!-- /ko -->
            </div>
            <div class="details-column width-98p">
                <label class="title">Вопрос</label>
                <span class="info" data-bind="text: question.text"></span>
            </div>
            <div class="details-column width-98p">
                <label class="title">Ответ</label>
                <span class="info" data-bind="text: commonHelper.parseAnswers(answer()) "></span>
            </div>
            <!-- /ko -->
        </div>
    </div>
</div>

