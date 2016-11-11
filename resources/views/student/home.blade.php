@extends('student.layout')
@section('title', 'Главная')
@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/site.css')}}" />
@endsection
@section('javascript')
    <script src="{{ URL::asset('js/student/home.js')}}"></script>
@endsection

@section('content')
    <div class="content">
        <!-- ko foreach: $root.current.rows -->
        <div class="row" data-bind="foreach: disciplines">
            <div class="discipline" data-bind="click: $root.actions.disciplineDetails.bind($data, $parent), css: {'current': $root.current.disciplineId() === id()},">
                <span data-bind="text: abbreviation"></span>
            </div>
        </div>
            <!-- ko if: $root.current.rowId() === rowId() && $root.mode() === 'details' -->
            <div class="details">
                <!-- ko if: $root.current.tests().length -->
                <table>
                    <tbody data-bind="foreach: $root.current.tests">
                        <tr data-bind="template: {name: 'test-template', data: test}"></tr>
                    </tbody>
                </table>
                <!-- /ko -->
                <!-- ko if: !$root.current.tests().length -->
                    <h3>По данной дисциплине пока нет тестов</h3>
                <!-- /ko -->
            </div>
            <!-- /ko -->
        <!-- /ko -->
    </div>
@endsection

<script type="text/html" id="test-template">
    <td data-bind="text: subject"></td>
    <td><button data-bind="click: $root.actions.startTest, enabled: $parent.attemptsLeft(), css: {'attempts-mid': ($parent.attemptsLeft() > $parent.attemptsMade()) && $parent.attemptsMade(), 'attempts-all': !$parent.attemptsMade(), 'attempts-little': $parent.attemptsLeft() == 1}">Пройти тест</button></td>
</script>