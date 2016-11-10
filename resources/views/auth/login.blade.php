@extends('layouts.app')
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
@endsection
{{--@section('content')--}}
{{--<div class="container">--}}
    {{--<div class="row">--}}
        {{--<div class="col-md-8 col-md-offset-2">--}}
            {{--<div class="panel panel-default">--}}
                {{--<div class="panel-heading">Login</div>--}}
                {{--<div class="panel-body">--}}
                    {{--<form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">--}}
                        {{--{{ csrf_field() }}--}}

                        {{--<div class="form-group{{ $errors->has('login') ? ' has-error' : '' }}">--}}
                            {{--<label for="login" class="col-md-4 control-label">Login</label>--}}

                            {{--<div class="col-md-6">--}}
                                {{--<input id="login"  class="form-control" name="login" value="{{ old('login') }}" required autofocus>--}}

                                {{--@if ($errors->has('login'))--}}
                                    {{--<span class="help-block">--}}
                                        {{--<strong>{{ $errors->first('login') }}</strong>--}}
                                    {{--</span>--}}
                                {{--@endif--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">--}}
                            {{--<label for="password" class="col-md-4 control-label">Password</label>--}}

                            {{--<div class="col-md-6">--}}
                                {{--<input id="password" type="password" class="form-control" name="password" required>--}}

                                {{--@if ($errors->has('password'))--}}
                                    {{--<span class="help-block">--}}
                                        {{--<strong>{{ $errors->first('password') }}</strong>--}}
                                    {{--</span>--}}
                                {{--@endif--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="form-group">--}}
                            {{--<div class="col-md-6 col-md-offset-4">--}}
                                {{--<div class="checkbox">--}}
                                    {{--<label>--}}
                                        {{--<input type="checkbox" name="remember"> Remember Me--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="form-group">--}}
                            {{--<div class="col-md-8 col-md-offset-4">--}}
                                {{--<button type="submit" class="btn btn-primary">--}}
                                    {{--Login--}}
                                {{--</button>--}}

                                {{--<a class="btn btn-link" href="{{ url('/password/reset') }}">--}}
                                    {{--Forgot Your Password?--}}
                                {{--</a>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</form>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
{{--@endsection--}}
