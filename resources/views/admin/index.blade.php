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