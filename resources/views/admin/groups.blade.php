@extends('shared.layout')
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
        <div class="details" data-bind="template: {name: 'show-group-info', data: $root.current.group}"></div>
        <!-- /ko -->
        <div class="items-body" data-bind="foreach: $root.current.groups">
            <div class="item">
                <span data-bind="text: name"></span>
                <!-- ko if: $root.user.role() === role.admin.name -->
                <span class="fa tag float-right" data-bind="click: $root.actions.start.remove" title="Удалить группу">&#xf1f8;</span>
                <!-- /ko -->
                <span class="fa tag float-right" data-bind="click: $root.actions.start.update" title="Редактировать">&#xf040;</span>
                <span class="fa tag float-right" data-bind="click: $root.actions.moveTo.students" title="Перейти к учетным записям студентов">&#xf007;</span>
            </div>
            <!-- ko if: id() === $root.current.group().id() && $root.mode() === state.update -->
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
    <div class="box-modal" id="remove-group-modal">
        <div class="popup-delete">
            <div><h3>Вы действительно хотите удалить выбранную группу?</h3></div>
            <div>
                <button class="remove" data-bind="click: $root.actions.end.remove">Удалить</button>
                <button class="cancel arcticmodal-close" data-bind="click: $root.actions.cancel">Отмена</button>
            </div>
        </div>
    </div>
</div>

<div class="g-hidden">
    <div class="box-modal" id="select-plan-modal">
        <div class="box-modal_close arcticmodal-close"><span class="fa modal-close">&#xf00d;</span></div>
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

    @include('shared.error-modal')
@endsection

<script type="text/html" id="show-group-info">
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
                        <span class="coloredin-patronus bold" data-bind="click: $root.actions.generate">(Сгенерировать)</span>
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
            <button data-bind="click: $root.actions.cancel" class="cancel">Отмена</button>
            <button id="bUpdateGroup" accept-validation class="approve"
                    title="Проверьте правильность заполнения полей"
                    data-bind="click: $root.actions.end.update" >Сохранить</button>
        </div>
    </div>
</script>
<script type="text/html" id="update-group">

</script>
