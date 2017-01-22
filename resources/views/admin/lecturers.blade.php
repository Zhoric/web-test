@extends('shared.layout')
@section('title', 'Преподаватели')
@section('javascript')
    <script src="{{ URL::asset('js/admin/lecturers.js')}}"></script>
@endsection

@section('content')
    <div class="content">
        <div class="items">
            <div class="items-head">
                <h1>Администрирование учетных записей преподавателей</h1>
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
        <div class="filter" data-bind="with: $root.filter">
            <div class="filter-block">
                <label class="title">ФИО преподавателя</label>
                <input type="text" data-bind="value: name"/>
            </div>
        </div>
    </div>
    @include('shared.error-modal')
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
                <div class="details-column width-98p">
                    <label class="title">Фамилия</label>
                    <input type="text" data-bind="value: lastname"/>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-98p">
                    <label class="title">Имя</label>
                    <input type="text" data-bind="value: firstname"/>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-98p">
                    <label class="title">Отчество</label>
                    <input type="text" data-bind="value: patronymic"/>
                </div>
            </div>
        </div>
        <div class="details-column width-48p float-right">
            <div class="details-row">
                <div class="details-column width-98p">
                    <label class="title">E-mail</label>
                    <input type="text" data-bind="value: email"/>
                </div>
            </div>
            <div class="details-row">
                <div class="details-column width-98p">
                    <label class="title">Пароль</label>
                    <!-- ko if: $root.mode() === state.update -->
                    <span class="radio-important" data-bind="click: $root.actions.password.change">Изменить пароль</span>
                    <!-- /ko -->
                    <!-- ko if: $root.mode() === state.create -->
                    <input type="password" data-bind="value: $root.current.password"/>
                    <!-- /ko -->
                </div>
            </div>
        </div>
    </div>
    <div class="details-row"></div>
    <div class="details-row float-buttons">
        <div class="details-column width-100p">
            <button class="cancel" data-bind="click: $root.actions.cancel">Отмена</button>
            <button class="approve" data-bind="click: $root.actions.end.update">Сохранить</button>
        </div>
    </div>
</script>

<div class="g-hidden">
    <div class="box-modal" id="change-password-modal">
        <div class="popup-delete">
            <div>
                <label class="title">Новый пароль</label>
                <input type="password" data-bind="value: $root.current.password" />
            </div>
            <div>
                <button data-bind="click: $root.actions.password.approve" class="approve">Изменить пароль</button>
                <button data-bind="click: $root.actions.password.cancel" class="cancel arcticmodal-close">Отмена</button>
            </div>
        </div>
    </div>
</div>

<div class="g-hidden">
    <div class="box-modal" id="change-success-modal">
        <div class="popup-delete">
            <div>
                <h3>Пароль успешно изменён</h3>
            </div>
            <div>
                <button class="approve arcticmodal-close">OK</button>
            </div>
        </div>
    </div>
</div>

<div class="g-hidden">
    <div class="box-modal" id="remove-request-modal">
        <div class="popup-delete">
            <h3>Удалить выбранную заявку?</h3>
            <div>
                <button class="remove arcticmodal-close" data-bind="click: $root.actions.end.remove">Удалить</button>
                <button class="cancel arcticmodal-close">Отмена</button>
            </div>
        </div>
    </div>
</div>

<div class="g-hidden">
    <div class="box-modal" id="cancel-request-modal">
        <div class="popup-delete">
            <div>
                <h3 class="text-center">Заявка будет удалена. Вы действительно хотите отклонить выбранную заявку?</h3>
            </div>
            <div>
                <button class="remove arcticmodal-close" data-bind="click: $root.actions.end.remove">Удалить</button>
                <button class="cancel arcticmodal-close">Отмена</button>
            </div>
        </div>
    </div>
</div>