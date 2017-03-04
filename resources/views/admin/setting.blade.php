@extends('layouts.manager')
@section('title', 'Администрирование')
@section('javascript')
    <link rel="stylesheet" href="{{ URL::asset('css/datepicker.css')}}"/>
    <script src="{{ URL::asset('js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{ URL::asset('js/bootstrap.min.js')}}"></script>
    <script src="{{ URL::asset('js/helpers/datepicker.js')}}"></script>
    <script src="{{ URL::asset('js/admin/setting.js')}}"></script>
@endsection

@section('content')
<div class="content">
    <div class="layer">
        <div class="details-row">
            <div class="details-column width-98p">
                <a data-bind="click: $root.actions.results.start">Удаление результатов тестирования</a>
            </div>
        </div>
    </div>
</div>
@endsection

<div class="g-hidden">
    <div class="box-modal" id="remove-test-results-modal">
        <div class="layer width-auto zero-margin">
            <div class="layer-head">
                <h3>Удаление результатов тестирования</h3>
            </div>
            <div class="layer-body">
                <div class="details-row">
                    <div class="details-column width-98p">
                        <label class="title inline">Удалить все записи до</label>
                        <span class="fa pointer date-ico" data-bind="datePicker: $root.current.resultsDate">&#xf073;</span>
                        <span data-bind="text: $root.current.resultsDate.parseDay()"></span>
                    </div>
                </div>
                <div class="details-row float-buttons minh-40">
                    <button data-bind="click: $root.actions.results.cancel" class="cancel arcticmodal-close">Отмена</button>
                    <button data-bind="click: $root.actions.results.end" class="remove">Удалить результаты</button>
                </div>
            </div>
        </div>
    </div>
</div>