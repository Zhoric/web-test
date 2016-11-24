@extends('shared.layout')
@section('title', 'Редактор')
@section('javascript')
    <script src="{{ URL::asset('js/admin/editor.js')}}"></script>
@endsection
@section('content')
    <div class="content">
        <div class="section-buttons">
            <button data-bind="click: $root.approve" class="fa success">&#xf00c;</button>
            <button data-bind="click: $root.cancel" class="fa danger">&#xf00d;</button>
        </div>
        <div class="section-name">
            <label>Название раздела</label>
            <input type="text" data-bind="value: $root.section().name">
        </div>
        <textarea id="editor" data-bind="value: $root.section().content"></textarea>

    </div>
    @ckeditor('editor', ['language' => 'ru', 'filebrowserBrowseUrl' => '/elfinder/ckeditor', 'extraPlugins' => 'oembed,video,html5audio'])
@endsection
<div class="g-hidden">
    <div class="box-modal" id="cancel-modal">
        <div>
            <div data-bind="text: $root.text"></div>
        </div>
    </div>
</div>
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
