<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ URL::asset('css/styles.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/jquery.arcticmodal.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/simple.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/admin.css')}}" />
    <script src="{{ URL::asset('js/jquery-3.1.1.js')}}"></script>
    <script src="{{ URL::asset('js/knockout-3.4.0.debug.js')}}"></script>
    <script src="{{ URL::asset('js/knockout.mapping.js')}}"></script>
    <script src="{{ URL::asset('js/jquery.arcticmodal.js')}}"></script>
    <script src="{{ URL::asset('js/helpers/common.js')}}"></script>
    <script src="{{ URL::asset('js/helpers/ko-pager.js')}}"></script>
    <script src="{{ URL::asset('js/helpers/ko-errors.js')}}"></script>
    @yield('javascript')
</head>
<body>
    <div class="LoadingImage">
        <img src="{{ URL::asset('images/custom-spinner.gif')}}" />
    </div>
    <script>
        var loading = $(".LoadingImage");
        $(document).ajaxStart(function () {
            loading.show();
        });

        $(document).ajaxStop(function () {
            loading.hide();
        });
    </script>
    <div class="menu">
        <ul>
            <li><a href="/admin/main">Главная</a></li>
            <li>Преподаватели</li>
            <li><a href="/admin/groups">Группы</a></li>
            <li>Студенты</li>
            <li><a href="/admin/disciplines">Дисциплины</a></li>
            <li><a href="/admin/studyplans">Планы</a></li>
            <li><a href="/admin/tests">Тесты</a></li>
            <li><a href="/admin/results">Результаты</a></li>
            <li>Выход</li>
        </ul>
    </div>
    @yield('content')

    <div class="footer">

    </div>
</body>
</html>