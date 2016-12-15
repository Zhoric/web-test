@extends('shared.layout')
@section('title', 'Студенты')
@section('javascript')
    <script src="{{ URL::asset('js/admin/students.js')}}"></script>
@endsection

@section('content')
    <div class="content">
        <div class="items">
            <div class="items-head">
                <h1>Администрирование учетных записей студентов</h1>
                <label class="adder" data-bind="click: $root.actions.start.create">Добавить</label>
            </div>
            <h3 class="text-center" data-bind="if: !filter.group()">Пожалуйста, выберите группу</h3>
            <div class="items-body" data-bind="if: current.students().length">
                <!-- ko foreach: current.students -->
                <div class="item" data-bind="click: $root.actions.show">
                    <span class="float-right" data-bind="if: active()">Подтверждена</span>
                    <span class="float-right" data-bind="if: !active()">Ожидает подтверждения</span>
                    <span data-bind="text: lastname() + ' ' + firstname() + ' ' + patronymic()"></span>
                </div>
                    <!-- ko if: $root.mode() !== state.none && $root.current.student().id() === id()  -->
                    <div class="details" data-bind="template: {name: 'student-info', data: $root.current.student }"></div>
                    <!-- /ko -->
                <!-- /ko -->
            </div>
            @include('admin.shared.pagination')
        </div>
        <div class="filter" data-bind="with: $root.filter">
            <div class="filter-block">
                <label class="title">ФИО студента</label>
                <input type="text" data-bind="value: name"/>
            </div>
            <div class="filter-block">
                <label class="title">Название группы</label>
                <select data-bind="options: $root.initial.groups,
                       optionsText: 'name',
                       value: group,
                       optionsCaption: 'Выберите группу'"></select>
            </div>
            <div class="filter-block"></div>
            <div class="filter-block"></div>
            <div class="filter-block"></div>
            <div class="filter-block"></div>
        </div>
    </div>
    @include('admin.shared.error-modal')
@endsection

<script type="text/html" id="student-info">
    <!-- ko if: $root.mode() === state.info -->
    <div class="details-row">
        <div class="details-column">
            <label class="title">ФИО</label>
            <span class="info" data-bind="text: lastName() + ' ' + firstName() + ' ' + middleName()"></span>
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
        <div class="details-column width-31p">
            <label class="title">Фамилия</label>
            <input type="text" data-bind="value: lastName"/>
        </div>
        <div class="details-column width-31p">
            <label class="title">Имя</label>
            <input type="text" data-bind="value: firstName"/>
        </div>
        <div class="details-column width-31p">
            <label class="title">Отчество</label>
            <input type="text" data-bind="value: middleName"/>
        </div>
    </div>
    <div class="details-row">
        <div class="details-column width-55p">
            <label class="title">E-mail</label>
            <input type="text" data-bind="value: email"/>
        </div>
        <div class="details-column width-15p">
            <label class="title">Группа<span>(Перевести)</span></label>
            <span class="info" data-bind="text: group.name"></span>
        </div>
        <div class="details-column width-20p">
            <label class="title">Пароль</label>
            <span class="radio-important">Изменить пароль</span>
        </div>
    </div>
    <div class="details-row">
        <div class="details-column width-98p">
            <label class="title">Статус учётной записи</label>
            <span class="radio radio-important">Подтвердить</span>
            <span>|</span>
            <span class="radio">Отклонить</span>
        </div>
    </div>
    <div class="details-row float-buttons">
        <div class="details-column width-100p">
            <button class="cancel">Отмена</button>
            <button class="approve">Сохранить</button>
        </div>
    </div>
</script>