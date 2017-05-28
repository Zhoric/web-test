@extends('layouts.student')
@section('title', '')
@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/site.css')}}" />
@endsection
@section('javascript')
	<script src="{{ URL::asset('js/min/student-media.js')}}"></script>
@endsection
@section('content')
    <div class="section">
        <div class="section-data" data-bind="html: $root.media().content, afterHtmlRender: $root.goToAnchor"></div>
    </div>
@endsection

