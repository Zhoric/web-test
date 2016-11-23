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