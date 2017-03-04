@extends('layouts.student')
@section('title', 'Раздел')
@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/site.css')}}" />
@endsection
@section('javascript')
    <script src="{{ URL::asset('js/student/section.js')}}"></script>
@endsection
@section('content')
    <div class="section">
        <div data-bind="html: $root.section().content"></div>
    </div>
    @include('shared.error-modal')
@endsection
