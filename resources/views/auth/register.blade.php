@extends('layouts.app')
@section('title', 'Регистрация')
@section('javascript')
    <script src="{{ URL::asset('js/auth/register.js')}}"></script>
@endsection
@section('content')
    <div class="register">
        <div>
            <h2>Заявка на регистрацию</h2>
        </div>
        <div>
            <input type="text" data-bind="value: user().name.last" placeholder="Фамилия">
        </div>
        <div>
            <input type="text" data-bind="value: user().name.first" placeholder="Имя">
        </div>
        <div>
            <input type="text" data-bind="value: user().name.patronymic" placeholder="Отчество">
        </div>
        <div>
            <input type="text" data-bind="value: user().email" placeholder="E-mail">
        </div>
        <div>
            <input type="password" data-bind="value: user().password" placeholder="Пароль">
        </div>
        <div>
            <select data-bind="options: groups, optionsText: 'name', value: user().group, optionsCaption: 'Выберите группу'"></select>
        </div>
        <div>
            <button data-bind="click: register">Отправить</button>
            <a href="/login">Войти</a>
            <div class="clear"></div>
        </div>
    </div>

<div class="g-hidden">
    <div class="box-modal" id="register-info">
        <!-- ko if: registerResult()-->
        <h3 data-bind="text: registerResult().message"></h3>
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
    <span id="question_tooltip">
        <span data-bind="validationMessage: $root.user().name.email"></span>
    </span>
</div>
@endsection
