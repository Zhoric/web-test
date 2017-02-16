@extends('shared.layout')
@section('title', 'Дисциплины')
@section('javascript')

    <script src="{{ URL::asset('js/knockout.autocomplete.js')}}"></script>
    <script src="{{ URL::asset('js/helpers/ko-multiselect.js')}}"></script>
    <script src="{{ URL::asset('js/admin/disciplines.js')}}"></script>
@endsection

@section('content')
<div class="content">
    <div class="items disciplines">
        <div class="items-head">
            <h1>Администрирование дисциплин</h1>
            <label class="adder" data-bind="click: $root.csed.startAdd">Добавить</label>
        </div>
        <!-- ko if: $root.mode() === 'add'-->
        <div data-bind="template: {name: 'show-details', data: $root.current.discipline}"></div>
        <!-- /ko -->
        <div class="items-body">
            <!-- ko foreach: disciplines -->
            <div class="item" data-bind="click: $root.csed.show, css: {'current': $root.current.discipline().id() === id()}">
                <span class="fa tag float-right" data-bind="click: $root.csed.showSections" title="Общие разделы">&#xf0f6;</span>
                <span class="fa tag float-right" data-bind="click: $root.moveTo.tests" title="Перейти к тестам">&#xf022;</span>
                <span data-bind="text: name"></span>
            </div>
            <!-- ko if: $root.mode() !== 'none' && $data.id() === $root.current.discipline().id()-->
            <div data-bind="template: {name: 'show-details', data: $root.current.discipline}"></div>
            <!-- /ko -->
            <!-- /ko -->
        </div>
        @include('admin.shared.pagination')
    </div>
    <div class="filter">
        <div class="filter-block">
            <label class="title">Дисциплина</label>
            <input type="text" data-bind="value: $root.filter.discipline, valueUpdate: 'keyup'" placeholder="Название дисциплины">
        </div>
        <div class="filter-block">
            <label class="title">Направление</label>
            <select data-bind="options: $root.multiselect.source,
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
    <div class="box-modal" id="delete-modal">
        <div class="popup-delete">
            <div><h3>Удалить выбранную дисциплину?</h3></div>
            <div>
                <button data-bind="click: $root.csed.remove" class="fa">&#xf00c;</button>
                <button data-bind="click: $root.csed.cancel" class="fa danger arcticmodal-close">&#xf00d;</button>
            </div>
        </div>
    </div>
</div>
<div class="g-hidden">
    <div class="box-modal" id="remove-theme-modal">
        <div class="popup-delete">
            <div><h3>Удалить выбранную тему?</h3></div>
            <div>
                <button data-bind="click: $root.actions.theme.end.remove" class="fa">&#xf00c;</button>
                <button data-bind="click: $root.actions.theme.cancel" class="fa danger arcticmodal-close">&#xf00d;</button>
            </div>
        </div>
    </div>
</div>
<div class="g-hidden">
    <div class="box-modal" id="add-theme-modal">
        <div class="popup-theme">
            <div><h3>Добавление темы</h3></div>
            <div>
                <label>Название</label></br>
                <input type="text" data-bind="value: $root.current.theme().name" placeholder="Название темы">
            </div>
            <div class="popup-btn-group">
                <button data-bind="click: $root.csed.theme.add" class="fa">&#xf00c;</button>
                <button class="fa danger arcticmodal-close">&#xf00d;</button>
            </div>
        </div>
    </div>
</div>

<script type="text/html" id="show-details">
    <div class="">
        <!-- ko if: $root.mode() === 'info' || $root.mode() === 'delete' || $root.mode() === 'section' -->
        <div data-bind="template: {name: 'info-mode', data: $data}"></div>
        <!-- /ko -->
        <!-- ko if: $root.mode() === 'edit' || $root.mode() === 'add'-->
        <div data-bind="template: {name: 'edit-mode', data: $data}"></div>
        <!-- /ko -->
        <!-- ko if: $root.mode() !== 'add' -->
        <div class="details discipline">
            <div class="details-row">
                <table class="werewolf themes">
                    <thead>
                        <tr><th>№</th><th>Темы</th><th>Действия</th></tr>
                    </thead>
                    <tbody>
                    <!-- ko if: $root.current.theme().mode() !== state.create-->
                    <tr class="adder-row">
                        <td colspan="3" data-bind="click: $root.actions.theme.start.add">
                            <span class="fa">&#xf067;</span>&nbsp;Добавить тему
                        </td>
                    </tr>
                    <!-- /ko -->
                    <!-- ko if: $root.current.theme().mode() === state.create -->
                    <tr class="input-row">
                        <td colspan="2">
                            <input type="text" data-bind="value: $root.current.theme().name" placeholder="Название темы"/>
                        </td>
                        <td>
                            <button data-bind="click: $root.actions.theme.end.add" class="fa approve mini">&#xf00c;</button>
                            <button data-bind="click: $root.actions.theme.cancel" class="fa remove mini">&#xf00d;</button>
                        </td>
                    </tr>
                    <!-- /ko -->
                    <!-- ko foreach: $root.current.themes-->
                    <tr data-bind="click: $root.actions.theme.move">
                        <td data-bind="text: $index()+1"></td>
                        <td data-bind="text: name"><a data-bind="text: name, click: $root.moveTo.theme"></a></td>
                        <td>
                            <button data-bind="click: $root.csed.theme.showSections" class="fa approve mini actions">&#xf0f6;</button>
                            <button data-bind="click: $root.actions.theme.start.remove" class="fa remove mini actions">&#xf014;</button>
                        </td>
                    </tr>
                    <!-- /ko -->
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /ko -->
    </div>
