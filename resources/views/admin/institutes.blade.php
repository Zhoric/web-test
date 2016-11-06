@extends('shared.layout')
@section('title', 'Институты')
@section('javascript')
    <script src="{{ URL::asset('js/institutes-coffee.js')}}"></script>
@endsection

@section('content')

    <div class="content">
        <div class="institutes">
        </div>
    </div>

@endsection