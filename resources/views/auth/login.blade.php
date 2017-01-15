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
    @include('shared.error-modal')
@endsection