@extends('shared.layout')
@section('title', 'Тема')
@section('javascript')
    <link rel="stylesheet" href="{{ URL::asset('css/knockout-file-bindings.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/knockout-file-bindings.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/tooltipster.bundle.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/tooltipster-sideTip-light.min.css')}}"/>
    <script src="{{ URL::asset('js/knockout-file-bindings.js')}}"></script>
    <script src="{{ URL::asset('js/knockout.validation.js')}}"></script>
    <script src="{{ URL::asset('js/tooltipster.bundle.js')}}"></script>
    <script src="{{ URL::asset('js/ace.js') }}"></script>
    <script src="{{ URL::asset('js/codeEditor/sendCode.js')}}"></script>
    <script src="{{ URL::asset('js/admin/themes.js')}}"></script>
@endsection

@section('content')
<div class="image-expander" data-bind="click: function(){$('.image-expander').hide();}">
    <!-- ko if: $root.current.question().showImage() -->
    <img data-bind="attr: {src: '/' + $root.current.question().showImage()}, click: $root.csed.image.expand"/>
    <!-- /ko -->
</div>
<div class="content">
    <div class="layer">
        <div class="details-row theme">
            <div class="details-column width-98p">
                <!-- ko ifnot: $root.mode() === 'theme.edit' -->
                <h2><a data-bind="text: $root.current.theme().name, click: $root.csed.theme.edit"></a></h2>
                <!-- /ko -->
                <!-- ko if: $root.mode() === 'theme.edit' -->
                <input type="text" tooltip-mark="theme-name_tooltip" data-bind="value: $root.current.theme().name, event: {focusin: $root.events.focusin, focusout: $root.events.focusout}">
                <span>
                    <button data-bind="click: $root.csed.theme.update" class="fa approve mini">&#xf00c;</button>
                    <button data-bind="click: $root.csed.theme.cancel" class="fa cancel mini">&#xf00d;</button>
                </span>
                <!-- /ko -->
            </div>

        </div>
        <div class="details-row theme-head">
            <div class="details-column width-98p">
                <label class="title">Дисциплина</label>
                <span data-bind="text: $root.current.discipline().name"></span>
            </div>

            <div class="details-column width-98p">
                <button class="action-button" data-bind="click: $root.csed.question.toggleAdd">Добавить вопрос</button>
            </div>
        </div>
    </div>

    <!-- ko if: $root.mode() === 'add' || $root.mode() === 'edit' -->
    <div class="layer theme">
        <div class="details-rows">
            <div class="details-column width-15p">
                <label class="title">Время на ответ <span class="required">*</span></label>
                <input class="time" tooltip-mark="minutes_tooltip" type="text" data-bind=", value: $root.current.question().minutes, valueUpdate: 'keyup', event: {focusin: $root.events.focusin, focusout: $root.events.focusout}" placeholder="00"/>
                <span>&nbsp;:&nbsp;</span>
                <input class="time" tooltip-mark="seconds_tooltip" type="text" data-bind="value: $root.current.question().seconds, valueUpdate: 'keyup', event: {focusin: $root.events.focusin, focusout: $root.events.focusout}" placeholder="00"/>
            </div>
            <div class="details-column width-39p">
                <label class="title">Тип вопроса <span class="required">*</span></label>
                <select data-bind="options: $root.filter.types,
                   optionsText: 'name',
                   value: $root.current.question().type,
                   optionsCaption: 'Выберите тип'"></select>
            </div>
            <div class="details-column width-39p">
                <label class="title">Сложность вопроса <span class="required">*</span></label>
                <select data-bind="options: $root.filter.complexityTypes,
                   optionsText: 'name',
                   value: $root.current.question().complexity,
                   optionsCaption: 'Выберите сложность'"></select>
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
                            <input type="file" data-bind="fileInput: $root.current.fileData, customFileInput: {
                        buttonClass: 'upload-btn', fileNameClass: 'disabled',
                        buttonText: 'Выберите файл', changeButtonText: 'Изменить',
                        clearButtonText: 'Очистить', clearButtonClass: 'clean-btn', noFileText: 'Файл не выбран'}" accept="image/*">
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <!-- /ko -->
                <!-- ko if: $root.current.question().showImage() -->
                <div class="image-holder">
                    <div class="fa remove" data-bind="click: $root.csed.image.remove">&#xf00d;</div>
                    <img data-bind="attr: {src: '/' + $root.current.question().showImage()}, click: $root.csed.image.expand"/>
                </div>
                <!-- /ko -->
            </div>
        </div>
        <div class="details-row">
            <div class="details-column width-98p">
                <label class="title">Текст вопроса <span class="required">*</span></label>
                <textarea tooltip-mark="question_tooltip" type="text" data-bind="value: $root.current.question().text, event: {focusin: $root.events.focusin, focusout: $root.events.focusout}"></textarea>
            </div>
        </div>
        <!-- ko if: $root.current.question().isCode() && $root.current.question().type() -->

        <div class="details-row">
            <div class="details-column width-45p">
                <label class="title">Входные параметры<span class="required"></span></label>
                <textarea data-bind="value: $root.code.params.input" placeholder="Входные параметры"></textarea>
            </div>
            <div class="details-column width-45p float-right">
                <label class="title">Выходные параметры<span class="required"></span></label>
                <textarea data-bind="value: $root.code.params.output" placeholder="Выходные параметры"></textarea>
            </div>
        </div>
        <div class="details-row float-buttons">
            <div class="details-column width-99p">
                <button data-bind="click: $root.code.params.add" class="approve"><span class="fa">&#xf067;</span>&nbsp;Добавить набор параметров</button>
            </div>
        </div>
        <!-- ko if: $root.code.params.set().length -->
        <div class="details-row">
            <div class="details-column width-98p">
                <table class="stripe-table paramset">
                    <tbody data-bind="foreach: $root.code.params.set">
                    <tr>
                        <td data-bind="text: $index()+1"></td>
                        <td data-bind="text: input"></td>
                        <td data-bind="text: expectedOutput"></td>
                        <td><button class="remove mini fa" data-bind="click: $root.code.params.remove">&#xf014;</button></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /ko -->
        <div class="details-row">
            <div class="details-column width-100p">
                <button data-bind="click: $root.code.open" class="action-button"><span class="fa">&#xf121;</span>&nbsp;Отладка программы</button>
            </div>
        </div>
        <!-- /ko -->
        <!-- ko if: !$root.current.question().isOpenMultiLine() && !$root.current.question().isCode() && $root.current.question().type() -->
        <div class="details-row variants">
            <div class="details-column width-98p">
                <label class="title">Варианты ответов<span class="required"></span></label>
                <input type="text" data-bind="value: $root.current.answer().text, valueUpdate: 'keyup', event: {keyup: $root.events.answers}"/>
                <button data-bind="click: $root.csed.answer.add" class="fa mini approve">&#xf067;</button>
            </div>
        </div>
        <!-- ko if: $root.current.answers().length -->
        <div class="details-row">
            <div class="details-column width-98p">
                <table class="stripe-table variants">
                    <tbody data-bind="foreach: $root.current.answers">
                    <tr>
                        <td data-bind="text: $index()+1"></td>
                        <td data-bind="text: text"></td>
                        <td data-bind="visible: !$root.current.question().isOpenSingleLine()">
                            <span level="1" class="radio" data-bind="css: { 'radio-positive': isRight() }, click: $root.alter.set.answerCorrectness">Правильный</span>
                            <span>|</span>
                            <span level="0" class="radio" data-bind="css: {'radio-negative':  !isRight() }, click: $root.alter.set.answerCorrectness" >Неправильный</span>
                        </td>
                        <td>
                            <button class="fa mini remove" data-bind="click: $root.csed.answer.remove">&#xf014;</button>
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
                <button class="cancel" data-bind="click: $root.csed.question.cancel">Отмена</button>
                <button class="approve approve-btn" data-bind="click: $root.csed.question.update">Сохранить вопрос</button>
            </div>
        </div>
    </div>
    <!-- /ko -->

    <div class="items">
        <table class="stripe-table questions">
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
                <tr>
                    <td data-bind="text: text" class="width-10p"></td>
                    <td data-bind="text: $root.alter.set.type($data)"></td>
                    <td data-bind="text: $root.alter.set.complexity($data)"></td>
                    <td>
                        <button data-bind="click: $root.csed.question.edit" class="fa approve mini">&#xf040;</button>
                        <button data-bind="click: $root.csed.question.startDelete" class="fa remove mini">&#xf014;</button>
                    </td>
                </tr>
            <!-- /ko -->
            </tbody>
        </table>
        @include('admin.shared.pagination')
    </div>

    <div class="filter theme">
        <div class="filter-block">
            <label class="title">Вопрос</label>
            <input type="text" data-bind="value: $root.filter.name, valueUpdate: 'keyup'">
        </div>
        <div class="filter-block">
            <label class="title">Тип вопроса</label>
            <select data-bind="options: $root.filter.types,
                       optionsText: 'name',
                       value: $root.filter.type,
                       optionsCaption: 'Выберите тип'"></select>
        </div>
        <div class="filter-block">
            <label class="title">Сложность вопроса</label>
            <select data-bind="options: $root.filter.complexityTypes,
                       optionsText: 'name',
                       value: $root.filter.complexity,
                       optionsCaption: 'Выберите сложность'"></select>
        </div>
    </div>

</div>
    @include('admin.shared.error-modal')
@endsection
<div class="tooltip_templates">
    <span id="minutes_tooltip">
        <span data-bind="validationMessage: $root.current.question().minutes"></span>
    </span>
    <span id="seconds_tooltip">
        <span data-bind="validationMessage: $root.current.question().seconds"></span>
    </span>
    <span id="theme-name_tooltip">
        <span data-bind="validationMessage: $root.current.theme().name"></span>
    </span>
    <span id="question_tooltip">
        <span data-bind="validationMessage: $root.current.question().text"></span>
    </span>
</div>

<div class="g-hidden">
    <div class="box-modal" id="delete-modal">
        <div class="popup-delete">
            <div><h3>Удалить выбранный вопрос?</h3></div>
            <div>
                <button data-bind="click: $root.csed.question.remove" class="fa">&#xf00c;</button>
                <button data-bind="click: $root.csed.question.cancel" class="fa danger arcticmodal-close">&#xf00d;</button>
            </div>
        </div>
    </div>
</div>
<div class="g-hidden">
    <div class="box-modal" id="code-editor-modal">
        <div>
            <div id="editor"></div>
            <input type="button" id="button" value="Скомпилировать" data-bind="click: $root.code.compile"/>
            <input type="button" class="cancel arcticmodal-close" value="Отмена">
            <input type="button" class="save" data-bind="click: $root.code.approve" value="Сохранить">
        </div>
    </div>
</div>
<div class="g-hidden">
    <div class="box-modal" id="compile-modal">
        <div class="">
            <div>
                <h3 data-bind="text: $root.code.result.text"></h3>
            </div>
            <div>
                <button class="arcticmodal-close width200">OK</button>
            </div>
        </div>
    </div>
</div>

<div class="g-hidden">
    <div class="box-modal" id="save-code-modal">
        <div class="">
            <div>
                <h3>Программный код будет показан студенту во время тестирования. Вы действительно хотите сохранить написанный код?</h3>
            </div>
            <div>
                <button class="arcticmodal-close width200" data-bind="click: $root.code.save">OK</button>
                <button class="arcticmodal-close width200" data-bind="click: $root.code.clear">Очистить</button>
            </div>
        </div>
    </div>
</div>

