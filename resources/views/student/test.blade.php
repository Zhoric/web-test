@extends('student.layout')
@section('title', 'Тест')
@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/test.css')}}"/>
@endsection
@section('javascript')
    <script src="{{ URL::asset('js/ace.js') }}"></script>
    <script src="{{ URL::asset('js/codeEditor/sendCode.js')}}"></script>
    <script src="{{ URL::asset('js/student/test.js')}}"></script>
@endsection

@section('content')
    <div class="content">
        <!-- ko if: current.question() -->
        <div class="question-head">
            <h3 data-bind="text: current.question().text"></h3>
            <h1 data-bind="text: current.timeLeft"></h1>
            <!-- ko if: current.question().image() -->
            <img data-bind="attr: {src: '/' + current.question().image()}, click: $root.actions.image.expand"/>
            <!-- /ko -->
        </div>

        <!-- ko if: $root.current.answers().length -->
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
        <!-- ko if: $root.current.question().type() === 5-->
        <div class="question-answers">
            <button class="write-code-btn" data-bind="click: $root.code.write">Ввести код</button>
            <label class="code-text" data-bind="text: $root.code.text"></label>
        </div>
        <!-- /ko -->
        <div class="next-question">
            <button data-bind="click: $root.actions.answer">Ответить</button>
        </div>
        <!-- /ko -->


        <!-- ko if: $root.current.testResult() -->
            <div class="test-results">
                <h2>Результат теста</h2>
                <div class="result-text">
                    <!-- ko if: $root.current.testResult().mark() !== null -->
                        <span class="text-middle">Ваш результат составляет: <span data-bind="text: $root.current.testResult().mark()"></span>/100 баллов.</span>
                    <!-- /ko -->
                    <!-- ko if: $root.current.testResult().mark() === null -->
                    <span>Результат вашего теста вы сможете узнать после того, как преподаватель проверит ваши ответы на открытые вопросы.</span>
                    <!-- /ko -->
                    <span class="date" data-bind="text: $root.current.testResult().dateTime()"></span>
                </div>
                <button data-bind="click: $root.actions.goHome">Вернуться на главную</button>
            </div>
        <!-- /ko -->
    </div>
    <div id="image-expander" data-bind="click: $root.actions.image.hide">
        <!-- ko if: $root.current.question() -->
            <!-- ko if: $root.current.question().image() -->
            <img data-bind="attr: {src: '/' + $root.current.question().image()}"/>
            <!-- /ko -->
        <!-- /ko -->
    </div>
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

<div class="g-hidden">
    <div class="box-modal" id="code-editor-modal">
        <div>
            <div id="editor"></div>
            <div class="code-task" data-bind="text: $root.code.task"></div>
            <input type="button" class="cancel" data-bind="click: $root.code.clear" value="Очистить">
            <input type="button" class="save arcticmodal-close" data-bind="click: $root.code.approve" value="Подтвердить">
        </div>
    </div>
</div>