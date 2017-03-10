@extends('layouts.manager')
@section('title', 'Студенты')
@section('javascript')
    <script src="{{ URL::asset('js/admin/students.js')}}"></script>
@endsection

@section('content')
    <div class="content">
        <div class="items">
            <div class="items-head">
                <h1>Учетные записи студентов</h1>
                <label class="adder" data-bind="click: $root.actions.start.create">Добавить</label>
            </div>
            <!-- ko if: $root.mode() === state.create -->
            <div class="details" data-bind="template: {name: 'update-user-info', data: $root.current.student}"></div>
            <!-- /ko -->
            <div class="items-body" data-bind="if: current.students().length">
                <!-- ko foreach: current.students -->
                <div class="item student" data-bind="click: $root.actions.show, css: {'current': id() === $root.current.student().id()}">
                    <!-- ko if: !active() -->
                    <span class="fa radio-important float-right"
                          title="Отклонить заявку на регистрацию"
                          data-bind="click: $root.actions.switch.off">
                        &#xf00d;
                    </span>
                    <span class="fa radio-important float-right"
                          title="Потвердить заявку на регистрацию"
                          data-bind="click: $root.actions.switch.on">
                        &#xf00c;
                    </span>
                    <!-- /ko -->
                    <span data-bind="text: lastname() + ' ' + firstname() + ' ' + patronymic()"></span>
                </div>
                    <!-- ko if: $root.mode() !== state.none && $root.current.student().id() === id()  -->
                    <div class="details" data-bind="template: {name: 'student-info', data: $root.current.student }"></div>
                    <!-- /ko -->
                <!-- /ko -->
            </div>
            @include('shared.pagination')
        </div>
        <div class="filter" data-bind="with: $root.filter">
            <div class="filter-block">
                <label class="title">Студент</label>
                <input type="text" data-bind="value: name, valueUpdate: 'keyup'" placeholder="ФИО студента"/>
            </div>
            <div class="filter-block">
                <label class="title">Группа</label>
                <select data-bind="options: $root.initial.groups,
                       optionsText: 'name',
                       value: $root.filter.group,
                       optionsCaption: 'Выберите группу'"></select>
            </div>
            <div class="filter-block">
                <label class="title">Статус учетной записи</label>
                <input id="all-students" data-bind="checked: request" value="all" type="radio" group="filter" class="custom-radio"/>
                <label class="block" for="all-students">Все</label>
                <input id="active-students" data-bind="checked: request" value="active" type="radio" group="filter" class="custom-radio"/>
                <label class="block" for="active-students">Подтвержденные</label>
                <input id="non-active-students" data-bind="checked: request" value="inactive" type="radio" group="filter" class="custom-radio"/>
                <label class="block" for="non-active-students">Не подтвержденные</label>
            </div>
            <div class="filter-block">
                <span class="clear" data-bind="click: clear">Очистить</span>
            </div>
        </div>
    </div>
@endsection

<script type="text/html" id="student-info">
    <!-- ko if: $root.mode() === state.info -->
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
            <label class="title">Статус</label>
            <span class="radio-important" data-bind="if: active()">Подтверждена</span>
            <span class="radio-negative" data-bind="if: !active()">Ожидает подтверждения</span>
        </div>
    </div>
    <div class="details-row float-buttons">
        <button class="remove" data-bind="click: $root.actions.start.remove"><span class="fa">&#xf014;</span>&nbsp;Удалить</button>
        <button class="approve" data-bind="click: $root.actions.start.update"><span class="fa">&#xf040;</span>&nbsp;Редактировать</button>
    </div>
    <!-- /ko -->
    <!-- ko if: $root.mode() === state.update -->
        <!-- ko template: {name: 'update-user-info', data: $data} -->
        <!-- /ko -->
    <!-- /ko -->
</script>

<script type="text/html" id="update-user-info">
    <div class="details-row">
        <div class="details-column width-48p">
            <div class="details-row">
                <div class="details-column width-98p">
                    <label class="title">Фамилия&nbsp;<span class="required">*</span></label>
                    <input id="iStudentLastName" validate type="text"
                           data-bind="value: lastname,
                           validationElement: lastname,
                           event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-98p">
                    <label class="title">Имя&nbsp;<span class="required">*</span></label>
                    <input id="iStudentFirstName" validate type="text"
                           data-bind="value: firstname,
                           validationElement: firstname,
                           event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-98p">
                    <label class="title">Отчество</label>
                    <input id="iStudentPatronymic" validate type="text"
                           data-bind="value: patronymic,
                           validationElement: patronymic,
                           event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                </div>
            </div>
        </div>
        <div class="details-column width-48p float-right">
            <div class="details-row">
                <div class="details-column width-98p">
                    <label class="title">Группа&nbsp;<span class="required">*</span></label>
                    <select id="sStudentGroupSelection" validate
                            data-bind="options: $root.initial.groups,
                            optionsText: 'name',
                            value: group,
                            optionsCaption: 'Выберите группу',
                            validationElement: group,
                            event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"></select>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-98p">
                    <label class="title">E-mail&nbsp;<span class="required">*</span></label>
                    <input id="iStudentEmail" validate type="text"
                           data-bind="value: email, validationElement: email,
                           event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-98p">
                    <label class="title">Пароль&nbsp;
                        <span class="required" data-bind="if: $root.mode() === state.create">*</span>
                    </label>
                    <!-- ko if: $root.mode() === state.update -->
                    <span class="radio-important" data-bind="click: $root.actions.password.change">Изменить пароль</span>
                    <!-- /ko -->
                    <!-- ko if: $root.mode() === state.create -->
                    <input id="iStudentPassword" type="password" validate
                           data-bind="value: password, validationElement: password,
                           event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                    <!-- /ko -->
                </div>
            </div>
        </div>
    </div>
    <div class="details-row float-buttons">
        <div class="details-column width-100p">
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
                        <input id="iMStudentPassword" validate type="password"
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
                <h3>Удалить выбранную заявку?</h3>
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

{{--<div class="g-hidden">--}}
    {{--<div class="box-modal removal-modal" id="cancel-request-modal">--}}
        {{--<div class="layer zero-margin width-auto">--}}
            {{--<div class="layer-head">--}}
                {{--<h3>Заявка будет удалена. Вы действительно хотите отклонить выбранную заявку?</h3>--}}
            {{--</div>--}}
            {{--<div class="layer-body">--}}
                {{--<div class="details-row float-buttons">--}}
                    {{--<button class="cancel arcticmodal-close">Отмена</button>--}}
                    {{--<button class="remove arcticmodal-close" data-bind="click: $root.actions.end.remove">Удалить</button>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}