@extends('layouts.student')
@section('title', 'Материалы')
@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/site.css')}}" />
@endsection
@section('javascript')
    <script src="{{ URL::asset('js/min/student-materials.js')}}"></script>
@endsection
@section('menu')
    @include('student.menu')
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
                    <div class="details-row materials-details">
                        <div class="details-row materials-link">
                            <div class="details-column">
                                <label class="adder" data-bind="click: $root.actions.discipline.themes, css: {'current': $root.mode() === state.themes || $root.mode() === state.materials}">Темы</label>
                            </div>
                            <div class="details-column">
                                <label class="adder" data-bind="click: $root.actions.discipline.overall, css: {'current': $root.mode() === state.overall}">Учебно - методические материалы</label>
                            </div>

                        </div>
                        <!-- ko if: $root.mode() === state.overall  -->
                        <div class="details" data-bind="template: {name: 'overall-mode', data: $data}"></div>
                        <!-- /ko -->
                        <!-- ko if: $root.mode() === state.themes || $root.mode() === state.materials -->
                        <div class="details" data-bind="template: {name: 'themes-mode', data: $data}"></div>
                        <!-- /ko -->
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

    <div class="g-hidden">
        <div data-bind="css: $root.current.media().type()" class="box-modal multimedia-modal" id="multimedia-modal">
            <div class="layer zero-margin width-auto">
                <div class="layer-head">
                    <!-- ko if: $root.current.media().start() != null && $root.current.media().stop() != null -->
                    <h3 data-bind="text: $root.current.media().name"></h3>
                    <span class="anchor-modal-text"> [<span data-bind="text: $root.current.media().start"></span>;
                        <span data-bind="text: $root.current.media().stop"></span>] </span>
                    <!-- /ko -->
                    <!-- ko if: $root.current.media().start() == null && $root.current.media().stop() == null -->
                    <h3 data-bind="text: $root.current.media().name"></h3>
                    <!-- /ko -->
                </div>
                <div class="layer-body">
                    <!-- ko if: $root.current.media().type() === "audio" -->
                    <audio id="multimedia"
                           data-bind="event: {loadeddata: $root.actions.multimedia.loadeddata,
                           play: $root.actions.multimedia.play,
                           timeupdate: $root.actions.multimedia.play}" controls>
                        <source data-bind="attr: {src: $root.current.multimediaURL()} ">
                        Ваш браузер не поддерживает данный аудио-элемент.
                    </audio>
                    <!-- /ko -->
                    <!-- ko if: $root.current.media().type() === "video" -->
                    <video id="multimedia"
                           data-bind="event: {loadeddata: $root.actions.multimedia.loadeddata,
                           play: $root.actions.multimedia.play,
                           timeupdate: $root.actions.multimedia.play}" controls>
                        <source data-bind="attr: {src: $root.current.multimediaURL()} ">
                        Ваш браузер не поддерживает данный аудио-элемент.
                    </video>
                    <!-- /ko -->
                    <div class="details-row float-buttons">
                        <button class="cancel arcticmodal-close">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


