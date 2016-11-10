@extends('layouts.app')
@section('javascript')
    <script src="{{ URL::asset('js/auth/register.js')}}"></script>
@endsection
@section('content')
    <div class="register">
        <div>
            <h2>Заявка на регистрацию</h2>
        </div>
        <div>
            <input type="text" data-bind="value: user.name" placeholder="ФИО">
            <label data-bind="validationElement: user.name"></label>
        </div>
        <div>
            <input type="text" data-bind="value: user.email" placeholder="E-mail">
        </div>
        <div>
            <input type="password" data-bind="value: user.password" placeholder="Пароль">
        </div>
        <div>
            <label data-bind="validationElement: user.admissionYear"></label>
            <input type="text" data-bind="value: user.admissionYear" placeholder="Год поступления">
        </div>
        <div>
            <input type="text" data-bind="value: user.group" placeholder="Группа">
            {{--<div data-bind="template: {name: 'dropdown-items', foreach: groups}"></div>--}}
        </div>
        <div>
            <button data-bind="click: $root.register">Отправить</button>
            <a href="/login">Войти</a>
            <div class="clear"></div>
        </div>
    </div>
@endsection

<script type="text/html" id="dropdown-items">
    <div>
        <span data-bind="text: name"></span>
    </div>
</script>


{{--@section('content')--}}
{{--<div class="container">--}}
    {{--<div class="row">--}}
        {{--<div class="col-md-8 col-md-offset-2">--}}
            {{--<div class="panel panel-default">--}}
                {{--<div class="panel-heading">Register</div>--}}
                {{--<div class="panel-body">--}}
                    {{--<form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">--}}
                        {{--{{ csrf_field() }}--}}

                        {{--<div class="form-group{{ $errors->has('login') ? ' has-error' : '' }}">--}}
                            {{--<label for="name" class="col-md-4 control-label">Name</label>--}}

                            {{--<div class="col-md-6">--}}
                                {{--<input id="login" type="text" class="form-control" name="login" value="{{ old('login') }}" required autofocus>--}}

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

                        {{--<div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">--}}
                            {{--<label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>--}}

                            {{--<div class="col-md-6">--}}
                                {{--<input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>--}}

                                {{--@if ($errors->has('password_confirmation'))--}}
                                    {{--<span class="help-block">--}}
                                        {{--<strong>{{ $errors->first('password_confirmation') }}</strong>--}}
                                    {{--</span>--}}
                                {{--@endif--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="form-group">--}}
                            {{--<div class="col-md-6 col-md-offset-4">--}}
                                {{--<button type="submit" class="btn btn-primary">--}}
                                    {{--Register--}}
                                {{--</button>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</form>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
{{--@endsection--}}
