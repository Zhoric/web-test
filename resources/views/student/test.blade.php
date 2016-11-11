@extends('student.layout')
@section('title', 'Тест')
@section('javascript')
    <script src="{{ URL::asset('js/student/test.js')}}"></script>
@endsection

@section('content')
    <div class="content">
        <!-- ko if: current.question() -->
        <div class="question-head">
            <h3 data-bind="text: current.question().text"></h3>
            <h1 data-bind="text: current.timeLeft"></h1>
        </div>

        <!-- ko if: current.answers().length -->
        <div class="question-answers" data-bind="foreach: current.answers">
            <!-- ko if: $root.current.question().type() === 1 -->
            <div class="container">
                <input data-bind="attr: {id: id}, checked: $root.current.singleAnswer, value: id" type="radio" group="answers">
                <label data-bind="text: text, attr: {for: id}"></label>
            </div>
            <!-- /ko -->
            <!-- ko if: $root.current.question().type() === 2-->
            <div class="container">
                <input type="checkbox" data-bind="attr: {id: id}, checked: isRight" >
                <label data-bind="text: text, attr: {for: id}"></label> </br>
            </div>
            <!-- /ko -->
        </div>
        <!-- /ko -->
        <!-- ko if: $root.current.question().type() === 3 -->
        <div class="question-answers">
            <label>Введите свой ответ</label> </br>
            <input data-bind="value: $root.current.answerText" type="text">
        </div>
        <!-- /ko -->
        <!-- ko if: $root.current.question().type() === 4 -->
        <div class="question-answers">
            <label>Введите свой ответ</label> </br>
            <textarea data-bind="value: $root.current.answerText" type=""></textarea>
        </div>
        <!-- /ko -->
        <button id="next-question" data-bind="click: $root.actions.answer">Ответить</button>
        <!-- /ko -->


        <!-- ko if: $root.current.testResult() -->
            <div class="test-results">
                <h2>Результат теста</h2>
                <div class="result-text">
                    <!-- ko if: $root.current.testResult().mark() -->
                        <span>Ваш результат составляет: <span>50/100</span> </span>
                    <!-- /ko -->
                    <!-- ko if: !$root.current.testResult().mark() -->
                    <span>Результат вашего теста вы сможете узнать после того, как преподаватель проверит ваши ответы на открытые вопросы.</span>
                    <!-- /ko -->

                </div>
                <button data-bind="">Вернуться на главную</button>
            </div>
        <!-- /ko -->
    </div>
@endsection