<script type="text/html" id="overall-mode">
    <table class="werewolf materials">
        <!-- ko if:  $root.current.medias().length > 0 || $root.current.multimedias().length > 0 || $root.current.others().length > 0 -->
        <thead>
        <tr><th>№</th><th>Тип</th><th>Название</th></tr>
        </thead>
        <!-- /ko -->
        <tbody>

        <!-- ko if:  $root.current.medias().length > 0-->
        <tr><th colspan="3">Документы</th></tr>
        <!-- ko foreach: $root.current.medias-->
        <tr>
            <td data-bind="text: $index()+1"></td>
            <td><span data-bind="css: type" class="fa approve mini material-type"></span></td>
            <!-- ko if: start() != null && stop() != null -->
            <td data-bind="click: $root.actions.media.move"><span data-bind="text: pureName"></span>
                <span class="anchor-text"> [<span data-bind="text: start"></span>; <span data-bind="text: stop"></span>] </span></td>
            <!-- /ko -->
            <!-- ko if: start() != null && stop() == null -->
            <td data-bind="click: $root.actions.media.move"><span data-bind="text: pureName"></span>
                <span class="anchor-text"> [<span data-bind="text: start"></span>] </span></td>
            <!-- /ko -->
            <!-- ko if: start() == null && stop() == null -->
            <td data-bind="text: pureName, click: $root.actions.media.move"></td>
            <!-- /ko -->
        </tr>
        <!-- /ko -->
        <!-- /ko -->

        <!-- ko if:  $root.current.multimedias().length > 0-->
        <tr><th colspan="3">Мультимедия</th></tr>
        <!-- ko foreach: $root.current.multimedias-->
        <tr>
            <td data-bind="text: $index()+1"></td>
            <td><span data-bind="css: type" class="fa approve mini material-type"></span></td>
            <!-- ko if: start() != null && stop() != null -->
            <td data-bind="click: $root.actions.media.move"><span data-bind="text: pureName"></span>
                <span class="anchor-text"> [<span data-bind="text: start"></span>; <span data-bind="text: stop"></span>] </span></td>
            <!-- /ko -->
            <!-- ko if: start() != null && stop() == null -->
            <td data-bind="click: $root.actions.media.move"><span data-bind="text: pureName"></span>
                <span class="anchor-text"> [<span data-bind="text: start"></span>] </span></td>
            <!-- /ko -->
            <!-- ko if: start() == null && stop() == null -->
            <td data-bind="text: pureName, click: $root.actions.media.move"></td>
            <!-- /ko -->
        </tr>
        <!-- /ko -->
        <!-- /ko -->

        <!-- ko if:  $root.current.others().length > 0-->
        <tr><th colspan="3">Другое</th></tr>
        <!-- ko foreach: $root.current.others-->
        <tr>
            <td data-bind="text: $index()+1"></td>
            <td><span data-bind="css: type" class="fa approve mini material-type"></span></td>
            <!-- ko if: start() != null && stop() != null -->
            <td data-bind="click: $root.actions.media.move"><span data-bind="text: pureName"></span>
                <span class="anchor-text"> [<span data-bind="text: start"></span>; <span data-bind="text: stop"></span>] </span></td>
            <!-- /ko -->
            <!-- ko if: start() != null && stop() == null -->
            <td data-bind="click: $root.actions.media.move"><span data-bind="text: pureName"></span>
                <span class="anchor-text"> [<span data-bind="text: start"></span>] </span></td>
            <!-- /ko -->
            <!-- ko if: start() == null && stop() == null -->
            <td data-bind="text: pureName, click: $root.actions.media.move"></td>
            <!-- /ko -->
        </tr>
        <!-- /ko -->
        <!-- /ko -->



        <!-- ko if:  $root.current.medias().length == 0 && $root.current.multimedias().length == 0 && $root.current.others().length == 0-->
        <tr>
            <td class="empty" colspan="3"> Для данной дисциплины материалы отсутствуют</td>
        </tr>
        <!-- /ko -->
        </tbody>
    </table>
</script>

<script type="text/html" id="themes-mode">
    <div class="themes">
        <!-- ko foreach: $root.current.themes-->
        <div class="details-row" data-bind="click: $root.actions.theme.materials">
            <div class="details-column" data-bind="text: $index()+1"></div>
            <div class="details-column" data-bind="text: name"><a data-bind="text: name, click: $root.actions.theme.materials"></a></div>
        </div>
        <!-- ko if: $root.mode() === state.materials &&  $data.id() === $root.current.theme().id()-->
        <div class="details" data-bind="template: {name: 'materials-mode', data: $data}"></div>
        <!-- /ko -->
        <!-- /ko -->
    </div>
</script>

