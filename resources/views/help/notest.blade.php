@extends('layouts.manager')
@section('title', 'Помощь')
@section('javascript')
    <script src="{{ URL::asset('js/wheelzoom.js') }}"></script>
    <script src="{{ URL::asset('js/admin/help.js')}}"></script>
@endsection

@section('content')
<div class="image-expander" data-bind="click: $root.actions.hide">
    <img class="zoom" data-bind="attr: {src: $root.current.image}"/>
</div>
<div class="content">
    <div class="layer help">
        <div class="layer-head">
            <h1>Студенты не видят теста</h1>
        </div>
        <div class="layer-body">
            <div class="details-row">
                <div class="details-column width-98p">
                    <p>
                        В случае, если после добавления теста, он не отображается у студентов тестируемой группы,
                        необходимо удостовериться, что дисциплина, к которой относится тест,
                        присутствует в учебном плане данной группы. Для этого необходимо:
                    </p>
                    <p>
                        <ol>
                        <li>
                            Перейти на страницу «Группы»,
                            перейти к редактированию интересующей группы и выяснить её учебный план
                            (см. картинку)
                            <img src="{{ URL::asset('images/help/studyplan-discipline.png')}}" data-bind="click: $root.actions.expand"/>
                        </li>
                        <li>Перейти на страницу «Главная».</li>
                        <li>
                            Перейти к учебным планам для направления, к которому относится данная группа.
                            <img src="{{ URL::asset('images/help/show-studyplans.png')}}" data-bind="click: $root.actions.expand"/>
                        </li>
                        <li>
                            Выбрать учебный план группы студентов, у которой не отображается добавленный тест.
                            <img src="{{ URL::asset('images/help/goto-studyplan.png')}}" data-bind="click: $root.actions.expand"/>
                        </li>
                        <li>
                            На странице детализации учебного плана необходимо удостовериться в том, что дисциплина,
                            по которой был создан тест, присутствует в данном плане, а также в том,
                            что в поле «Начальный семестр» для данной дисциплины установлено значение,
                            не превышающее порядковый номер текущего семестра для группы.
                            <img src="{{ URL::asset('images/help/check-semester.png')}}" data-bind="click: $root.actions.expand"/>
                            Если дисциплина отсутствует в учебном плане, или указанный начальный семестр больше,
                            чем текущий семестр для группы, следует обратиться к администратору для коррекции учебного плана группы.
                        </li>
                        </ol>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection