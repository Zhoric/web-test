@extends('shared.layout')
@section('title', 'Учебные планы')
@section('javascript')
    <script src="{{ URL::asset('js/admin/studyplans.js')}}"></script>
@endsection
@section('content')
    <div class="content">
        <div class="filter">
            <div>
                <label>Направление</label></br>
                <select data-bind="options: $root.current.profile().profiles,
                       optionsText: 'name',
                       value: $root.filter.profile,
                       optionsCaption: 'Выберите профиль'"></select>
            </div>
        </div>
        <div class="org-accordion">
            <div class="org-item">
                <span class="fa">&#xf067;</span>
            </div>
            <!-- ko foreach: studyplans -->
            <div class="org-item" data-bind="text: name, click: $root.move"></div>
            <!-- /ko -->
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