<script type="text/html" id="materials-mode">
    <table class="werewolf materials">
        <!-- ko if:  $root.current.medias().length > 0 || $root.current.multimedias().length > 0 || $root.current.others().length > 0 -->
        <thead>
        <tr><th>№</th><th>Тип</th><th>Название</th></tr>
        </thead>
        <!-- /ko -->
        <tbody>

        <!-- ko if:  $root.current.medias().length > 0-->
        <tr><th colspan="3">Документы</th></tr>
        <!-- ko foreach: $root.current.medias-->
        <tr>
            <td data-bind="text: $index()+1"></td>
            <td><span data-bind="css: type" class="fa approve mini material-type"></span></td>
            <!-- ko if: start() != null && stop() != null -->
            <td data-bind="click: $root.actions.media.move"><span data-bind="text: pureName"></span>
                <span class="anchor-text"> [<span data-bind="text: start"></span>; <span data-bind="text: stop"></span>] </span></td>
            <!-- /ko -->
            <!-- ko if: start() != null && stop() == null -->
            <td data-bind="click: $root.actions.media.move"><span data-bind="text: pureName"></span>
                <span class="anchor-text"> [<span data-bind="text: start"></span>] </span></td>
            <!-- /ko -->
            <!-- ko if: start() == null && stop() == null -->
            <td data-bind="text: pureName, click: $root.actions.media.move"></td>
            <!-- /ko -->
        </tr>
        <!-- /ko -->
        <!-- /ko -->

        <!-- ko if:  $root.current.multimedias().length > 0-->
        <tr><th colspan="3">Мультимедия</th></tr>
        <!-- ko foreach: $root.current.multimedias-->
        <tr>
            <td data-bind="text: $index()+1"></td>
            <td><span data-bind="css: type" class="fa approve mini material-type"></span></td>
            <!-- ko if: start() != null && stop() != null -->
            <td data-bind="click: $root.actions.media.move"><span data-bind="text: pureName"></span>
                <span class="anchor-text"> [<span data-bind="text: start"></span>; <span data-bind="text: stop"></span>] </span></td>
            <!-- /ko -->
            <!-- ko if: start() != null && stop() == null -->
            <td data-bind="click: $root.actions.media.move"><span data-bind="text: pureName"></span>
                <span class="anchor-text"> [<span data-bind="text: start"></span>] </span></td>
            <!-- /ko -->
            <!-- ko if: start() == null && stop() == null -->
            <td data-bind="text: pureName, click: $root.actions.media.move"></td>
            <!-- /ko -->
        </tr>
        <!-- /ko -->
        <!-- /ko -->

        <!-- ko if:  $root.current.others().length > 0-->
        <tr><th colspan="3">Другое</th></tr>
        <!-- ko foreach: $root.current.others-->
        <tr>
            <td data-bind="text: $index()+1"></td>
            <td><span data-bind="css: type" class="fa approve mini material-type"></span></td>
            <!-- ko if: start() != null && stop() != null -->
            <td data-bind="click: $root.actions.media.move"><span data-bind="text: pureName"></span>
                <span class="anchor-text"> [<span data-bind="text: start"></span>; <span data-bind="text: stop"></span>] </span></td>
            <!-- /ko -->
            <!-- ko if: start() != null && stop() == null -->
            <td data-bind="click: $root.actions.media.move"><span data-bind="text: pureName"></span>
                <span class="anchor-text"> [<span data-bind="text: start"></span>] </span></td>
            <!-- /ko -->
            <!-- ko if: start() == null && stop() == null -->
            <td data-bind="text: pureName, click: $root.actions.media.move"></td>
            <!-- /ko -->
        </tr>
        <!-- /ko -->
        <!-- /ko -->



        <!-- ko if:  $root.current.medias().length == 0 && $root.current.multimedias().length == 0 && $root.current.others().length == 0-->
        <tr>
            <td class="empty" colspan="3"> Для данной темы материалы отсутствуют</td>
        </tr>
        <!-- /ko -->
        </tbody>
    </table>
</script>
