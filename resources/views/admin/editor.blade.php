@extends('shared.layout')
@section('title', 'Редактор')
@section('javascript')
@endsection
@section('content')
    <div class="content">
        <textarea id="editor-field"></textarea>
    </div>
    @ckeditor('editor-field', ['language' => 'ru', 'filebrowserBrowseUrl' => '/elfinder/ckeditor'])
@endsection
