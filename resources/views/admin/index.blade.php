@extends('shared.layout')
@section('title', 'Index')
@section('javascript')
    <script src="{{ URL::asset('js/admin/profile.js')}}"></script>
@endsection

@section('content')
    <div class="content">
        <div class="profiles" data-bind="template: {name: 'profile-item', foreach: profiles}"></div>
        <div class="groups" data-bind="template: {name: 'group-item', foreach: $root.currentProfile().groups()}"></div>
        <div class="clear"></div>
    </div>
@endsection

<script type="text/html" id="profile-item">
    <div class="profile-item" data-bind="click: $root.changeCurrentProfile, css: {'current-profile': $root.currentProfile().id() === id() }">
        <span data-bind="text: name"></span>
    </div>
</script>

<script type="text/html" id="group-item">
    <div class="group-item">
        <span data-bind="text: id"></span>
    </div>
</script>
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