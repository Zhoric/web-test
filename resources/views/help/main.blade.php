@extends('layouts.manager')
@section('title', 'Помощь')
@section('javascript')
    <script src="{{ URL::asset('js/min/manager-help.js')}}"></script>
@endsection

@section('content')
<div class="image-expander" data-bind="click: $root.actions.hide">
    <img class="zoom" data-bind="attr: {src: $root.current.image}"/>
</div>
<div class="content">
    <div class="layer help">
        <div class="layer-head">
            <h1>Помощь</h1>
        </div>
        <div class="layer-body">
            <div class="details-row">
                <div class="details-column width-98p">
                    <p>
                        <a href="/admin/help/test">Создание теста</a>
                    </p>
                    <p>
                        <a href="/admin/help/types">Типы тестов</a>
                    </p>
                    <p>
                        <a href="/admin/help/notest">Студент не видит теста</a>
                    </p>
                    <p>
                        <a href="/admin/help/results">Проверка тестов</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection