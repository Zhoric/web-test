<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title')</title>

    <link rel="stylesheet" href="{{ URL::asset('css/jquery.arcticmodal.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/simple.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/styles.css')}}"/>

    @yield('style')

    <script src="{{ URL::asset('js/jquery-3.1.1.js')}}"></script>
    <script src="{{ URL::asset('js/knockout-3.4.0.debug.js')}}"></script>
    <script src="{{ URL::asset('js/knockout.mapping.js')}}"></script>
    <script src="{{ URL::asset('js/helpers/ko-errors.js')}}"></script>
    <script src="{{ URL::asset('js/helpers/ko-postget.js')}}"></script>
    <script src="{{ URL::asset('js/helpers/common.js')}}"></script>
    <script src="{{ URL::asset('js/jquery.arcticmodal.js')}}"></script>

    @yield('javascript')
</head>
<body>
    @yield('menu')
    @yield('content')
    <div class="footer"></div>
</body>
</html>