</script>
<script type="text/html" id="info-mode">
    <div class="details discipline">
        <div class="details-row">
            <div class="details-column width-20p">
                <label class="title">Аббревиатура</label>
                <span class="info" data-bind="text: abbreviation"></span>
            </div>
            <div class="details-column width-75p">
                <label class="title">Полное название дисциплины</label>
                <span class="info" data-bind="text: name"></span>
            </div>
        </div>
        <div class="details-row float-buttons">
            <div class="details-column width-100p">
                <button data-bind="click: $root.csed.startRemove" class="remove"><span class="fa">&#xf014;</span>&nbsp;Удалить</button>
                <button data-bind="click: $root.csed.startUpdate" class="approve"><span class="fa">&#xf040;</span>&nbsp;Редактировать</button>
            </div>
        </div>
    </div>
</script>
<script type="text/html" id="edit-mode">
    <div class="details discipline">
        <div class="details-row">
            <div class="details-column width-20p">
                <label class="title">Аббревиатура&nbsp;<span class="required">*</span></label>
                <input id="iAbbreviation" validate type="text"
                       data-bind="value: abbreviation,validationElement: abbreviation,
                           event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
            </div>
            <div class="details-column width-75p">
                <label class="title">Полное название дисциплины&nbsp;<span class="required">*</span></label>
                <input id="iFullName" validate type="text"
                       data-bind="value: name,validationElement: name,
                           event: {focusout: $root.events.focusout, focusin: $root.events.focusin}">
            </div>
        </div>
        <div class="details-row">
            <div class="details-column width-99p">
                <label class="title">Профили&nbsp;<span class="required">*</span></label>
                <div class="multiselect-wrap theme" id="dProfiles" validate special
                     title="Пожалуйста, укажите хотя бы один профиль">
                    <!-- ko if: $root.multiselect.tags().length -->
                    <div class="multiselect">
                        <ul data-bind="foreach: $root.multiselect.tags">
                            <li><span data-bind="click: $root.multiselect.remove" class="fa">&#xf00d;</span><span data-bind="text: fullname"></span></li>
                        </ul>
                    </div>
                    <!-- /ko -->
                    <input placeholder="Начните вводить название направления"
                            data-bind="autocomplete: {
                            data: $root.multiselect.source,
                            format: $root.multiselect.text,
                            onSelect: $root.multiselect.select},
                            css: {'full': $root.multiselect.tags().length}"
                            type="text" value=""/>
                </div>
            </div>
        </div>
        <div class="details-row float-buttons">
            <div class="details-column width-100p">
                <button data-bind="click: $root.csed.cancel" class="cancel">Отмена</button>
                <button id="bAcceptDiscipline" title="Проверьте правильность заполнения полей"
                        data-bind="click: $root.csed.update"
                        accept-validation class="approve">Сохранить
                </button>
            </div>
        </div>
    </div>
</script>

<div class="g-hidden">
    <div class="box-modal" id="sections-modal">
        <div class="box-modal_close arcticmodal-close">закрыть</div>
        <div class="width100">
            <div>
                <button data-bind="click: $root.csed.theme.addSection" class="add-section"><span class="fa">&#xf067;</span>&nbsp;Добавить новый раздел</button>
            </div>
            <!-- ko if:  $root.current.sections().length > 0-->
            <div class="section-info">
            <table class="theme">
                <thead>
                <tr><th>#</th><th>Название</th><th>Действия</th></tr>
                </thead>
                <tbody>
                <!-- ko foreach: $root.current.sections-->
                <tr>
                    <td data-bind="text: $index()+1"></td>
                    <td><a data-bind="click: $root.csed.section.info, text: name"></a></td>
                    <td><button data-bind="click: $root.csed.section.info" class="fa success">&#xf0f6;</button>
                        <button data-bind="click: $root.csed.section.edit" class="fa info">&#xf040;</button>
                        <button data-bind="click: $root.csed.section.startRemove" class="fa danger">&#xf014;</button>
                    </td>
                </tr>
                <!-- /ko -->
                </tbody>
            </table>
            </div>
            <!-- /ko -->
            <!-- ko if:  $root.current.sections().length == 0-->
            <div class="section-info">
                <p>Для данной части разделы отсутствуют</p>
            </div>
            <!-- /ko -->

        </div>
    </div>
</div>
<div class="g-hidden">
    <div class="box-modal" id="remove-section-modal">
        <div class="popup-delete">
            <div><span>Удалить выбранный раздел?</span></div>
            <div>
                <button data-bind="click: $root.csed.section.remove" class="fa">&#xf00c;</button>
                <button class="fa danger arcticmodal-close">&#xf00d;</button>
            </div>
        </div>
    </div>
</div>

@include('admin.shared.error-modal')
@endsection