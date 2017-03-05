@extends('layouts.app')
@section('title', 'Вход')
@section('javascript')
    <script src="{{ URL::asset('js/auth/login.js')}}"></script>
@endsection

@section('content')
    <div class="login">
        <div>
            <img class="logo" src="{{ URL::asset('images/sevsu-logo.png')}}"/>
        </div>
        <div class="university">
            <span>Севастопольский государственный университет</span>
        </div>
        <div class="header">
            <span>Система контроля знаний</span>
        </div>
        <div>
            <input type="text" data-bind="value: user.email, event: {keypress: $root.enter}" placeholder="Логин">
        </div>
        <div>
            <input type="password" data-bind="value: user.password, event: {keypress: $root.enter}" placeholder="Пароль">
        </div>
        <div>
            <button data-bind="click: $root.login">Войти</button>
        </div>
        <div class="not-registered">
            <span>Ещё&nbsp;не&nbsp;зарегистрированы?</span>
            <a href="/register">Отправить&nbsp;заявку</a>
        </div>
    </div>
@endsection