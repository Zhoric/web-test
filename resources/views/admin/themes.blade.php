@extends('shared.layout')
@section('title', 'Дисциплины')
@section('javascript')
    <script src="{{ URL::asset('js/admin/themes.js')}}"></script>
@endsection

@section('content')
<div class="content themes">
    <div class="org-info">
        <div class="main-info width100">
            <div>
                <label>Дисциплина</label></br>
                <span data-bind="text: $root.current().discipline().name"></span>
            </div></br>
            <div>
                <label>Тема</label></br>
                <!-- ko ifnot: $root.mode() === 'theme.edit' -->
                <span><a data-bind="text: $root.current().theme().name, click: $root.csed().theme().edit"></a></span>
                <!-- /ko -->
                <!-- ko if: $root.mode() === 'theme.edit' -->
                <input type="text" data-bind="value: $root.current().theme().name">
                <span>
                    <button data-bind="click: $root.csed().theme().update" class="fa sq-small">&#xf00c;</button>
                    <button data-bind="click: $root.csed().theme().cancel" class="fa danger sq-small">&#xf00d;</button>
                </span>
                <!-- /ko -->
            </div></br>
            <div>
                <button class="width200" data-bind="click: $root.csed().question().toggleAdd">Добавить вопрос</button>
            </div>
        </div>
    </div>
    <!-- ko if: $root.mode() === 'add' -->
    <div class="themes-add org-info">
        <div>
            <label>Время на ответ</label></br>
            <input type="text" data-bind="value: $root.current().question().minutes" placeholder="00">
            <span>:</span>
            <input type="text" data-bind="value: $root.current().question().seconds" placeholder="00">
        </div>
        <div>
            <label>Изображение</label></br>
            <input type="file" data-bind="value: $root.current().question().image" placefolder="C:/fakepath">
        </div>
        <div>
            <label>Тип вопроса</label></br>
            <select data-bind="options: $root.filter().types,
                       optionsText: 'name',
                       value: $root.current().question().type,
                       optionsCaption: 'Выберите тип'"></select>
        </div>
        <div>
            <label>Сложность вопроса</label></br>
            <select data-bind="options: $root.filter().complexityTypes,
                       optionsText: 'name',
                       value: $root.current().question().complexity,
                       optionsCaption: 'Выберите сложность'"></select>
        </div>
        <div>
            <label>Текст вопроса</label></br>
            <textarea type="text" data-bind="value: $root.current().question().text"></textarea>
        </div>
        <div>
            <label>Варианты ответов</label></br>
            <input data-bind="value: $root.current().answer().name" type="text">
            <button data-bind="click: $root.csed().answer().add" class="fa">&#xf067;</button>
        </div>
        <!-- ko if: $root.current().answers().length -->
        <div>
            <table>
                <tbody data-bind="foreach: $root.current().answers">
                    <tr>
                        <td data-bind="text: id"></td>
                        <td data-bind="text: name"></td>
                        <td>
                            <span level="1" class="radio" data-bind="css: { 'radio-positive': isRight() }, click: $root.toggleCurrent().set().answerCorrectness">Правильный</span>
                            <span>|</span>
                            <span level="0" class="radio" data-bind="css: {'radio-negative':  !isRight() }, click: $root.toggleCurrent().set().answerCorrectness" >Неправильный</span>
                        </td>
                        <td>
                            <button class="fa sq-small danger" data-bind="click: $root.csed().answer().remove">&#xf014;</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- /ko -->
        <div class="btn-larger-group">
            <button class="danger" data-bind="click: $root.csed().question().toggleAdd">Отмена</button>
            <button data-bind="click: $root.csed().question().add">Сохранить вопрос</button>
        </div>
    </div>
    <!-- /ko -->

    <div class="filter">
        <div>
            <label>Название вопроса</label></br>
            <input type="text" data-bind="value: $root.filter().name, valueUpdate: 'keyup'">
        </div>
        <div>
            <label>Тип вопроса</label></br>
            <select data-bind="options: $root.filter().types,
                       optionsText: 'name',
                       value: $root.filter().type,
                       optionsCaption: 'Выберите тип'"></select>
        </div>
        <div>
            <label>Сложность вопроса</label></br>
            <select data-bind="options: $root.filter().complexityTypes,
                       optionsText: 'name',
                       value: $root.filter().complexity,
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
            <!-- ko foreach: $root.current().questions-->
                <tr>
                    <td data-bind="text: text"></td>
                    <td data-bind="text: $root.toggleCurrent().set().type($data)"></td>
                    <td data-bind="text: $root.toggleCurrent().set().complexity($data)"></td>
                    <td>
                        <button data-bind="click: " class="fa">&#xf040;</button>
                        <button data-bind="click: " class="fa danger">&#xf014;</button>
                    </td>
                </tr>
            <!-- /ko -->
            </tbody>
        </table>
    </div>
    <div class="pager-wrap">
        <!-- ko if: ($root.pagination().totalPages()) > 0 -->
        <div class="pager">
            <!-- ko ifnot: $root.pagination().currentPage() == 1 -->
            <button class="" data-bind="click: $root.pagination().selectPage.bind($data, 1)">&lsaquo;&lsaquo;</button>
            <button class="" data-bind="click: $root.pagination().selectPage.bind($data, (currentPage() - 1))">&lsaquo;</button>
            <!-- /ko -->
            <!-- ko foreach: new Array($root.pagination().totalPages()) -->
            <span data-bind="visible: $root.pagination().dotsVisible($index() + 1)">...</span>
            <button class="" data-bind="click: $root.pagination().selectPage.bind($data, ($index()+1)), text: ($index()+1), visible: $root.pagination().pageNumberVisible($index() + 1), css: {current: ($index() + 1) == $root.pagination().currentPage()}"></button>
            <!-- /ko -->
            <!-- ko ifnot: $root.pagination().currentPage() == $root.pagination().totalPages() -->
            <button class="" data-bind="click: $root.pagination().selectPage.bind($data, ($root.pagination().currentPage() + 1))">&rsaquo;</button>
            <button class="" data-bind="click: $root.pagination().selectPage.bind($data, $root.pagination().totalPages())">&rsaquo;&rsaquo;</button>
            <!-- /ko -->
        </div>
        <!-- /ko -->
    </div>
