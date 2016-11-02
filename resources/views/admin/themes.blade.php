@extends('shared.layout')
@section('title', 'Дисциплины')
@section('javascript')
    <link rel="stylesheet" href="{{ URL::asset('css/knockout-file-bindings.css')}}"/>
    <script src="{{ URL::asset('js/knockout-file-bindings.js')}}"></script>
    <script src="{{ URL::asset('js/admin/themes.js')}}"></script>
@endsection

@section('content')
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
                <input type="text" data-bind="value: $root.current.theme().name">
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
    {{--<div>--}}
        {{--<div class="image-uploader" data-bind="fileDrag: $root.current.fileData">--}}
            {{--<div class="row">--}}
                {{--<div class="img-preview">--}}
                    {{--<img class="img-rounded  thumb" data-bind="attr: { src: $root.current.fileData().dataURL }, visible: $root.current.fileData().dataURL">--}}
                    {{--<div data-bind="ifnot: $root.current.fileData().dataURL">--}}
                        {{--<label class="drag-label">Drag file here</label>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="file-input">--}}
                    {{--<input type="file" data-bind="fileInput: $root.current.fileData, customFileInput: {--}}
              {{--buttonClass: 'upload-btn',--}}
              {{--fileNameClass: 'disabled'}" accept="image/*">--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="clear"></div>--}}
        {{--</div>--}}
    {{--</div>--}}

    <!-- ko if: $root.mode() === 'add' || $root.mode() === 'edit' -->
    <div class="themes-add org-info">
        <div class="time">
            <label>Время на ответ</label></br>
            <input type="text" data-bind="value: $root.current.question().minutes, valueUpdate: 'keyup' " placeholder="00">
            <span>:</span>
            <input type="text" data-bind="value: $root.current.question().seconds, valueUpdate: 'keyup' " placeholder="00">
        </div>
        <div class="upload-image">
            <label>Изображение</label></br>
            <div class="image-uploader" data-bind="fileDrag: $root.current.fileData">
                <div class="row">
                    <div class="img-preview">
                        <img class="img-rounded  thumb" data-bind="attr: { src: $root.current.fileData().dataURL }, visible: $root.current.fileData().dataURL">
                        <div data-bind="ifnot: $root.current.fileData().dataURL">
                            <label class="drag-label">Drag file here</label>
                        </div>
                    </div>
                    <div class="file-input">
                        <input type="file" data-bind="fileInput: $root.current.fileData, customFileInput: {
              buttonClass: 'upload-btn',
              fileNameClass: 'disabled'}" accept="image/*">
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="select-theme">
            <label>Тип вопроса</label></br>
            <select data-bind="options: $root.filter.types,
                       optionsText: 'name',
                       value: $root.current.question().type,
                       optionsCaption: 'Выберите тип'"></select>
        </div>
        <div class="select-complexity">
            <label>Сложность вопроса</label></br>
            <select data-bind="options: $root.filter.complexityTypes,
                       optionsText: 'name',
                       value: $root.current.question().complexity,
                       optionsCaption: 'Выберите сложность'"></select>
        </div>
        <div class="question-text">
            <label>Текст вопроса</label></br>
            <textarea type="text" data-bind="value: $root.current.question().text"></textarea>
        </div>
        <!-- ko if: !$root.current.question().isOpenMultiLine() && $root.current.question().type() -->
        <div class="answers-input">
            <label>Варианты ответов</label></br>
            <input data-bind="value: $root.current.answer().text" type="text">
            <button data-bind="click: $root.csed.answer.add" class="fa">&#xf067;</button>
        </div>
        <!-- ko if: $root.current.answers().length -->
        <div class="answers-table">
            <table>
                <tbody data-bind="foreach: $root.current.answers">
                    <tr>
                        <td data-bind="text: id"></td>
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
            <button data-bind="click: $root.csed.question.update">Сохранить вопрос</button>
        </div>
    </div>
    <!-- /ko -->

    <div class="filter">
        <div>
            <label>Название вопроса</label></br>
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
<div class="g-hidden">
    <div class="box-modal" id="validation-modal">
        <div class="box-modal_close arcticmodal-close">закрыть</div>
        <div>
            <span data-bind="text: $root.current.question().validationMessage"></span>
        </div>
    </div>
</div>
@endsection