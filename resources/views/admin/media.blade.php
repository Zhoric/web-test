@extends('layouts.manager')
@section('title', '')
@section('javascript')
    <script src="{{ URL::asset('js/admin/media.js')}}"></script>
@endsection
@section('content')
    <div class="section">
        <div class="section-data" data-bind="html: $root.media().content, afterHtmlRender: $root.goToAnchor"></div>
    </div>
@endsection
