<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title')</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('images/favicon.ico')}}"/>

    <link rel="stylesheet" href="{{ URL::asset('css/styles.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/jquery.arcticmodal.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/tooltipster.bundle.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/tooltipster-sideTip-light.min.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/simple.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/admin.css')}}" />

    <script src="{{ URL::asset('js/min/manager-common.js')}}"></script>
    {{--<script src="{{ URL::asset('js/es5-shim.js')}}"></script>--}}
    {{--<script src="{{ URL::asset('js/jquery-3.1.1.js')}}"></script>--}}
    {{--<script src="{{ URL::asset('js/jquery.cookie.js')}}"></script>--}}
    {{--<script src="{{ URL::asset('js/knockout-3.4.0.debug.js')}}"></script>--}}
    {{--<script src="{{ URL::asset('js/knockout.validation.js')}}"></script>--}}
    {{--<script src="{{ URL::asset('js/knockout.mapping.js')}}"></script>--}}
    {{--<script src="{{ URL::asset('js/ru-RU.js')}}"></script>--}}
    {{--<script src="{{ URL::asset('js/helpers/ko-postget.js')}}"></script>--}}
    {{--<script src="{{ URL::asset('js/tooltipster.bundle.js')}}"></script>--}}
    {{--<script src="{{ URL::asset('js/jquery.arcticmodal.js')}}"></script>--}}
    {{--<script src="{{ URL::asset('js/helpers/common.js')}}"></script>--}}
    {{--<script src="{{ URL::asset('js/helpers/ko-copy.js')}}"></script>--}}
    {{--<script src="{{ URL::asset('js/helpers/ko-pager.js')}}"></script>--}}
    {{--<script src="{{ URL::asset('js/helpers/modals.js')}}"></script>--}}
    {{--<script src="{{ URL::asset('js/helpers/ko-events.js')}}"></script>--}}
    {{--<script src="{{ URL::asset('js/helpers/tooltip.js')}}"></script>--}}
    {{--<script src="{{ URL::asset('js/helpers/user-info.js')}}"></script>--}}
    @yield('javascript')
</head>
<body>
@include('shared.common-script')
<div class="page-wrap">
    <div class="menu">
        <a href="/admin/main" data-bind="css: {'current': $root.page() === menu.admin.main}">Главная</a>
        <!-- ko if: $root.user.role() === role.admin.name -->
        <a href="/admin/lecturers" data-bind="css: {'current': $root.page() === menu.admin.lecturers}">Преподаватели</a>
        <!-- /ko -->
        <a href="/admin/groups" data-bind="css: {'current': $root.page() === menu.admin.groups}">Группы</a>
        <a href="/admin/students" data-bind="css: {'current': $root.page() === menu.admin.students}">Студенты</a>
        <a href="/admin/disciplines" data-bind="css: {'current': $root.page() === menu.admin.disciplines}">Дисциплины</a>
        <a href="/admin/tests" data-bind="css: {'current': $root.page() === menu.admin.tests}">Тесты</a>
        <a href="/admin/results" data-bind="css: {'current': $root.page() === menu.admin.results}">Результаты</a>
        <a  class="user" data-bind="text: $root.user.name()"></a>
        <div class="menu-dd">
            <!-- ko if: $root.user.role() === role.admin.name -->
            <a href="/admin/setting">Администрирование</a>
            <!-- /ko -->
            <!-- ko if: $root.user.role() === role.lecturer.name -->
            <a href="/admin/help/navigation">Помощь</a>
            <!-- /ko -->
            <a data-bind="click: $root.user.password.change.bind($root)">Сменить пароль</a>
            <a href="/logout">Выход</a>
        </div>
    </div>
    @yield('content')
    @include('shared.common-modals')
</div>
@include('shared.footer')
</body>
</html>