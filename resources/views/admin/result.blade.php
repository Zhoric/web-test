@extends('shared.layout')
@section('title', 'Результат теста')
@section('javascript')
    <link rel="stylesheet" href="{{ URL::asset('css/knockout-file-bindings.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/knockout-file-bindings.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/tooltipster.bundle.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/tooltipster-sideTip-light.min.css')}}"/>
    <script src="{{ URL::asset('js/knockout-file-bindings.js')}}"></script>
    <script src="{{ URL::asset('js/knockout.validation.js')}}"></script>
    <script src="{{ URL::asset('js/tooltipster.bundle.js')}}"></script>
    <script src="{{ URL::asset('js/helpers/common.js')}}"></script>
    <script src="{{ URL::asset('js/admin/result.js')}}"></script>
@endsection

@section('content')
<div class="content result-details">
    <div class="org-info" data-bind="if: current.result()">
        <div class="main-info width100" data-bind="with: current.result">
            <div class="name">
                <label>ФИО студента</label></br>
                <span data-bind="text: user.lastName"></span>
                <span data-bind="text: user.firstName"></span>
                <span data-bind="text: user.patronymic"></span>
            </div>
            <div class="date">
                <label>Дата прохождения теста</label></br>
                <span data-bind="text: dateTime.date"></span>
            </div>
            <!-- ko if: $root.current.test -->
                <!-- ko with: $root.current.test -->
                <div class="test">
                    <label>Тест</label></br>
                    <span data-bind="text: subject"></span>
                </div>
                <div class="discipline">
                    <label>Дисциплина</label></br>
                    <span data-bind="text: disciplineName"></span>
                </div>
                <!-- /ko -->
            <!-- /ko -->
            <div class="mark">
                <label>Оценка</label></br>
                <!-- ko if: mark() !== null -->
                <span data-bind="text: mark"></span>
                <span>/100</span>
                <!-- /ko -->
                <span class="not-ok" data-bind="if: mark() === null">Требуется проверка</span>
            </div>
            <div class="attempts">
                <label>Номер попытки &nbsp;<span class="clickable" data-bind="if: attempt() > 1">(Предыдущие попытки)</span></label></br>
                <span data-bind="text: attempt"></span>
                <span>/</span>
                <span data-bind="text: $root.current.attempts()"></span>
            </div>
        </div>
    </div>

    <div class="answers" data-bind="foreach: current.answers">
        <div class="answer" data-bind="click: $root.actions.answer.show, css: {'current': $root.current.answer().id() === id()}">
            <span data-bind="text: $index() + 1"></span>&nbsp;
            <span data-bind="text: question.text"></span>
            <!-- ko if: rightPercentage() === null -->
            <span class="tagged-label fa">&#xf123;</span>
            <!-- /ko -->
        </div>
        <!-- ko if: $root.current.answer().id() === id() -->
        <div class="answer-details" data-bind="with: $root.current.answer">
            <div class="question">
                <span class="fa icon">&#xf128;</span>
                <span class="text" data-bind="text: question().text"></span>
            </div>
            <div class="answer-text">
                <span class="fa icon">&#xf24a;</span>
                <span class="text" data-bind="text: answer"></span>
            </div>
            <div class="mark">
                <label>Правильность ответа</label></br>
                <!-- ko if: rightPercentage() !== null && !$root.current.mark.isInput() -->
                <span data-bind="text: rightPercentage, click: $root.actions.mark.edit"></span>
                <!-- /ko -->
                <!-- ko if: rightPercentage() === null && !$root.current.mark.isInput() -->
                <span data-bind="text: $root.current.mark.value, click: $root.actions.mark.edit"></span>
                <!-- /ko -->
                <!-- ko if: $root.current.mark.isInput() -->
                <input type="text" data-bind="value: $root.current.mark.value">
                <span class="fa" data-bind="click: $root.actions.mark.approve">&#xf00c;</span>
                <span class="fa" data-bind="click: $root.actions.mark.cancel">&#xf00d;</span>
                <!-- /ko -->
            </div>
        </div>
        <!-- /ko -->
    </div>

</div>


{{--<div class="tooltip_templates">--}}
    {{--<span id="minutes_tooltip">--}}
        {{--<span data-bind="validationMessage: $root.current.question().minutes"></span>--}}
    {{--</span>--}}
    {{--<span id="seconds_tooltip">--}}
        {{--<span data-bind="validationMessage: $root.current.question().seconds"></span>--}}
    {{--</span>--}}
    {{--<span id="theme-name_tooltip">--}}
        {{--<span data-bind="validationMessage: $root.current.theme().name"></span>--}}
    {{--</span>--}}
    {{--<span id="question_tooltip">--}}
        {{--<span data-bind="validationMessage: $root.current.question().text"></span>--}}
    {{--</span>--}}
{{--</div>--}}

@endsection

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