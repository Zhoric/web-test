@extends('shared.layout')
@section('title', 'Дисциплины')
@section('javascript')
    <link rel="stylesheet" href="{{ URL::asset('css/knockout-file-bindings.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/knockout-file-bindings.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/tooltipster.bundle.css')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/tooltipster-sideTip-light.min.css')}}"/>
    <script src="{{ URL::asset('js/knockout-file-bindings.js')}}"></script>
    <script src="{{ URL::asset('js/knockout.validation.js')}}"></script>
    <script src="{{ URL::asset('js/tooltipster.bundle.js')}}"></script>
    <script src="{{ URL::asset('js/admin/themes.js')}}"></script>
@endsection

@section('content')
    <div class="expanded-image" data-bind="click: function(){$('.expanded-image').hide();}">
        <!-- ko if: $root.current.question().showImage() -->
        <img data-bind="attr: {src: '/' + $root.current.question().showImage()}, click: $root.csed.image.expand"/>
        <!-- /ko -->
    </div>
<div class="content themes">
    <div class="org-info">
        <div class="main-info width100">
            <div>
                <label>Дисциплина</label></br>
                <span data-bind="text: $root.current.discipline().name"></span>
            </div></br>
            <div>
                <label>Тема</label></br>
                <!-- ko ifnot: $root.mode() === 'theme.edit' -->
                <span><a data-bind="text: $root.current.theme().name, click: $root.csed.theme.edit"></a></span>
                <!-- /ko -->
                <!-- ko if: $root.mode() === 'theme.edit' -->
                <input type="text" tooltip-mark="theme-name_tooltip" data-bind="value: $root.current.theme().name, event: {focusin: $root.events.focusin, focusout: $root.events.focusout}">
                <span>
                    <button data-bind="click: $root.csed.theme.update" class="fa sq-small">&#xf00c;</button>
                    <button data-bind="click: $root.csed.theme.cancel" class="fa danger sq-small">&#xf00d;</button>
                </span>
                <!-- /ko -->
            </div></br>
            <div>
                <button class="width200" data-bind="click: $root.csed.question.toggleAdd">Добавить вопрос</button>
            </div>
        </div>
    </div>
    <span ></span>
    <!-- ko if: $root.mode() === 'add' || $root.mode() === 'edit' -->
    <div class="themes-add org-info">
        <div class="time">
            <label>Время на ответ <span>*</span></label></br>
            <input tooltip-mark="minutes_tooltip" type="text" data-bind=", value: $root.current.question().minutes, valueUpdate: 'keyup', event: {focusin: $root.events.focusin, focusout: $root.events.focusout}" placeholder="00"/>
            <span>&nbsp;:&nbsp;</span>
            <input tooltip-mark="seconds_tooltip" type="text" data-bind="value: $root.current.question().seconds, valueUpdate: 'keyup', event: {focusin: $root.events.focusin, focusout: $root.events.focusout}" placeholder="00"/>
        </div>
        <div class="upload-image">
            <label>Изображение</label></br>
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
                            clearButtonText: 'Очистить', noFileText: 'Файл не выбран'}" accept="image/*">
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
        <div class="select-theme">
            <label>Тип вопроса <span>*</span></label></br>
            <select data-bind="options: $root.filter.types,
                       optionsText: 'name',
                       value: $root.current.question().type,
                       optionsCaption: 'Выберите тип'"></select>
        </div>
        <div class="select-complexity">
            <label>Сложность вопроса <span>*</span></label></br>
            <select data-bind="options: $root.filter.complexityTypes,
                       optionsText: 'name',
                       value: $root.current.question().complexity,
                       optionsCaption: 'Выберите сложность'"></select>
        </div>
        <div class="question-text">
            <label>Текст вопроса <span>*</span></label></br>
            <textarea tooltip-mark="question_tooltip" type="text" data-bind="value: $root.current.question().text, event: {focusin: $root.events.focusin, focusout: $root.events.focusout}"></textarea>
        </div>
        <!-- ko if: !$root.current.question().isOpenMultiLine() && $root.current.question().type() -->
        <div class="answers-input">
            <label>Варианты ответов <span>*</span></label></br>
            <input type="text" data-bind="value: $root.current.answer().text, valueUpdate: 'keyup'"/>
            <button data-bind="click: $root.csed.answer.add" class="fa">&#xf067;</button>
        </div>
        <!-- ko if: $root.current.answers().length -->
        <div class="answers-table">
            <table>
                <tbody data-bind="foreach: $root.current.answers">
                    <tr>
                        <td data-bind="text: $index()+1"></td>
                        <td data-bind="text: text"></td>
                        <td data-bind="visible: !$root.current.question().isOpenSingleLine()">
                            <span level="1" class="radio" data-bind="css: { 'radio-positive': isRight() }, click: $root.toggleCurrent.set.answerCorrectness">Правильный</span>
                            <span>|</span>
                            <span level="0" class="radio" data-bind="css: {'radio-negative':  !isRight() }, click: $root.toggleCurrent.set.answerCorrectness" >Неправильный</span>
                        </td>
                        <td>
                            <button class="fa sq-small danger" data-bind="click: $root.csed.answer.remove">&#xf014;</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- /ko -->
        <!-- /ko -->
        <div class="btn-larger-group">
            <button class="danger" data-bind="click: $root.csed.question.cancel">Отмена</button>
            <button class="approve-btn" data-bind="click: $root.csed.question.update">Сохранить вопрос</button>
        </div>
    </div>
    <!-- /ko -->

    <div class="filter">
        <div>
            <label>Вопрос</label></br>
            <input type="text" data-bind="value: $root.filter.name, valueUpdate: 'keyup'">
        </div>
        <div>
            <label>Тип вопроса</label></br>
            <select data-bind="options: $root.filter.types,
                       optionsText: 'name',
                       value: $root.filter.type,
                       optionsCaption: 'Выберите тип'"></select>
        </div>
        <div>
            <label>Сложность вопроса</label></br>
            <select data-bind="options: $root.filter.complexityTypes,
                       optionsText: 'name',
                       value: $root.filter.complexity,
                       optionsCaption: 'Выберите сложность'"></select>
        </div>
    </div>

    <div class="width100">
        <table class="theme themes">
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
                    <td data-bind="text: text"></td>
                    <td data-bind="text: $root.toggleCurrent.set.type($data)"></td>
                    <td data-bind="text: $root.toggleCurrent.set.complexity($data)"></td>
                    <td>
                        <button data-bind="click: $root.csed.question.edit" class="fa">&#xf040;</button>
                        <button data-bind="click: $root.csed.question.startDelete" class="fa danger">&#xf014;</button>
                    </td>
                </tr>
            <!-- /ko -->
            </tbody>
        </table>
    </div>

    <div class="pager-wrap">
        <!-- ko if: ($root.pagination.totalPages()) > 0 -->
        <div class="pager">
            <!-- ko ifnot: $root.pagination.currentPage() == 1 -->
            <button class="" data-bind="click: $root.pagination.selectPage.bind($data, 1)">&lsaquo;&lsaquo;</button>
            <button class="" data-bind="click: $root.pagination.selectPage.bind($data, ($root.pagination.currentPage() - 1))">&lsaquo;</button>
            <!-- /ko -->
            <!-- ko foreach: new Array($root.pagination.totalPages()) -->
            <span data-bind="visible: $root.pagination.dotsVisible($index() + 1)">...</span>
            <button class="" data-bind="click: $root.pagination.selectPage.bind($data, ($index()+1)), text: ($index()+1), visible: $root.pagination.pageNumberVisible($index() + 1), css: {current: ($index() + 1) == $root.pagination.currentPage()}"></button>
            <!-- /ko -->
            <!-- ko ifnot: $root.pagination.currentPage() == $root.pagination.totalPages() -->
            <button class="" data-bind="click: $root.pagination.selectPage.bind($data, ($root.pagination.currentPage() + 1))">&rsaquo;</button>
            <button class="" data-bind="click: $root.pagination.selectPage.bind($data, $root.pagination.totalPages())">&rsaquo;&rsaquo;</button>
            <!-- /ko -->
        </div>
        <!-- /ko -->
    </div>
</div>


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
        <div>
            <div><span>Удалить выбранный вопрос?</span></div>
            <div>
                <button data-bind="click: $root.csed.question.remove" class="fa">&#xf00c;</button>
                <button data-bind="click: $root.csed.question.cancel" class="fa danger arcticmodal-close">&#xf00d;</button>
            </div>
        </div>
    </div>
</div>
@endsection