</div>

{{--<div class="g-hidden">--}}
    {{--<div class="box-modal" id="delete-modal">--}}
        {{--<div>--}}
            {{--<div><span>Удалить выбранную дисциплину?</span></div>--}}
            {{--<div>--}}
                {{--<button data-bind="click: $root.csed().remove" class="fa">&#xf00c;</button>--}}
                {{--<button data-bind="click: $root.csed().cancel" class="fa danger arcticmodal-close">&#xf00d;</button>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
{{--<div class="g-hidden">--}}
    {{--<div class="box-modal" id="remove-theme-modal">--}}
        {{--<div>--}}
            {{--<div><span>Удалить выбранную тему?</span></div>--}}
            {{--<div>--}}
                {{--<button data-bind="click: $root.csed().theme().remove" class="fa">&#xf00c;</button>--}}
                {{--<button class="fa danger arcticmodal-close">&#xf00d;</button>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
{{--<div class="g-hidden">--}}
    {{--<div class="box-modal" id="add-theme-modal">--}}
        {{--<div>--}}
            {{--<div><span>Добавление темы</span></div>--}}
            {{--<div>--}}
                {{--<label>Название</label>--}}
                {{--<input type="text" data-bind="value: $root.current().theme().name">--}}
            {{--</div>--}}
            {{--<div>--}}
                {{--<button data-bind="click: $root.csed().theme().add" class="fa">&#xf00c;</button>--}}
                {{--<button class="fa danger arcticmodal-close">&#xf00d;</button>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}

<script type="text/html" id="show-details">
    <div class="org-info">
        <!-- ko if: $root.mode() === 'info' || $root.mode() === 'delete' -->
        <div class="width100" data-bind="template: {name: 'info-mode', data: $data}"></div>
        <!-- /ko -->
        <!-- ko if: $root.mode() === 'edit' || $root.mode() === 'add'-->
        <div class="width100" data-bind="template: {name: 'edit-mode', data: $data}"></div>
        <!-- /ko -->
        <!-- ko if: $root.mode() !== 'add' -->
        <div class="width100">
            <table class="theme">
                <thead>
                <tr><th>#</th><th>Темы</th><th>Действия</th></tr>
                </thead>
                <tbody>
                <!-- ko foreach: $root.current().themes-->
                <tr>
                    <td data-bind="text: $index()+1"></td>
                    <td><a data-bind="text: name"></a></td>
                    <td><button data-bind="click: $root.csed().theme().startRemove" class="fa danger">&#xf014;</button></td>
                </tr>
                <!-- /ko -->
                </tbody>
            </table>
        </div>
        <!-- /ko -->
    </div>
</script>
<script type="text/html" id="info-mode">
    <div class="org-info-details width100 discipline-info">
        <div>
            <label>Дисциплина</label></br>
            {{--<span data-bind="text: discipline().name"></span>--}}
        </div>
        <div>
            <label>Тема</label></br>
            <span data-bind="text: theme().name"></span>
        </div>
        <div>
        <i>
            <button class="move" data-bind="click: $root.csed().theme().startAdd"><span class="fa">&#xf067;</span>&nbsp;Добавить вопрос</button>
        </i>
        <i>
            <button data-bind="click: $root.csed().startUpdate" class="fa">&#xf040;</button>
        </i>
        </div>
    </div>
</script>
<script type="text/html" id="edit-mode">
    <div class="org-info-edit width100 discipline-edit">
        <div>
            <label>Назван</label></br>
            <input type="text" data-bind="value: abbreviation">
        </div>
        <div>
            <label>Полное название дисциплины</label></br>
            <input type="text" data-bind="value: name">
        </div>
        <div>
            <label>Профили</label></br>
            <!-- ko with: $root.current().profile() -->
            <select data-bind="options: profiles, optionsText: 'fullname',  selectedOptions: selected" size="4" multiple="true"></select>
            <!-- /ko -->
        </div>
        <div class="float-btn-group">
            <button data-bind="click: $root.csed().update" class="fa">&#xf00c;</button>
            <button data-bind="click: $root.csed().cancel" class="fa danger">&#xf00d;</button>
        </div>
    </div>
</script>
@endsection