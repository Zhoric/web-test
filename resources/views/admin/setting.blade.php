@extends('layouts.manager')
@section('title', 'Администрирование')
@section('javascript')
    <link rel="stylesheet" href="{{ URL::asset('css/datepicker.css')}}"/>
    <script src="{{ URL::asset('js/min/manager-setting.js')}}"></script>
@endsection

@section('content')
<div class="content">
    <div class="layer">
        <div class="layer-head">
            <h1>Администрирование</h1>
        </div>
        <div class="layer-body">
            <div class="details-row">
                <div class="details-column width-98p">
                    <button class="action-button width-auto"
                            data-bind="click: $root.actions.settings.allow,
                            disable: $root.current.editSettingsAllowed">
                        Изменить настройки
                    </button>
                    <button class="action-button width-auro"
                            data-bind="click: $root.actions.results.start,
                            disable: $root.current.editSettingsAllowed">
                        Удалить результаты тестирования
                    </button>
                </div>
                <div class="details-row">
                    <table class="werewolf" data-bind="with: $root.current.settings()">
                        <tr>
                            <td>Максимально возможная оценка за тест</td>
                            <td>
                                <input id="maxMarkValue" type="text" validate
                                       data-bind="textInput: maxMarkValue, validationElement: maxMarkValue,
                                       enable: $root.current.editSettingsAllowed,
                                       event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                            </td>
                            <td>
                                <span title="Максимальная оценка, которая может быть получена за тест.
                                            По умолчанию используется 100-балльная система."
                                      class="fa tagged coloredin-patronus">
                                    &#xf05a;
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Месяц начала первого семестра</td>
                            <td>
                                <input id="firstSemesterMonth" type="text" validate
                                       data-bind="textInput: firstSemesterMonth, validationElement: firstSemesterMonth,
                                       enable: $root.current.editSettingsAllowed,
                                       event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                            </td>
                            <td>
                                <span title="Номер месяца, с которого отсчитывается первый семестр.
                                Используется для расчёта номера текущего семестра и
                                определения актуальных для конкретного студента дисциплин"
                                      class="fa tagged coloredin-patronus">
                                    &#xf05a;
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Месяц начала второго семестра</td>
                            <td>
                                <input id="secondSemesterMonth" type="text" validate
                                       data-bind="textInput: secondSemesterMonth, validationElement: secondSemesterMonth,
                                       enable: $root.current.editSettingsAllowed,
                                       event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                            </td>
                            <td>
                                <span title="Номер месяца, с которого отсчитывается второй семестр.
                                Используется для расчёта номера текущего семестра и
                                определения актуальных для конкретного студента дисциплин"
                                      class="fa tagged coloredin-patronus">
                                    &#xf05a;
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Допуск по времени для ответа на вопрос, сек.</td>
                            <td>
                                <input id="questionEndTolerance" type="text" validate
                                       data-bind="textInput: questionEndTolerance, validationElement: questionEndTolerance,
                                       enable: $root.current.editSettingsAllowed,
                                       event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                            </td>
                            <td>
                                <span title="Допуск времени (в секундах), отведённого для ответа на каждый вопрос теста.
                                Используется для компенсации потерь времени на пересылку данных и
                                ответ сервера во время получения студентом вопроса и отправки им ответа."
                                      class="fa tagged coloredin-patronus">
                                    &#xf05a;
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Допуск по времени для прохождения теста, мин.</td>
                            <td>
                                <input id="testEndTolerance" type="text" validate
                                       data-bind="textInput: testEndTolerance, validationElement: testEndTolerance,
                                       enable: $root.current.editSettingsAllowed,
                                       event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                            </td>
                            <td>
                                <span title="Допуск времени (в секундах), отведённого для прохождения всего теста.
                                Используется для компенсации потерь времени на пересылки данных и
                                ответы сервера во время получения студентом вопросов и отправки им ответов."
                                      class="fa tagged coloredin-patronus">
                                    &#xf05a;
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Время хранения сессий тестирования</td>
                            <td>
                                <input id="testSessionTrackingCacheExpiration" type="text" validate
                                       data-bind="textInput: testSessionTrackingCacheExpiration, validationElement: testSessionTrackingCacheExpiration,
                                       enable: $root.current.editSettingsAllowed,
                                       event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                            </td>
                            <td>
                                <span title="Время хранения записей о сессиях тестирования в Redis Cache.
                                Примеры валидных значений настройки: '+ 1 day', '+ 5 hours',
                                '+1 week 2 days 4 hours 2 seconds' "
                                      class="fa tagged coloredin-patronus">
                                    &#xf05a;
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Время хранения записей мониторинга сессий тестирования</td>
                            <td>
                                <input id="cacheExpiration" type="text" validate
                                       data-bind="textInput: cacheExpiration, validationElement: cacheExpiration,
                                       enable: $root.current.editSettingsAllowed,
                                       event: {focusout: $root.events.focusout, focusin: $root.events.focusin}"/>
                            </td>
                            <td>
                                <span title="Время хранения записей о существовании сессий тестирования в Redis Cache.
                                Записи используются для мониторинга процесса тестирования в реальном времени.
                                Значение должно быть не больше, чем время хранения самой сессии.
                                В противном случае записи о существовании сессии будут храниться дольше самой сессии,
                                что может привести к непредсказуемым последствиям.
                                Примеры валидных значений настройки: '+ 1 day', '+ 5 hours', '+1 week 2 days 4 hours 2 seconds' "
                                      class="fa tagged coloredin-patronus">
                                    &#xf05a;
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- ko if: $root.current.editSettingsAllowed -->
                <div class="details-row float-buttons">
                    <div class="details-column width-99p">
                        <button class="cancel" data-bind="click: $root.actions.settings.default">Установить по умолчанию</button>
                        <button id="bSaveSetting" accept-validation class="approve"
                                title="Проверьте правильность заполнения полей"
                                data-bind="click: $root.actions.settings.save">Сохранить</button>
                    </div>
                </div>
                <!-- /ko -->
            </div>
        </div>
    </div>
</div>
@endsection

<div class="g-hidden">
    <div class="box-modal" id="remove-test-results-modal">
        <div class="layer width-auto zero-margin">
            <div class="layer-head">
                <h3>Удаление результатов тестирования</h3>
            </div>
            <div class="layer-body zero-margin">
                <div class="details-row">
                    <div class="details-column width-98p">
                        <label class="title inline">Удалить все записи до</label>
                        <span class="fa pointer date-ico" data-bind="datePicker: $root.current.resultsDate">&#xf073;</span>
                        <span data-bind="text: $root.current.resultsDate.parseDay()"></span>
                    </div>
                </div>
                <div class="details-row float-buttons minh-40">
                    <button data-bind="click: $root.actions.results.cancel" class="cancel arcticmodal-close">Отмена</button>
                    <button data-bind="click: $root.actions.results.end" class="remove">Удалить результаты</button>
                </div>
            </div>
        </div>
    </div>
</div>