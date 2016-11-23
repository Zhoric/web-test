@extends('shared.layout')
@section('title', 'Институты')
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
                    <span data-bind="text: name"></span></br>
                    <button data-bind="click: $root.showPlans" class="tyle-btn">Учебные планы</button>
                    <button data-bind="click: $root.moveToGroup" class="tyle-btn tyle-btn-negative">Перейти к группам</button>
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

        <div class="g-hidden">
            <div class="box-modal" id="plans-modal">
                <div class="box-modal_close arcticmodal-close">закрыть</div>
                <div class="institutes">
                    <h3>Учебные планы</h3> <h3 data-bind=""></h3>
                    <div class="institute">+</div>
                    <!-- ko foreach: $root.currentPlans -->
                    <div class="institute" data-bind="click: $root.moveToPlan">
                        <span data-bind="text: name"></span>
                    </div>
                    <!-- /ko -->
                </div>
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