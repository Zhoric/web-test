@extends('layouts.manager')
@section('title', 'Преподаватели')
@section('javascript')
    <script src="{{ URL::asset('js/knockout.multiselect.js')}}"></script>
    <script src="{{ URL::asset('js/admin/lecturers.js')}}"></script>
@endsection

@section('content')
    <div class="content">
        <div class="items">
            <div class="items-head">
                <h1>Учетные записи преподавателей</h1>
                <label class="adder" data-bind="click: $root.actions.start.create">Добавить</label>
            </div>
            <!-- ko if: $root.mode() === state.create -->
            <div class="details" data-bind="template: {name: 'update-user-info', data: $root.current.lecturer}"></div>
            <!-- /ko -->
            <div class="items-body" data-bind="if: $root.current.lecturers().length">
                <!-- ko foreach: $root.current.lecturers -->
                <div class="item" data-bind="click: $root.actions.show, css: {'current': lecturer.id() === $root.current.lecturer().id()}">
                    <span data-bind="text: lecturer.lastname() + ' ' + lecturer.firstname() + ' ' + lecturer.patronymic()"></span>
                </div>
                <!-- ko if: $root.current.lecturer().id() === lecturer.id()-->
                    <!-- ko if: $root.mode() === state.info  -->
                    <div class="details" data-bind="template: {name: 'lecturer-info', data: $root.current.lecturer }"></div>
                    <!-- /ko -->
                    <!-- ko if: $root.mode() === state.update  -->
                    <div class="details" data-bind="template: {name: 'update-user-info', data: $root.current.lecturer }"></div>
                    <!-- /ko -->
                <!-- /ko -->
                <!-- /ko -->
            </div>
            @include('shared.pagination')
        </div>
        <div class="filter">
            <div class="filter-block">
                <label class="title">Преподаватель</label>
                <input type="text" placeholder="ФИО преподавателя"
                       data-bind="value: filter.name, valueUpdate: 'keyup'"/>
            </div>
            <div class="filter-block">
                <span class="clear" data-bind="click: filter.clear">Очистить</span>
            </div>
        </div>
    </div>
@endsection

<script type="text/html" id="lecturer-info">
    <div class="details-row">
        <div class="details-column">
            <label class="title">ФИО</label>
            <span class="info" data-bind="text: lastname() + ' ' + firstname() + ' ' + patronymic()"></span>
        </div>
        <div class="details-column">
            <label class="title">E-mail</label>
            <span class="info" data-bind="text: email"></span>
        </div>
        <div class="details-column">
            <label class="title">Дисциплины</label>
            <!-- ko if: !$root.current.disciplines().length -->
            <span class="info">Нe указано</span>
            <!-- /ko  -->
            <!-- ko foreach: $root.current.disciplines -->
            <span class="info" data-bind="text: $index()+1 < $root.current.disciplines().length ? abbreviation() + ',' : abbreviation()"></span>
            <!-- /ko -->
        </div>
    </div>
    <div class="details-row float-buttons">
        <button class="remove" data-bind="click: $root.actions.start.remove"><span class="fa">&#xf014;</span>&nbsp;Удалить</button>
        <button class="approve" data-bind="click: $root.actions.start.update"><span class="fa">&#xf040;</span>&nbsp;Редактировать</button>
    </div>
</script>

<script type="text/html" id="update-user-info">
    <div class="details-row">
        <div class="details-column width-48p">
            <div class="details-row">
                <div class="details-column width-98p zero-margin">
                    <label class="title">Фамилия&nbsp;<span class="required">*</span></label>
                    <input id="iLastName" type="text" validate
                           data-bind="value: lastname,
                           validationElement: lastname,
                           event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-98p zero-margin">
                    <label class="title">Имя&nbsp;<span class="required">*</span></label>
                    <input id="iFirstName" validate type="text"
                           data-bind="value: firstname,
                           validationElement: firstname,
                           event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-98p zero-margin">
                    <label class="title">Отчество</label>
                    <input id="iPatronymic" validate type="text"
                           data-bind="value: patronymic,
                           validationElement:patronymic,
                           event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                </div>
            </div>
        </div>
        <div class="details-column width-48p float-right">
            <div class="details-row">
                <div class="details-column width-98p zero-margin">
                    <label class="title">E-mail&nbsp;<span class="required">*</span></label>
                    <input id="IEmail" validate type="text"
                           data-bind="value: email,
                           validationElement: email,
                           event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-98p zero-margin">
                    <label class="title">Пароль&nbsp;<span class="required" data-bind="if: $root.mode() === state.create">*</span></label>
                    <!-- ko if: $root.mode() === state.update -->
                    <span class="radio-important" data-bind="click: $root.actions.password.change">Изменить пароль</span>
                    <!-- /ko -->
                    <!-- ko if: $root.mode() === state.create -->
                    <input  id="iPassword" validate type="password"
                            data-bind="value: password,
                            validationElement: password,
                            event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                    <!-- /ko -->
                </div>
            </div>
        </div>
    </div>
    <div class="details-row">
        <div class="details-column width-98p" data-bind="with: $root.multiselect">
            <label class="title">Дисциплины</label>
            <multiselect params="{source: data, tags: tags, textField: 'name'}"></multiselect>
        </div>
    </div>
    <div class="details-row float-buttons">
        <div class="details-column width-99p">
            <button class="cancel" data-bind="click: $root.actions.cancel">Отмена</button>
            <button id="bUpdateLecturer" accept-validation class="approve"
                    title="Проверьте правильность заполнения полей"
                    data-bind="click: $root.actions.end.update">Сохранить</button>
        </div>
    </div>
</script>

<div class="g-hidden">
    <div class="box-modal" id="change-password-modal">
        <div class="layer width-auto zero-margin">
            <div class="layer-head">
                <h3>Новый пароль</h3>
            </div>
            <div class="layer-body">
                <div class="details-row">
                    <div class="details-column width-98p">
                        <input id="iMPassword" validate type="password"
                               data-bind="value: $root.current.password,
                               validationElement: $root.current.password,
                               event: {focusout: $root.events.focusout, focusin: $root.events.focusin}" />
                    </div>
                </div>
                <div class="details-row float-buttons">
                    <button data-bind="click: $root.actions.password.cancel" class="cancel arcticmodal-close">Отмена</button>
                    <button data-bind="click: $root.actions.password.approve" class="approve">Изменить пароль</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="g-hidden">
    <div class="box-modal removal-modal" id="remove-request-modal">
        <div class="layer zero-margin width-auto">
            <div class="layer-head">
                <h3>Удалить выбранного преподавателя?</h3>
            </div>
            <div class="layer-body">
                <div class="details-row float-buttons">
                    <button class="cancel arcticmodal-close">Отмена</button>
                    <button class="remove arcticmodal-close" data-bind="click: $root.actions.end.remove">Удалить</button>
                </div>
            </div>
        </div>
    </div>
</div>
