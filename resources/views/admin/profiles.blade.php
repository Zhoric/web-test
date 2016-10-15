@extends('shared.layout')
@section('title', 'Index')
@section('javascript')
    <script src="{{ URL::asset('js/admin/institutes.js')}}"></script>
@endsection

@section('content')
    <div class="content">

        <div class="institutes">
            <!-- ko foreach: institutes-->
            <div class="institute" data-bind="click: $root.getProfiles, css: {'institute-current': $root.currentInstitute() === id() }">
                <span data-bind="text: name"></span>
            </div>
            <!-- ko if: $root.currentInstitute() === id() -->
            <div class="profiles">
                <!-- ko foreach: $root.currentProfiles-->
                <div class="profile">
                    <span data-bind="text: name"></span>
                </div>
                <!-- /ko -->
                <div class="profile" data-bind="">
                    <span>+</span>
                </div>
                <div class="clear"></div>
            </div>
            <!-- /ko -->
            <!-- /ko -->
            <div class="institute" data-bind="">
                <span>+</span>
            </div>
        </div>

    </div>
@endsection