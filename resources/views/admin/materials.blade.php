@extends('layouts.manager')
@section('title', 'Материалы')
@section('javascript')

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
                            <!-- ko if:  $root.current.disciplineMedias().length > 0-->
                            <!-- ko foreach: $root.current.disciplineMedias-->
                            <tr>
                                <td data-bind="text: $index()+1"></td>
                                <td><span data-bind="css: type" class="fa approve mini material-type"></span></td>
                                <td data-bind="text: name"></td>
                                <td class="action-holder">
                                    <button class="fa approve mini actions">&#xf0ec;</button>
                                    <button data-bind="click: $root.actions.discipline.start.removeMedia" class="fa remove mini actions">&#xf014;</button>
                                </td>
                            </tr>
                            <!-- /ko -->
                            <!-- /ko -->
                            <!-- ko if:  $root.current.disciplineMedias().length == 0-->
                            <tr>
                                <td class="empty" colspan="4"> Для данной дисциплины материалы отсутствуют</td>
                            </tr>
                            <!-- /ko -->
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

    <div id="elfinder"></div>

    <div class="g-hidden">
        <div class="box-modal removal-modal" id="remove-media-modal">
            <div class="layer zero-margin width-auto">
                <div class="layer-head">
                    <h3>Удалить выбранный материал для данной дисциплины?</h3>
                </div>
                <div class="layer-body">
                    <div class="details-row float-buttons">
                        <button class="cancel arcticmodal-close">Отмена</button>
                        <button data-bind="click: $root.actions.discipline.end.removeMedia" class="remove arcticmodal-close">Удалить</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
