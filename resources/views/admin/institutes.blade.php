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