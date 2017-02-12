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
        <div class="layer">
            <div class="layer-head">
                <div class="timer" data-bind="if: $root.timer()">
                    <span data-bind="text: $root.current.timeLeft">3:15</span>
                </div>
                <label class="title" data-bind="text: 'Дисциплина: ' + $root.current.test.discipline()"></label>
                <label class="title" data-bind="text: 'Тест: ' + $root.current.test.name()"></label>
            </div>
            <div class="layer-body" data-bind="if: current.question()">
                <div class="question">
                    <span data-bind="text: current.question().text"></span>
                </div>
                <div class="question-image" data-bind="if: current.question().image()">
                    <img data-bind="attr: {src: '/' + current.question().image()}, click: $root.actions.image.expand"/>
                </div>
                <div class="answers" data-bind="if: $root.current.answers().length && $root.current.question().type() === 1">
                    <!-- ko foreach: $root.current.answers-->
                    <input type="radio" group="answers" class="custom-radio"
                           data-bind="attr: {id: id}, checked: $root.current.singleAnswer, value: id" >
                    <label data-bind="text: text, attr: {for: id}"></label>
                    <!-- /ko -->
                </div>
                <div class="answers" data-bind="if: $root.current.answers().length && $root.current.question().type() === 2">
                    <!-- ko foreach: $root.current.answers -->
                    <input type="checkbox" class="custom-checkbox"
                           data-bind="attr: {id: id}, checked: isRight" >
                    <label data-bind="text: text, attr: {for: id}"></label>
                    <!-- /ko -->
                </div>
                <div class="answers" data-bind="if: $root.current.question().type() === 3">
                    <input data-bind="value: $root.current.answerText" type="text" placeholder="Введите свой ответ">
                </div>
                <div class="answers" data-bind="if: $root.current.question().type() === 4">
                    <textarea data-bind="value: $root.current.answerText" placeholder="Введите свой ответ"></textarea>
                </div>
                <div class="action-holder">
                    <button class="float-right approve answer" data-bind="click: $root.actions.answer">Ответить</button>
                </div>
            </div>
            <div class="layer-body" data-bind="if: $root.current.testResult()">
                <h1 class="text-center">Результат</h1>
                <div class="test-result" data-bind="with: $root.current.testResult">
                    <div class="student">
                        <span data-bind="text: user.lastName"></span>
                        <span data-bind="text: user.firstName"></span>
                        <span data-bind="text: user.patronymic"></span>
                    </div>
                    <div class="mark">
                        <span class="no-mark" data-bind="if: !mark()">Результат вашего теста вы сможете узнать после того,
                            как преподаватель проверит ваши ответы на открытые вопросы.</span>
                        <span data-bind="text: mark"></span>
                    </div>
                    <div class="date">
                        <span data-bind="text: dateTime.date"></span>
                    </div>
                </div>
                <div class="action-holder">
                    <button class="approve home" data-bind="click: $root.actions.home">Вернуться на главную</button>
                </div>
            </div>
        </div>

    @include('shared.error-modal')
@endsection


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