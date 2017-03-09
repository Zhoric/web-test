@extends('layouts.manager')
@section('title', 'Группы')
@section('javascript')
    <script src="{{ URL::asset('js/admin/groups.js')}}"></script>
@endsection

@section('content')
<div class="content">
    <div class="items">
        <div class="items-head">
            <h1>Администрирование групп</h1>
            <label class="adder" data-bind="click: $root.actions.start.create">Добавить</label>
        </div>
        <!-- ko if: $root.mode() === state.create -->
        <div class="details" data-bind="template: {name: 'update-group-info', data: $root.current.group}"></div>
        <!-- /ko -->
        <div class="items-body" data-bind="foreach: $root.current.groups">
            <div class="item" data-bind="click: $root.actions.show, css: {'current' : $root.current.group().id() === id()}">
                <span data-bind="text: name"></span>
                <span class="fa tag float-right" data-bind="click: $root.actions.moveTo.students" title="Перейти к учетным записям студентов">&#xf007;</span>
            </div>
            <!-- ko if: id() === $root.current.group().id() && $root.mode() === state.update -->
            <div class="details" data-bind="template: {name: 'update-group-info', data: $root.current.group}"></div>
            <!-- /ko -->
            <!-- ko if: id() === $root.current.group().id() && ($root.mode() === state.info || $root.mode() === state.remove)  -->
            <div class="details" data-bind="template: {name: 'show-group-info', data: $root.current.group}"></div>
            <!-- /ko -->
        </div>
        @include('shared.pagination')
    </div>
    <div class="filter">
        <div class="filter-block">
            <label class="title">Группа </label>
            <input type="text" data-bind="value: $root.filter.name, valueUpdate: 'keyup'" placeholder="Название группы">
        </div>
        <div class="filter-block">
            <span class="clear" data-bind="click: $root.filter.clear">Очистить</span>
        </div>
    </div>
</div>
<div class="g-hidden">
    <div class="box-modal removal-modal" id="remove-group-modal">
        <div class="layer zero-margin width-auto">
            <div class="layer-head">
                <h3>Вы действительно хотите удалить выбранную группу?</h3>
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

<div class="g-hidden">
    <div class="box-modal" id="select-plan-modal">
        <div class="layer zero-margin width-auto">
            <h3>Учебный план</h3>
            <div class="details-row">
                <div class="details-column width-98p">
                    <select data-bind="options: $root.current.institutes,
                       optionsText: 'name',
                       value: $root.current.institute,
                       optionsCaption: 'Институт'"></select>
                </div>
                <div class="details-column width-98p">
                    <select data-bind="options: $root.current.profiles,
                       optionsText: 'name',
                       value: $root.current.profile,
                       optionsCaption: 'Направление подготовки',
                       enable: $root.current.institute()"></select>
                </div>
                <div class="details-column width-98p">
                    <select data-bind="options: $root.current.plans,
                       optionsText: 'name',
                       value: $root.current.plan,
                       optionsCaption: 'Учебный план',
                       enable: $root.current.institute() && $root.current.profile()"></select>
                </div>
            </div>
            <div class="details-row float-buttons">
                <div class="details-column width-99p">
                    <button data-bind="click: $root.actions.selectPlan.cancel" class="cancel">Отмена</button>
                    <button data-bind="click: $root.actions.selectPlan.end" class="approve">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script type="text/html" id="update-group-info">
    <div class="details-row">
        <div class="details-column width-45p">
            <div class="details-row">
                <div class="details-column width-50p">
                    <label class="title">Префикс</label>
                    <input id="iGroupPrefix" validate type="text"
                           data-bind="value: prefix,
                           validationElement: prefix,
                           event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-50p">
                    <label class="title">Курс&nbsp;<span class="required">*</span></label>
                    <input id="iGroupCourse" validate type="text"
                           data-bind="value: course,
                           validationElement: course,
                           event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-50p">
                    <label class="title">Номер группы&nbsp;<span class="required">*</span></label>
                    <input id="iGroupNumber" type="text" validate
                           data-bind="value: number,
                           validationElement: number,
                           event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                </div>
            </div>
        </div>
        <div class="details-column width-50p">
            <div class="details-row">
                <div class="details-column">
                    <label class="title">Форма обучения</label>
                    <span class="radio form-heights" data-bind="click: $root.actions.switchForm.day, css: {'radio-important' : isFulltime()}">Очная</span>
                    <span>|</span>
                    <span class="radio form-heights" data-bind="click: $root.actions.switchForm.night, css: {'radio-important' : !isFulltime()}">Заочная</span>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-70p">
                    <label class="title">
                        Полное&nbsp;наименование&nbsp;группы
                        <span class="coloredin-patronus bold pointer" data-bind="click: $root.actions.generate">(Сгенерировать)</span>
                        <span class="required">*</span>
                    </label>
                    <input id="iGroupNameGenerated" validate type="text"
                           data-bind="value: name,
                           enable: $root.current.isGenerated,
                           validationElement: name,
                           event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-98p">
                    <label class="title">Учебный план&nbsp;<span class="required">*</span></label>
                    <!-- ko if: $root.current.groupPlan() -->
                    <span class="form-heights info coloredin-patronus pointer"
                          data-bind="text: $root.current.groupPlan().name,
                          click: $root.actions.selectPlan.start">
                    </span>
                    <!-- /ko -->
                    <span class="form-heights info coloredin-patronus pointer" id="sGroupStudyPlan"
                          validate special title="Пожалуйста, выберите учебный план"
                          data-bind="if: !$root.current.groupPlan(),
                          click: $root.actions.selectPlan.start">Изменить
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="details-row float-buttons">
        <div class="details-column width-100p">
            <button data-bind="click: $root.actions.show" class="cancel">Отмена</button>
            <button id="bUpdateGroup" accept-validation class="approve"
                    title="Проверьте правильность заполнения полей"
                    data-bind="click: $root.actions.end.update" >Сохранить</button>
        </div>
    </div>
</script>
<script type="text/html" id="show-group-info">
    <div class="details-row">
        <div class="details-column">
            <label class="title">Курс</label>
            <span class="info" data-bind="text: course"></span>
        </div>
        <div class="details-column">
            <label class="title">Номер&nbsp;группы</label>
            <span class="info" data-bind="text: number"></span>
        </div>
        <div class="details-column">
            <label class="title">Форма&nbsp;обучения</label>
            <span class="info" data-bind="text: isFulltime() ? 'Очная' : 'Заочная'"></span>
        </div>
        <!-- ko if: $root.current.groupPlan() -->
        <div class="details-column">
            <label class="title">Учебный&nbsp;план</label>
            <span class="info" data-bind="text: $root.current.groupPlan().name"></span>
        </div>
        <!-- /ko -->
    </div>
    <div class="details-row float-buttons">
        <div class="details-column width-100p">
            <!-- ko if: $root.current.hasInactive-->
            <span class="info coloredin-patronus pointer bold"
                  data-bind="click: $root.actions.approveStudents">
                Подтвердить учетные записи
            </span>
            <!-- /ko -->
            <!-- ko if: $root.user.role() === role.admin.name -->
            <button data-bind="click: $root.actions.start.remove" class="remove">Удалить</button>
            <button data-bind="click: $root.actions.start.update" class="approve">Редактировать</button>
            <!-- /ko -->
        </div>
    </div>
</script>