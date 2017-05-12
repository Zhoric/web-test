@extends('layouts.manager')
@section('title', 'Тема')
@section('javascript')
    <link rel="stylesheet" href="{{ URL::asset('css/knockout-file-bindings.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/knockout-file-bindings.css')}}"/>

    <script src="{{ URL::asset('js/min/manager-themes.js')}}"></script>
@endsection

@section('content')
<div class="image-expander" data-bind="click: $root.actions.image.hide">
    <!-- ko if: $root.current.question().showImage() -->
    <img class="zoom" data-bind="attr: {src: '/' + $root.current.question().showImage()}"/>
    <!-- /ko -->
</div>
<div class="content">
    <div class="layer">
        <div class="details-row">
            <div class="details-column width-98p">
                <!-- ko ifnot: $root.current.theme.mode() === state.update -->
                <h2><a data-bind="text: $root.current.theme.name, click: $root.actions.theme.start.update"></a></h2>
                <!-- /ko -->
                <!-- ko if: $root.current.theme.mode() === state.update -->
                <table>
                    <tr>
                        <td class="width-100p">
                            <input type="text" id="iThemeName" validate class="height-40"
                                   data-bind="value: $root.current.theme.name,
                                   validationElement: $root.current.theme.name,
                                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
                        </td>
                        <td class="minw-100">
                            <button data-bind="click: $root.actions.theme.end.update" class="fa approve mini">&#xf00c;</button>
                            <button data-bind="click: $root.actions.theme.cancel" class="fa cancel mini">&#xf00d;</button>
                        </td>
                    </tr>
                </table>
                <!-- /ko -->
            </div>

        </div>
        <div class="details-row theme-head float-buttons">
            <div class="details-column">
                <label class="title">Дисциплина</label>
                <span data-bind="text: $root.current.discipline().name"></span>
            </div>
            <button class="action-button minw-100" data-bind="click: $root.actions.exportFile">
                <span class="fa">&#xf019;</span>&nbsp;Экспорт
            </button>
            <button class="action-button minw-100" data-bind="click: $root.actions.importFile.start">
                <span class="fa">&#xf093;</span>&nbsp;Импорт
            </button>
        </div>
        <div class="details-row theme-head">
            <div class="details-column width-98p">
                <button class="action-button width-100p" data-bind="click: $root.actions.question.start.add">Добавить вопрос</button>
            </div>
        </div>
    </div>

    <!-- ko if: $root.mode() === state.create || $root.mode() === state.update -->
    <div class="layer theme minw-600" id="question-form">
        <div class="details-rows">
            <div class="details-column width-15p minw-120">
                <label class="title">Время&nbsp;на&nbsp;ответ&nbsp;<span class="required">*</span></label>
                <input class="time" type="text" id="iQMinutes"
                       data-bind="value: $root.current.question().minutes,
                       valueUpdate: 'keyup',
                       validationElement: $root.current.question().minutes,
                       event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"
                       placeholder="мин." validate/>
                <span>&nbsp;:&nbsp;</span>
                <input class="time" type="text" id="iQSeconds"
                       data-bind="value: $root.current.question().seconds,
                       valueUpdate: 'keyup',
                       validationElement: $root.current.question().seconds,
                       event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"
                       placeholder="сек." validate/>
            </div>
            <div class="details-column width-39p">
                <label class="title">Тип&nbsp;вопроса&nbsp;<span class="required">*</span></label>
                <select id="sQType" validate
                        data-bind="options: $root.initial.types,
                        optionsText: 'name',
                        value: $root.current.question().type,
                        optionsCaption: 'Выберите тип',
                        validationElement: $root.current.question().type,
                        event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
                </select>
            </div>
            <div class="details-column width-39p">
                <label class="title">Сложность&nbsp;вопроса&nbsp;<span class="required">*</span></label>
                <select id="sQComplexity" validate
                        data-bind="options: $root.initial.complexity,
                        optionsText: 'name',
                        value: $root.current.question().complexity,
                        optionsCaption: 'Выберите сложность',
                        validationElement: $root.current.question().complexity,
                        event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
                </select>
            </div>
        </div>
        <div class="details-row">
            <div class="details-column width-98p">
                <label class="title">Изображение</label>
                <!-- ko if: !$root.current.question().showImage() -->
                <div class="image-uploader" data-bind="fileDrag: $root.current.fileData">
                    <div class="row">
                        <div class="img-preview">
                            <img class="img-rounded  thumb" data-bind="attr: { src: $root.current.fileData().dataURL }, visible: $root.current.fileData().dataURL">
                            <div data-bind="ifnot: $root.current.fileData().dataURL">
                                <label class="drag-label">Перетащите файл изображения</label>
                            </div>
                        </div>
                        <div class="file-input">
                            <input type="file" style="margin-right: 60px"
                                   data-bind="fileInput: $root.current.fileData,
                                   customFileInput: {
                                   buttonClass: 'upload-btn', fileNameClass: 'disabled',
                                   buttonText: 'Выберите файл', changeButtonText: 'Изменить',
                                   clearButtonText: 'Очистить', clearButtonClass: 'clean-btn',
                                   noFileText: 'Файл не выбран'}" accept="image/*">
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <!-- /ko -->
                <!-- ko if: $root.current.question().showImage() -->
                <div class="image-holder">
                    <div class="fa remove" data-bind="click: $root.actions.image.remove">&#xf00d;</div>
                    <img data-bind="attr: {src: '/' + $root.current.question().showImage()}, click: $root.actions.image.expand"/>
                </div>
                <!-- /ko -->
            </div>
        </div>
        <div class="details-row">
            <div class="details-column width-98p">
                <label class="title">Текст&nbsp;вопроса&nbsp;<span class="required">*</span></label>
                <textarea id="taQText" validate class="height-100 maxw-100p"
                        data-bind="value: $root.current.question().text,
                        validationElement: $root.current.question().complexity,
                        event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
                </textarea>
            </div>
        </div>
        <!-- ko if: $root.current.question().isCode() && $root.current.question().type() -->
        <div class="details" data-bind="attr: {'rdrd': $root.events.afterRender()}">
            <div class="details-row">
                <div class="details-column width-98p">
                    <button data-bind="click: $root.code.open" class="action-button width-100p">
                        <span class="fa">&#xf121;</span>&nbsp;Отладка программы
                    </button>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column minw-120 width-10p">
                    <label class="title">Лимит&nbsp;времени&nbsp;<span class="required">*</span></label>
                    <input class="text-center" type="text" id="iQTimeLimit"
                           data-bind="value: $root.code.timeLimit,
                           valueUpdate: 'keyup',
                           validationElement: $root.code.timeLimit,
                           event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"
                           placeholder="сек." validate/>
                </div>
                <div class="details-column minw-120 width-10p">
                    <label class="title">Лимит&nbsp;памяти&nbsp;<span class="required">*</span></label>
                    <input class="text-center" type="text" id="iQMemoryLimit"
                           data-bind="value: $root.code.memoryLimit,
                           valueUpdate: 'keyup',
                           validationElement: $root.code.memoryLimit,
                           event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"
                           placeholder="кб." validate/>
                </div>
                <div class="details-column width-72p">
                    <label class="title">Язык&nbsp;программирования&nbsp;<span class="required">*</span></label>
                    <select id="sQLang" validate
                            data-bind="options: $root.initial.langs,
                            value: $root.code.lang,
                            optionsCaption: 'Выберите язык программирования',
                            validationElement: $root.code.lang,
                            event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
                    </select>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-48p float-left">
                    <label class="title">Входные&nbsp;параметры&nbsp;<span class="required">*</span></label>
                    <textarea data-bind="value: $root.code.params.input" placeholder="Входные параметры"></textarea>
                </div>
                <div class="details-column width-48p float-right">
                    <label class="title">Выходной&nbsp;параметр&nbsp;<span class="required">*</span></label>
                    <textarea data-bind="value: $root.code.params.output" placeholder="Выходной параметр"></textarea>
                </div>
            </div>
            <div class="details-row float-buttons">
                <div class="details-column width-99p">
                    <button id="bQParams" validate special
                            title="Пожалуйста, укажите хотя бы один набор параметров"
                            data-bind="click: $root.code.params.add" class="approve">
                        <span class="fa">&#xf067;</span>&nbsp;Добавить набор параметров
                    </button>
                </div>
            </div>
            <!-- ko if: $root.code.params.set().length -->
            <div class="details-row">
                <div class="details-column width-98p">
                    <table class="werewolf">
                        <tbody data-bind="foreach: $root.code.params.set">
                        <tr>
                            <td data-bind="text: $index()+1" class="minw-20 text-center"></td>
                            <td data-bind="text: input" class="width-50p"></td>
                            <td data-bind="text: expectedOutput" class="width-50p"></td>
                            <td class="action-holder"><button class="remove mini fa" data-bind="click: $root.code.params.remove">&#xf014;</button></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /ko -->

        </div>
        <!-- /ko -->
        <!-- ko if: !$root.current.question().isOpenMultiLine() && !$root.current.question().isCode() && $root.current.question().type() -->
        <div class="details-row variants">
            <div class="details-column width-98p">
                <label class="title">Варианты&nbsp;ответов&nbsp;<span class="required">*</span></label>
                <input type="text" id="iQAnswers" validate special
                       data-bind="value: $root.current.answer().text,
                       valueUpdate: 'keyup',
                       event: {keyup: $root.events.answers}"/>
                <button data-bind="click: $root.actions.answer.add" class="fa mini approve">&#xf067;</button>
            </div>
        </div>
        <!-- ko if: $root.current.answers().length -->
        <div class="details-row">
            <div class="details-column width-98p">
                <table class="werewolf">
                    <tbody data-bind="foreach: $root.current.answers">
                    <tr>
                        <td data-bind="text: $index()+1" class="minw-20 text-center"></td>
                        <td data-bind="text: text" class="width-100p"></td>
                        <td data-bind="visible: !$root.current.question().isOpenSingleLine()" class="minw-220">
                            <span level="1" class="radio" data-bind="css: { 'radio-positive': isRight() }, click: $root.alter.set.answerCorrectness">Правильный</span>
                            <span>|</span>
                            <span level="0" class="radio" data-bind="css: {'radio-negative':  !isRight() }, click: $root.alter.set.answerCorrectness" >Неправильный</span>
                        </td>
                        <td class="action-holder">
                            <button class="fa mini remove" data-bind="click: $root.actions.answer.remove">&#xf014;</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /ko -->
        <!-- /ko -->
        <div class="details-row float-buttons">
            <div class="details-column width-99p">
                <button class="cancel" data-bind="click: $root.actions.question.cancel">Отмена</button>
                <button id="bUpdateQuestion" accept-validation class="approve"
                        title="Проверьте правильность заполнения полей"
                        data-bind="click: $root.actions.question.end.update">Сохранить вопрос</button>
            </div>
        </div>
    </div>
    <!-- /ko -->

    <div class="items">
        <table class="werewolf questions">
            <thead>
            <tr>
                <th>Вопрос</th>
                <th>Тип</th>
                <th>Сложность</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            <!-- ko foreach: $root.current.questions-->
            <tr data-bind="attr: {id: 'qwn-' + id()}">
                <td data-bind="text: text" class="width-100p"></td>
                <td data-bind="text: $root.alter.set.type($data)"></td>
                <td data-bind="text: $root.alter.set.complexity($data)" class="text-center"></td>
                <td class="minw-100 action-holder">
                    <button data-bind="click: $root.actions.question.start.update" class="fa approve mini actions">&#xf040;</button>
                    <button data-bind="click: $root.actions.question.start.remove" class="fa remove mini actions">&#xf014;</button>
                </td>
            </tr>
            <!-- /ko -->
            </tbody>
        </table>
        @include('shared.pagination')
    </div>

    <div class="filter theme">
        <div class="filter-block">
            <label class="title">Вопрос</label>
            <input type="text" data-bind="value: $root.filter.name, valueUpdate: 'keyup'" placeholder="Текст вопроса">
        </div>
        <div class="filter-block">
            <label class="title">Тип вопроса</label>
            <select data-bind="options: $root.initial.types,
                       optionsText: 'name',
                       value: $root.filter.type,
                       optionsCaption: 'Выберите тип'"></select>
        </div>
        <div class="filter-block">
            <label class="title">Сложность вопроса</label>
            <select data-bind="options: $root.initial.complexity,
                       optionsText: 'name',
                       value: $root.filter.complexity,
                       optionsCaption: 'Выберите сложность'"></select>
        </div>
        <div class="filter-block">
            <span class="clear" data-bind="click: $root.filter.clear">Очистить</span>
        </div>
    </div>
</div>
@endsection

<div class="g-hidden">
    <div class="box-modal removal-modal" id="remove-question-modal">
        <div class="layer zero-margin width-auto">
            <div class="layer-head">
                <h3>Удалить выбранный вопрос?</h3>
            </div>
            <div class="layer-body">
                <div class="details-row float-buttons">
                    <button class="cancel arcticmodal-close" data-bind="click: $root.actions.question.cancel">Отмена</button>
                    <button class="remove arcticmodal-close" data-bind="click: $root.actions.question.end.remove">Удалить</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="g-hidden">
    <div class="box-modal" id="code-editor-modal">
        <div class="layer zero-margin width-auto">
            <div class="layer-head">
                <h3>Программный код</h3>
            </div>
            <div class="layer-body">
                <div id="editor"></div>
            </div>
            <div class="details-row">
                <button class="approve float-left" data-bind="click: $root.code.compile">Скомпилировать</button>
                <button class="cancel arcticmodal-close float-right">Отмена</button>
                <button class="approve float-right mr-5" data-bind="click: $root.code.approve">Сохранить</button>
            </div>
        </div>
    </div>
</div>
<div class="g-hidden">
    <div class="box-modal" id="compile-modal">
        <div class="layer zero-margin width-auto">
            <div class="layer-head">
                <h3 data-bind="text: $root.code.result.text"></h3>
            </div>
            <div class="layer-body zero-margin">
                <div class="details-row float-buttons">
                    <button class="arcticmodal-close approve">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="g-hidden">
    <div class="box-modal" id="save-code-modal">
        <div class="layer zero-margin width-auto">
            <div class="layer-head">
                <h3>Программный код будет показан студенту во время тестирования. Вы действительно хотите сохранить написанный код?</h3>
            </div>
            <div class="layer-body zero-margin">
                <div class="details-row float-buttons">
                    <button class="arcticmodal-close remove" data-bind="click: $root.code.clear">Очистить</button>
                    <button class="arcticmodal-close approve" data-bind="click: $root.code.save">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="g-hidden">
    <div class="box-modal" id="import-modal">
        <div class="layer zero-margin width-auto">
            <div class="layer-head">
                <h3>Импорт вопросов</h3>
            </div>
            <div class="layer-body zero-margin">
                <div class="details-row">
                    <div class="details-column width-98p">
                        <div class="image-uploader" data-bind="fileDrag: $root.current.importFile">
                            <div class="row">
                                <div class="file-input">
                                    <input type="file" style="margin-right: 60px"
                                           data-bind="fileInput: $root.current.importFile,
                                               customFileInput: {
                                               buttonClass: 'upload-btn', fileNameClass: 'disabled',
                                               buttonText: 'Выберите файл', changeButtonText: 'Изменить',
                                               clearButtonText: 'Очистить', clearButtonClass: 'clean-btn',
                                               noFileText: 'Файл не выбран'}">
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
                <div class="details-row float-buttons minh-40">
                    <button class="arcticmodal-close cancel" data-bind="click: $root.actions.importFile.cancel">Отмена</button>
                    <button class="arcticmodal-close approve" data-bind="click: $root.actions.importFile.end">Загрузить</button>
                </div>
            </div>
        </div>
    </div>
</div>


