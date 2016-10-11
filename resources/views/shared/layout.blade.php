<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ URL::asset('css/admin.css')}}" />
    <script src="{{ URL::asset('js/jquery-3.1.1.js')}}"></script>
    <script src="{{ URL::asset('js/knockout-3.4.0.debug.js')}}"></script>
    <script src="{{ URL::asset('js/knockout.mapping.js')}}"></script>
    @yield('javascript')
</head>
<body>
    <div class="menu">
        {{--<img src="{{ URL::asset('images/sevsu_logo.png')}}"/>--}}
        <ul>
            <li>Направления</li>
            <li>Группы</li>
            <li>Преподаватели</li>
            <li>Дисциплины</li>
            <li>Тесты</li>
            <li>Результаты</li>
            <li>Выход</li>
        </ul>
        <div></div>
    </div>
    @yield('content')

    <div class="footer">

    </div>
</body>
</html>