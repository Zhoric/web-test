@extends('shared.layout')
@section('title', 'Учебные планы')
@section('javascript')
    <script src="{{ URL::asset('js/admin/studyplan.js')}}"></script>
@endsection
@section('content')
    <div class="content">
        <div class="filter">
            <div>
                <label>Название дисциплины</label></br>
                <input type="text" data-bind="value: $root.filter.discipline, valueUpdate: 'keyup'">
            </div>
        </div>
        <div class="width100 studyplans">
            <!-- ko foreach: $root.current.disciplineplans-->
                <!--ko if: ($root.current.disciplineplan().id() != id() && ($root.mode() === 'none' ||  $root.mode() === 'edit')) || ($root.mode() === 'delete' || $root.mode() === 'none')-->
                <div class="plan-details">
                    <div><label>Дисциплина:</label><br>
                        <span data-bind="text: discipline"></span></div>
                    <div><label>Начальный семестр:</label><br>
                        <span data-bind="text: startSemester"></span></div>
                    <div><label>Количество семестров:</label><br>
                        <span data-bind="text: semestersCount"></span></div>
                    <div><label>Количество часов:</label><br>
                        <span data-bind="text: hours"></span></div>
                    <div><label>Курсовой проект:</label><br>
                        <!-- ko if: hasProject() === true -->
                        <span>Есть</span>
                        <!-- /ko -->
                        <!-- ko if: hasProject() === false -->
                        <span>Нет</span>
                        <!-- /ko -->
                    </div>

                    <div><label>Экзамен:</label><br>
                        <!-- ko if: hasExam() === true -->
                        <span>Есть</span>
                        <!-- /ko -->
                        <!-- ko if: hasExam() === false -->
                        <span>Нет</span>
                        <!-- /ko -->
                    </div>
                    <div><button data-bind="click: $root.plan.startEdit" class="fa success">&#xf040;</button>
                        <button data-bind="click: $root.plan.startDelete" class="fa danger">&#xf014;</button>
                    </div>
                </div>
                <!-- /ko -->
                <!--ko if: $root.mode() === 'edit' && $root.current.disciplineplan().id() === id() -->
                <div data-bind="template: {name: 'edit-mode', data: $root.current.disciplineplan()}"></div>
                <!-- /ko -->
            <!-- /ko -->
        </div>
        <!-- ko if: $root.pagination.itemsCount() > $root.pagination.pageSize() -->
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
        <!-- /ko -->
    </div>

<script type="text/html" id="edit-mode">
    <div class="plan-details-edit plan-details">
        <div><label>Дисциплина:</label><br>
            <select data-bind="options: $root.current.disciplineplans(),
                       optionsText: 'discipline',
                       value: $root.disciplineSelected(),
                       optionsCaption: 'Выберите дисциплину'"></select>
        </div>
        <div><label>Начальный семестр:</label><br>
            <input type="text" data-bind="value: startSemester">
        </div>
        <div><label>Количество семестров:</label><br>
            <input type="text" data-bind="value: semestersCount">
        </div>
        <div><label>Количество часов:</label><br>
            <input type="text" data-bind="value: hours">
        </div>
        <div><label>Курсовой проект:</label><br>
            <span data-bind="click: function(){$data.hasProject(true);}, css: {'plan-form-selected': hasProject()}" class="plan-form">Есть</span>
            <span> | </span>
            <span data-bind="click: function(){$data.hasProject(false);}, css: {'plan-form-selected': !hasProject()}" class="plan-form">Нет</span>
        </div>

        <div><label>Экзамен:</label><br>
            <span data-bind="click: function(){$data.hasExam(true);}, css: {'plan-form-selected': hasExam()}" class="plan-form">Есть</span>
            <span> | </span>
            <span data-bind="click: function(){$data.hasExam(false);}, css: {'plan-form-selected': !hasExam()}" class="plan-form">Нет</span>
        </div>
        <div><button data-bind="click: $root.plan.update" class="fa success">&#xf00c;</button>
            <button data-bind="click: $root.plan.cancelEdit" class="fa danger">&#xf00d;</button>
        </div>
    </div>
</script>
<div class="g-hidden">
    <div class="box-modal" id="delete-plan-modal">
        {{--<div class="box-modal_close arcticmodal-close">закрыть</div>--}}
        <div class="popup-delete">
            <div><h3>Удалить выбранный план дисциплины?</h3></div>
            <div>
                <button data-bind="click: $root.plan.delete" class="fa">&#xf00c;</button>
                <button data-bind="click: $root.plan.cancelDelete" class="fa danger">&#xf00d;</button>
            </div>
        </div>
    </div>
</div>
@endsection
<div class="g-hidden">
    <div class="box-modal" id="errors-modal">
        <div>
            <div>
                <span class="fa">&#xf071;</span>
                <h3>Произошла ошибка</h3>
                <h4 data-bind="text: $root.errors.message"></h4>
            </div>
            <div class="button-holder">
                <button data-bind="click: $root.errors.accept">OK</button>
            </div>
        </div>
    </div>
</div>