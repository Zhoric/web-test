@extends('layouts.student')
@section('title', 'Документ')
@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/site.css')}}" />
@endsection
@section('javascript')
    <script src="{{ URL::asset('js/student/media.js')}}"></script>
@endsection
@section('content')
    <div class="section">
        <div data-bind="html: $root.media().content"></div>
    </div>
@endsection
