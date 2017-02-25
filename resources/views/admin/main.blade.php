@extends('shared.layout')
@section('title', 'Институты')
@section('javascript')
    <script src="{{ URL::asset('js/admin/institutes.js')}}"></script>
@endsection

@section('content')
    <div class="content">
        <div class="content">
            <div class="layer">
                <div class="layer-head">
                    <h1>Институты</h1>
                </div>
                <div class="layer-body" data-bind="foreach: $root.initial.institutes">
                    <div class="item" data-bind="click: $root.actions.show.institute, css: {'current': id() === $root.current.institute().id()}">
                        <span data-bind="text: name"></span>
                    </div>
                    <!-- ko if: id() === $root.current.institute().id() -->
                    <div class="details">
                        <div class="details-row" data-bind="foreach: $root.current.profiles">
                            <div class="details-column tile-boredom text-center">
                                <h3 data-bind="text: name, attr: {title: code() + ' ' + fullname()}"></h3>
                                <span data-bind="click: $root.actions.moveTo.group">Перейти к группам</span>
                                <span data-bind="click: $root.actions.show.plans">Учебные планы</span>
                            </div>
                        </div>
                    </div>
                    <!-- /ko -->
                </div>
            </div>
        </div>
    </div>

    <div class="g-hidden">
        <div class="box-modal" id="show-plans-modal">
            <div class="box-modal_close arcticmodal-close"><span class="fa modal-close">&#xf00d;</span></div>
            <div class="layer width-auto">
                <h3>Учебные планы</h3>
                <!-- ko if: $root.current.plan.mode() === state.none && $root.user.role() === role.admin.name -->
                <div class="item text-center" data-bind="click: $root.actions.plan.create">
                    <span class="bold">Добавить новый учебный план</span>
                </div>
                <!-- /ko -->
                <!-- ko if: $root.current.plan.mode() === state.create -->
                <div class="item no-hover">
                    <input type="text" data-bind="value: $root.current.plan.name"/>
                    <button class="approve" data-bind="click: $root.actions.plan.approve">Сохранить</button>
                    <button class="cancel" data-bind="click: $root.actions.plan.cancel">Отмена</button>
                </div>
                <!-- /ko -->
                <!-- ko foreach: $root.current.plans -->
                <div class="item" data-bind="click: $root.actions.moveTo.plan">
                    <span data-bind="text: name"></span>
                </div>
                <!-- /ko -->
            </div>
        </div>
    </div>

    @include('shared.error-modal')
@endsection