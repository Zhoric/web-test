@extends('layouts.manager')
@section('title', 'Материалы')
@section('javascript')


    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <!-- elFinder CSS (REQUIRED) -->
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('packages/barryvdh/elfinder/css/elfinder.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('packages/barryvdh/elfinder/css/theme.css') }}">

    <!-- elFinder JS (REQUIRED) -->
    <script src="{{ URL::asset('packages/barryvdh/elfinder/js/elfinder.min.js') }}"></script>

    <script src="{{ URL::asset('js/knockout.multiselect.js') }}"></script>
    <script src="{{ URL::asset('js/admin/materials.js')}}"></script>
@endsection

@section('content')
    <div class="content">
        <div class="items">
            <div class="items-head">
                <h1>Материалы</h1>
            </div>
            <div class="items-body">
                <!-- ko foreach: $root.current.disciplines -->
                <div class="item" data-bind="click: $root.actions.discipline.show, css: {'current': $root.current.discipline().id() === id()}">
                    <span data-bind="text: name"></span>
                </div>
                <!-- ko if: $root.mode() !== state.none && $data.id() === $root.current.discipline().id()-->

                <div class="details discipline">
                    <div class="details-row">
                        <table class="werewolf materials">
                            <thead>
                            <tr><th>№</th><th>Вид</th><th>Материалы</th><th>Действия</th></tr>
                            </thead>
                            <tbody>
                            <tr class="adder-row">
                                <td data-bind="click: $root.actions.discipline.addMedia" colspan="4">
                                    <span class="fa">&#xf067;</span>&nbsp;Добавить материал
                                </td>
                            </tr>
                            <tr>
                                <td data-bind="text: $index()+1"></td>
                                <td><span class="fa approve mini material-type">&#xf008;</span></td>
                                <td><a>Видео</a></td>
                                <td class="action-holder">
                                    <button class="fa approve mini actions">&#xf0f6;</button>
                                    <button class="fa remove mini actions">&#xf014;</button>
                                </td>
                            </tr>
                            <tr>
                                <td data-bind="text: $index()+1"></td>
                                <td><span class="fa approve mini material-type">&#xf001;</span></td>
                                <td><a>Аудио</a></td>
                                <td class="action-holder">
                                    <button class="fa approve mini actions">&#xf0f6;</button>
                                    <button class="fa remove mini actions">&#xf014;</button>
                                </td>
                            </tr>
                            <tr>
                                <td data-bind="text: $index()+1"></td>
                                <td><span class="fa approve mini material-type">&#xf15c;</span></td>
                                <td><a>Текст</a></td>
                                <td class="action-holder">
                                    <button class="fa approve mini actions">&#xf0f6;</button>
                                    <button class="fa remove mini actions">&#xf014;</button>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /ko -->
                <!-- /ko -->
            </div>
            @include('shared.pagination')
        </div>

        <div class="filter">
            <div class="filter-block">
                <label class="title">Наименование/аббревиатура&nbsp;дисциплины</label>
                <input type="text" data-bind="value: $root.filter.discipline, valueUpdate: 'keyup'" placeholder="Наименование/аббревиатура">
            </div>
            <div class="filter-block">
                <label class="title">Направление</label>
                <select data-bind="options: $root.multiselect.data,
                       optionsText: 'fullname',
                       value: $root.filter.profile,
                       optionsCaption: 'Выберите профиль'"></select>
            </div>
            <div class="filter-block">
                <span class="clear" data-bind="click: $root.filter.clear">Очистить</span>
            </div>
        </div>
    </div>

        <div id="elfinder">
        </div>


@endsection
