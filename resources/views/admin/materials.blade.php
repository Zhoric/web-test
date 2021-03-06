@extends('layouts.manager')
@section('title', 'Материалы')
@section('javascript')
    <script src="{{ URL::asset('js/min/manager-materials.js')}}"></script>
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

    <div id="elfinder"></div>

    <div class="g-hidden">
        <div class="box-modal removal-modal" id="delete-modal">
            <div class="layer zero-margin width-auto">
                <div class="layer-head">
                    <h3>Данный материал больше ни к чему не прикреплен. Удалить его из файловой системы?</h3>
                </div>
                <div class="layer-body">
                    <div class="details-row float-buttons">
                        <button class="cancel arcticmodal-close">Отмена</button>
                        <button data-bind="click: $root.actions.media.removeFromSystem" class="remove arcticmodal-close">Да</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="g-hidden">
        <div class="box-modal" id="all-texts-anchors-modal">
            <div class="layer zero-margin width-auto">
                <div class="layer-head">
                    <h3>Выберите из списка ниже фрагменты, которые необходимо прикрепить.</h3>
                </div>
                <div class="layer-body">
                    <div>
                        <!-- ko if:  $root.current.textAnchors().length == 0-->
                        <h3>Фрагменты для документов отсутствуют.</h3>
                        <!-- /ko -->

                        <!-- ko if:  $root.current.textAnchors().length > 0-->
                        <!-- ko foreach: $root.current.textAnchors-->
                        <input data-bind="attr: {id: id}, checked: isChecked" class="custom-checkbox" type="checkbox">
                        <label data-bind="text: media.name, attr: {for: id}" class="anchor"></label>
                        <label data-bind="text: start, attr: {for: id}"></label> <br>
                        <!-- /ko -->
                        <!-- /ko -->
                    </div>
                    <div class="details-row float-buttons">
                        <button class="cancel arcticmodal-close">Отмена</button>
                        <!-- ko if:  $root.current.textAnchors().length > 0-->
                        <button data-bind="click: $root.actions.anchor.attachTextAnchorsAndClose" class="approve arcticmodal-close">OK</button>
                        <!-- /ko -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="g-hidden">
        <div class="box-modal removal-modal" id="text-anchors-modal">
            <div class="layer zero-margin width-auto">
                <!-- ko if:  $root.current.textAnchors().length > 0-->
                <div class="layer-head">
                    <h3>Выберите из списка ниже фрагменты, которые необходимо прикрепить.</h3>
                </div>
                <div class="layer-body">
                    <div>
                        <!-- ko foreach: $root.current.textAnchors-->
                        <input data-bind="attr: {id: id}, checked: isChecked" class="custom-checkbox" type="checkbox">
                        <label data-bind="text: start, attr: {for: id}"></label>
                        <!-- /ko -->
                    </div>
                    <div class="details-row float-buttons">
                        <button class="cancel arcticmodal-close">Отмена</button>
                        <button data-bind="click: $root.actions.anchor.attachTextAnchor" class="remove arcticmodal-close">OK</button>
                    </div>
                </div>
                <!-- /ko -->
                <!-- ko if:  $root.current.textAnchors().length == 0-->
                <div class="layer-head">
                    <h3>Для данного документа фрагменты отсутствуют.</h3>
                </div>
                <div class="layer-body">
                    <div class="details-row float-buttons">
                        <button class="cancel arcticmodal-close">OK</button>
                    </div>
                </div>
                <!-- /ko -->
            </div>
        </div>
    </div>

    <div class="g-hidden">
        <div class="box-modal anchor-modal" id="anchor-multimedia-modal">
            <div class="layer zero-margin width-auto">
                <div class="layer-head">
                    <h3>Выделите начало и конец:</h3>
                </div>
                <div class="layer-body">
                    <!-- ko if: $root.current.media().type() === "audio" -->
                    <audio id="multimediaAnchor"
                           data-bind="event: {canplay: $root.actions.multimedia.anchorUpdate,
                           play: $root.actions.multimedia.anchorUpdate,
                           timeupdate: $root.actions.multimedia.anchorUpdate}" controls>
                        <source data-bind="attr: {src: $root.current.multimediaURL()} ">
                        Ваш браузер не поддерживает данный аудио-элемент.
                    </audio>
                    <!-- /ko -->
                    <!-- ko if: $root.current.media().type() === "video" -->
                    <video id="multimediaAnchor"
                           data-bind="event: {canplay: $root.actions.multimedia.anchorUpdate,
                           play: $root.actions.multimedia.anchorUpdate,
                           timeupdate: $root.actions.multimedia.anchorUpdate}" controls>
                        <source data-bind="attr: {src: $root.current.multimediaURL()} ">
                        Ваш браузер не поддерживает данный видео-элемент.
                    </video>
                    <!-- /ko -->
                    <div class="anchor-details" data-bind="with: $root.current.anchor">
                        <div class="inline-block">
                            <input id="start" type="radio" value="start" name="anchor" data-bind="checked: request" class="custom-radio"/>
                            <label for="start"><span class="required">*</span> Начало</label>
                            <input id="hourStart" class="anchor" type="text" maxlength="2" validate
                                   data-bind="value: hourStart,
                                              validationElement: hourStart,
                                              event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
                            <label>:</label>
                            <input id="minuteStart" class="anchor" type="text" maxlength="2" validate
                                   data-bind="value: minuteStart,
                                              validationElement: minuteStart,
                                              event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
                            <label>:</label>
                            <input id="secondStart" class="anchor" type="text" maxlength="2" validate
                                   data-bind="value: secondStart,
                                              validationElement: secondStart,
                                              event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
                        </div>
                        <div class="inline-block">
                            <input id="stop" type="radio" value="stop" name="anchor" data-bind="checked: request" class="custom-radio"/>
                            <label for="stop"><span class="required">*</span> Конец</label>
                            <input id="hourStop" class="anchor" type="text" maxlength="2" validate
                                   data-bind="value: hourStop,
                                              validationElement: hourStop,
                                              event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
                            <label>:</label>
                            <input id="minute.stop" class="anchor" type="text" maxlength="2" validate
                                   data-bind="value: minuteStop,
                                              validationElement: minuteStop,
                                              event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
                            <label>:</label>
                            <input id="second.stop" class="anchor" type="text" maxlength="2" validate
                                   data-bind="value: secondStop,
                                              validationElement: secondStop,
                                              event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
                        </div>
                    </div>
                    <div class="details-row float-buttons">
                        <button class="cancel arcticmodal-close">Отмена</button>
                        <button id="bAddAnchor" accept-validation
                                data-bind="click: $root.actions.anchor.create.multimedia"
                                title="Проверьте правильность заполнения полей"
                                class="approve">ОК</button>
                    </div>
                </div>
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
        <thead>
        <tr><th>№</th><th>Тип</th><th>Материалы</th><th>Действия</th></tr>
        </thead>
        <tbody>
        <tr class="adder-row">
            <td colspan="4">
                <div data-bind="click: $root.actions.media.open.add"> <span class="fa">&#xf067;</span>&nbsp;Добавить материал </div>
                <div data-bind="click: $root.actions.media.open.anchor"> <span class="fa">&#xf125;</span>&nbsp;Выделить отрывок </div>
                <div data-bind="click: $root.actions.anchor.open.text"> <span class="fa">&#xf067;</span>&nbsp;Прикрепить фрагмент текста </div>
            </td>
        </tr>
        <!-- ko if:  $root.current.medias().length > 0-->
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
            <td class="action-holder">
                <button data-bind="click: $root.actions.anchor.show, css: type" class="editor fa approve mini actions">&#xf13d;</button>
                <button data-bind="click: $root.actions.media.open.editor, css: type" class="editor fa approve mini actions">&#xf040;</button>
                <button data-bind="click: $root.actions.media.open.replacement" class="fa approve mini actions">&#xf0ec;</button>
                <button data-bind="click: $root.actions.media.remove" class="fa remove mini actions">&#xf014;</button>
            </td>
        </tr>
        <!-- /ko -->
        <!-- /ko -->
        <!-- ko if:  $root.current.medias().length == 0-->
        <tr>
            <td class="empty" colspan="4"> Для данной дисциплины материалы отсутствуют</td>
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
        <thead>
        <tr><th>№</th><th>Тип</th><th>Материалы</th><th>Действия</th></tr>
        </thead>
        <tbody>
        <tr class="adder-row">
            <td colspan="4">
                <div data-bind="click: $root.actions.media.open.add"> <span class="fa">&#xf067;</span>&nbsp;Добавить материал </div>
                <div data-bind="click: $root.actions.media.open.anchor"> <span class="fa">&#xf125;</span>&nbsp;Выделить отрывок </div>
                <div data-bind="click: $root.actions.anchor.open.text"> <span class="fa">&#xf067;</span>&nbsp;Прикрепить фрагмент текста </div>
            </td>
        </tr>
        <!-- ko if:  $root.current.medias().length > 0-->
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
            <td class="action-holder">
                <button data-bind="click: $root.actions.anchor.show, css: type" class="editor fa approve mini actions">&#xf13d;</button>
                <button data-bind="click: $root.actions.media.open.editor, css: type" class="editor fa approve mini actions">&#xf040;</button>
                <button data-bind="click: $root.actions.media.open.replacement" class="fa approve mini actions">&#xf0ec;</button>
                <button data-bind="click: $root.actions.media.remove" class="fa remove mini actions">&#xf014;</button>
            </td>
        </tr>
        <!-- /ko -->
        <!-- /ko -->
        <!-- ko if:  $root.current.medias().length == 0-->
        <tr>
            <td class="empty" colspan="4"> Для данной темы материалы отсутствуют</td>
        </tr>
        <!-- /ko -->
        </tbody>
    </table>
</script>