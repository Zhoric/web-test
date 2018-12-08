@extends('layouts.manager')
@section('title', 'Успеваемость')
@section('javascript')
    <script src="{{ URL::asset('js/min/manager-performance.js')}}"></script>
@endsection

@section('content')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
        crossorigin="anonymous">
    <style>
    body {
        background-color: #F2F1EF;
    }

        table {
            border-collapse: collapse;
            border: 1px solid black;
            margin: 15px auto;
            width: 100%;
            overflow: scroll;
            border-spacing: 0;
        }

        th {
            color: white;
            background-color: #5D8CAE;
        }

        td,
        th {
            border: 1px solid #BFBFBF;
            padding: 5px 10px;
            text-align: center;
        }

        .petails {
            margin-top: -2px;
            height: auto;
            border-top: 0.1px solid #BFBFBF;
            border-bottom: 1px solid #BFBFBF;
            width: 100%;
        }

        .cow:after {
            clear:both
        }

        .cow {
            margin-bottom:15px;
            margin-right:-15px;
            margin-left:-15px
        }

        .head-text {
            font-size: 2em;
            font-weight: bold;
            padding-left: 15px;
            padding-bottom: 10px;
            color: #BFBFBF;
        }

        .filter-input {
            margin: 0;
            padding: 0;
            border: 0px;
            border-style: solid;  
            max-width: 70px; 
            max-height: 20px;
            text-align: center;
        }

        .grey-title {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 3px;
            font-weight: lighter;
        }

        .table-wrapper {
            padding-top: 45px;
            overflow-x: scroll;
            overflow-y: visible;
            padding-bottom: 15px;
            width: 100%;
            float: left;
            position: relative;
            min-height: 1px;
            padding-right: 15px;
            padding-left: 15px;
        }

        .performance-wrapper {
            background-color: #FFFFFF;
            margin-top: 45px;
            width: 90%;
            padding-top: 15px;
            padding-right: 30px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }

        .first-select {
            width: 33.33333333%;
            float: left;
            position: relative;
            min-height: 1px;
            padding-right: 15px;
            padding-left: 15px;

        }

        .second-select {
            width: 25%;
            float: left;
            position: relative;
            min-height: 1px;
            padding-right: 15px;
            padding-left: 15px;
        }

        .inside-select {
            width: 100%;
            float: left;
            position: relative;
            min-height: 1px;


            font-size: inherit;
            line-height: inherit;
            text-transform: none;
            margin: 0;
            font: inherit;
            color: inherit;

            text-indent: 2px;
            border-radius: 2px;
            font-family: "Roboto Regular"!important;

            height: 30px;
            border: 1px solid #BFBFBF;
            background: #fff;

            -webkit-appearance: menulist;
            align-items: center;
            white-space: pre;
            -webkit-rtl-ordering: logical;
            cursor: default;

            -webkit-writing-mode: horizontal-tb !important;

        }
    </style>

    <div class="performance-wrapper">
        <h1 class="head-text">Учет успеваемости студентов</h1>
        <hr class="petails">
        <div class="row">
            <div class="first-select">
                <label class="grey-title">Название дисциплины <span class="required">*</span></label>
                <select class="inside-select">
                    <option selected disabled>Выберите дисциплину</option>
                    <option>Пункт 1</option>
                    <option>Пункт 2</option>
                </select>
            </div>
            <div class="second-select">
                <label class="grey-title">Название группы <span class="required">*</span></label>
                <select class="inside-select">
                    <option selected disabled>Выберите группу</option>
                    <option>Пункт 1</option>
                    <option>Пункт 2</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="table-wrapper">
                <table>
                    <tr>
                        <th>СТУДЕНТЫ</th>
                        <th>ЛЕК 1</th>
                        <th>ЛЕК 2</th>
                        <th>ЛЕК 3</th>
                        <th>ЛЕК 1</th>
                        <th>ПРЗ 1</th>
                        <th>ПРЗ 2</th>
                        <th>ПРЗ 3</th>
                        <th>ПРЗ 4</th>
                        <th>ЛАБ 1</th>
                        <th>ЛАБ 2</th>
                        <th>ЛАБ 3</th>
                        <th>ЛАБ 4</th>                        
                    </tr>
                    <tr>
                        <td>Студент 1</td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                    </tr>
                    <tr>
                        <td>Студент 2</td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                    </tr>
                    <tr>
                        <td>Студент 3</td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                        <td><input type="text" value="" class="filter-input"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection


