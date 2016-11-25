@extends('layouts.app')
@section('title', 'Регистрация')
@section('javascript')
    <link rel="stylesheet" href="{{ URL::asset('css/tooltipster.bundle.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/tooltipster-sideTip-light.min.css')}}"/>
    <script src="{{ URL::asset('js/tooltipster.bundle.js')}}"></script>
    <script src="{{ URL::asset('js/auth/register.js')}}"></script>
@endsection
@section('content')
    <div class="register">
        <div>
            <h2>Заявка на регистрацию</h2>
        </div>
        <div>
            <input type="text" tooltip-mark="last_tooltip" placeholder="Фамилия"
                   data-bind="value: user().name.last,
                   valueUpdate:'keydown',
                   event: {focusin: $root.events.focusin, focusout: $root.events.focusout}">
        </div>
        <div>
            <input type="text" tooltip-mark="first_tooltip" placeholder="Имя"
                   data-bind="value: user().name.first,
                   valueUpdate:'keydown', event: {focusin: $root.events.focusin, focusout: $root.events.focusout}">
        </div>
        <div>
            <input type="text" tooltip-mark="patronymic_tooltip" placeholder="Отчество"
                   data-bind="value: user().name.patronymic,
                   valueUpdate:'keydown',
                   event: {focusin: $root.events.focusin, focusout: $root.events.focusout}">
        </div>
        <div>
            <input type="text" tooltip-mark="email_tooltip" placeholder="E-mail"
                   data-bind="value: user().email,
                   valueUpdate:'keydown',
                   event: {focusin: $root.events.focusin, focusout: $root.events.focusout}">
        </div>
        <div>
            <input type="password" tooltip-mark="password_tooltip" placeholder="Пароль"
                   data-bind="value: user().password,
                   valueUpdate:'keydown',
                   event: {focusin: $root.events.focusin, focusout: $root.events.focusout}">
        </div>
        <div>
            <select  tooltip-mark="group_tooltip"
                     data-bind="options: groups, optionsText: 'name',
                     value: user().group,
                     optionsCaption: 'Выберите группу'">
            </select>
        </div>
        <div>
            <button tooltip-mark="overall_tooltip" data-bind="click: register">Отправить</button>
            <a href="/login">Войти</a>
            <div class="clear"></div>
        </div>
    </div>

<div class="g-hidden">
    <div class="box-modal" id="register-info">
        <!-- ko if: registerResult()-->
        <h3 data-bind="text: registerResult().Data"></h3>
        <button data-bind="click: acceptInformation">OK</button>
        <!-- /ko -->
    </div>
</div>
<div class="tooltip_templates">
    <span id="last_tooltip">
        <span data-bind="validationMessage: $root.user().name.last"></span>
    </span>
    <span id="first_tooltip">
        <span data-bind="validationMessage: $root.user().name.first"></span>
    </span>
    <span id="patronymic_tooltip">
        <span data-bind="validationMessage: $root.user().name.patronymic"></span>
    </span>
    <span id="email_tooltip">
        <span data-bind="validationMessage: $root.user().email"></span>
    </span>
    <span id="password_tooltip">
        <span data-bind="validationMessage: $root.user().password"></span>
    </span>
    <span id="group_tooltip">
        <span>Выбор группы обязателен</span>
    </span>
    <span id="overall_tooltip">
        <span>Не все поля заполнены</span>
    </span>
</div>
@endsection
