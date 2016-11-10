@extends('shared.layout')
@section('title', 'Секция')
@section('javascript')
    <script src="{{ URL::asset('js/admin/section.js')}}"></script>
@endsection
@section('content')
    <div class="content section">
        <div data-bind="text: $root.section().name"></div>
        <div data-bind="html: $root.section().content"></div>
    </div>
@endsection
