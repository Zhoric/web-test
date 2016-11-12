@extends('layouts.app')
@section('title', 'Вход')
@section('javascript')
    <script src="{{ URL::asset('js/auth/login.js')}}"></script>
@endsection

@section('content')
    <div class="register login">
        <div>
            <h2>Вход</h2>
        </div>
        <div>
            <input type="text" data-bind="value: user.email" placeholder="Логин">
        </div>
        <div>
            <input type="password" data-bind="value: user.password" placeholder="Пароль">
        </div>
        <div>
            <button data-bind="click: $root.login">Войти</button>
            <a href="/register">Регистрация</a>
            <div class="clear"></div>
        </div>
    </div>
    <div class="g-hidden">
        <div class="box-modal" id="login-info">
            <!-- ko if: loginResult()-->
            <h3 data-bind="text: loginResult().message"></h3>
            <button data-bind="click: acceptInformation">OK</button>
            <!-- /ko -->
        </div>
    </div>
@endsection