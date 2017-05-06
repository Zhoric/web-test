<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{--<title>{{ config('app.name', 'Laravel') }}</title>--}}
    <title>@yield('title')</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('images/favicon.ico')}}"/>

    <link rel="stylesheet" href="{{ URL::asset('css/styles.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/jquery.arcticmodal.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/tooltipster.bundle.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/tooltipster-sideTip-light.min.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/simple.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/auth.css')}}" />

    <script src="{{ URL::asset('js/min/auth.js')}}"></script>

@yield('javascript')

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
    <script>
        var loading = $(".LoadingImage");
        $(document).ajaxStart(function () {
            loading.show();
        });

        $(document).ajaxStop(function () {
            loading.hide();
        });
        ko.validation.init({
            messagesOnModified: true,
            insertMessages:false,
            errorsAsTitle: true
        });
        ko.validation.locale('ru-RU');
    </script>
</head>
<body>
    @yield('content')
    <div class="g-hidden">
        <div class="box-modal" id="errors-modal">
            <div>
                <div>
                    <span class="fa">&#xf071;</span>
                    <h3>Произошла ошибка</h3>
                    <h4 data-bind="text: $root.errors.message"></h4>
                </div>
                <div class="height-30">
                    <button class="approve" data-bind="click: $root.errors.accept">OK</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
