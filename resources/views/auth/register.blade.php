@extends('layouts.app')
@section('title', 'Регистрация')
@section('javascript')
    <script src="{{ URL::asset('js/auth/register.js')}}"></script>
@endsection
@section('content')
    <div class="register">
        <div>
            <img class="register-logo" src="{{ URL::asset('images/sevsu-logo.png')}}"/>
        </div>
        <div class="head">
            <span>Заявка на регистрацию</span>
        </div>
        <div>
            <input type="text" id="iAuthLastName"  placeholder="Фамилия" validate
                   data-bind="value: user().lastname,
                   valueUpdate:'keydown',
                   validationElement: user().lastname,
                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
        </div>
        <div>
            <input type="text" id="iAuthFirstName" placeholder="Имя" validate
                   data-bind="value: user().firstname,
                   valueUpdate:'keydown',
                   validationElement: user().firstname,
                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
        </div>
        <div>
            <input type="text" id="iAuthPatronymic" placeholder="Отчество" validate
                   data-bind="value: user().patronymic,
                   valueUpdate:'keydown',
                   validationElement: user().patronymic,
                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
        </div>
        <div>
            <input type="text" id="iAuthEmail" placeholder="E-mail" validate
                   data-bind="value: user().email,
                   valueUpdate:'keydown',
                   validationElement: user().email,
                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
        </div>
        <div>
            <input type="password" id="iAuthPassword" placeholder="Пароль" validate
                   data-bind="value: user().password,
                   valueUpdate:'keydown',
                   validationElement: user().password,
                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
        </div>
        <div>
            <select id="sAuthGroup" validate
                     data-bind="options: groups, optionsText: 'name',
                     value: user().group,
                     optionsCaption: 'Выберите группу',
                     validationElement: user().group,
                     event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
            </select>
        </div>
        <div>
            <button data-bind="click: $root.register">Отправить</button>
        </div>
        <div class="not-registered">
            <span>Уже&nbsp;зарегистрированы?</span>
            <a href="/login">Войти&nbsp;в&nbsp;систему</a>
        </div>
    </div>

<div class="g-hidden">
    <div class="box-modal" id="register-info">
        <!-- ko if: $root.registerResult.success() -->
        <h3 data-bind="text: $root.registerResult.message "></h3>
        <button data-bind="click: acceptInformation">OK</button>
        <!-- /ko -->
    </div>
</div>
@endsection
