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
    <div class="layer" data-bind="with: current.result">
        <div class="details-row">
            <div class="details-column width-25p">
                <div class="details-row">
                    <div class="details-column width-100p">
                        <label class="title">Дата&nbsp;прохождения&nbsp;теста</label>
                        <span class="info" data-bind="text: dateTime.date"></span>
                    </div>
                </div>
                <div class="details-row">
                    <div class="details-column width-100p">
                        <label class="title">Номер&nbsp;попытки&nbsp;
                            <span class="coloredin-patronus bold"
                                  data-bind="visible: $root.current.results().length,
                                  click: $root.actions.results.view">(Все&nbsp;попытки)</span>
                        </label>
                        <span class="info" data-bind="text: attempt"></span>
                        <span class="info">/</span>
                        <span class="info" data-bind="text: $root.current.attempts()"></span>
                    </div>
                </div>
            </div>
            <!-- ko if: $root.current.test -->
                <!-- ko with: $root.current.test -->
                <div class="details-column width-38p">
                    <div class="details-row">
                        <div class="details-column width-100p">
                            <label class="title">Тест</label>
                            <span class="info" data-bind="text: subject"></span>
                        </div>
                    </div>
                    <div class="details-row">
                        <div class="details-column width-100p">
                            <label class="title">Дисциплина</label>
                            <span class="info" data-bind="text: disciplineName"></span>
                        </div>
                    </div>
                </div>
                <!-- /ko -->
            <!-- /ko -->
            <div class="details-column width-30p">
                <div class="details-row">
                    <div class="details-column width-100p">
                        <label class="title">ФИО&nbsp;студента</label>
                        <span class="info" data-bind="text: user.lastName() + ' ' +
                                                            user.firstName() + ' ' +
                                                            user.patronymic()"></span>
                    </div>
                </div>
                <div class="details-row">
                    <div class="details-column width-100p">
                        <label class="title">Оценка</label>
                        <!-- ko if: mark() !== null -->
                        <span class="radio-important" data-bind="text: mark() + '/100'"></span>
                        <!-- /ko -->
                        <span class="radio-negative" data-bind="if: mark() === null">Требуется проверка</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layer">
        <div class="details" data-bind="foreach: current.answers">
            <div class="details-row special-item"
                 data-bind="click: $root.actions.answer.show,
                 css: {'current': $root.current.answer().id() === id()}">
                <div class="details-column width-98p">
                    <!-- ko if: rightPercentage() === null -->
                    <span title="Требуется проверка" class="tagged fa">&#xf06a;</span>
                    <!-- /ko -->
                    <span data-bind="text: $root.actions.answer.fit.question($data)"></span>
                </div>
            </div>
            <!-- ko if: $root.current.answer().id() === id() -->
            <div class="details-row special-item-details" data-bind="with: $root.current.answer">
                <div class="details-column float-right">
                    <div class="result-setter">
                        <label class="title">Правильность ответа</label>
                        <!-- ko if: rightPercentage() !== null && !$root.current.mark.isInput() -->
                        <span class="radio-important" data-bind="text: rightPercentage, click: $root.actions.mark.edit"></span>
                        <!-- /ko -->
                        <!-- ko if: rightPercentage() === null && !$root.current.mark.isInput() -->
                        <span class="radio-important" data-bind="text: $root.current.mark.value, click: $root.actions.mark.edit"></span>
                        <!-- /ko -->
                        <!-- ko if: $root.current.mark.isInput() -->
                        <input class="text-center" type="text" id="iResultMark" validate
                               data-bind="value: $root.current.mark.value,
                               validationElement: $root.current.mark.value,
                               event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
                        <span class="fa radio-important" data-bind="click: $root.actions.mark.approve">&#xf00c;</span>
                        <span class="fa radio-important" data-bind="click: $root.actions.mark.cancel">&#xf00d;</span>
                        <!-- /ko -->
                    </div>
                </div>
                <div class="details-column width-80p">
                    <div class="details-row">
                        <div class="details-column">
                            <span class="fa icon">&#xf128;</span>
                            <span class="text" data-bind="text: question().text"></span>
                        </div>
                    </div>
                    <div class="details-column">
                        <span class="fa icon">&#xf27a;</span>
                        <span class="text" class="text" data-bind="text: answer"></span>
                    </div>
                </div>
            </div>
            <!-- /ko -->
        </div>
    </div>
    <!-- /ko -->
</div>
@include('admin.shared.error-modal')
@endsection

<div class="g-hidden">
    <div class="box-modal" id="attempts-modal">
        <div class="box-modal_close arcticmodal-close">закрыть</div>
        <div class="layer zero-margin width-auto">
            <h3>Все попытки</h3>
            <!-- ko foreach: $root.current.results -->
            <div class="item" data-bind="click: $root.actions.results.select">
                <span data-bind="text: attempt() + ')'"></span>
                <span data-bind="if: mark() !== null">
                        <span data-bind="text: mark"></span>
                        <span>/100</span>
                    </span>
                <span data-bind="if: mark() === null">Требуется проверка</span>
                <span class="float-right date-string" data-bind="text: dateTime.date.parseDate()"></span>
            </div>
            <!-- /ko -->
        </div>
    </div>
</div>