@extends('layouts.manager')
@section('title', 'Редактор')
@section('javascript')
    <script src="{{ URL::asset('js/admin/editor.js')}}"></script>
   <script src="{{ URL::asset('js/tinymce/tinymce.min.js')}}"></script>
   <script></script>
@endsection
@section('content')
    <div class="content">
        <div class="editor-buttons">
            <button data-bind="click: $root.approve" class="fa success">&#xf00c;</button>
            <button data-bind="click: $root.move" class="fa danger">&#xf00d;</button>
        </div>
        <div class="editor-name">
            <label>Название документа</label>
            <input id="iName" type="text" data-bind="value: $root.name">
        </div>
        <textarea id="editor" data-bind="value: $root.media().content"></textarea>

    </div>
@endsection

<div class="g-hidden">
    <div class="box-modal removal-modal" id="move-modal">
        <div class="layer zero-margin width-auto">
            <div class="layer-head">
                <h3>Изменения успешно сохранены! Перейти на страницу материалов?</h3>
            </div>
            <div class="layer-body">
                <div class="details-row float-buttons">
                    <button class="cancel arcticmodal-close">Отмена</button>
                    <button data-bind="click: $root.move" class="remove arcticmodal-close">Да</button>
                </div>
            </div>
        </div>
    </div>
</div>
