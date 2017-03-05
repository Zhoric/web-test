<div class="g-hidden">
    <div class="box-modal" id="errors-modal">
        <div>
            <div>
                <span class="fa">&#xf071;</span>
                <h3>Произошла ошибка</h3>
                <h4 data-bind="text: $root.errors.message"></h4>
            </div>
            <div class="height-30">
                <button class="approve" data-bind="click: $root.errors.accept">OK</button>
            </div>
        </div>
    </div>
</div>

<div class="g-hidden">
    <div class="box-modal" id="confirmation-modal">
        <div class="layer zero-margin width-auto">
            <div class="layer-head">
                <h3 data-bind="text: $root.confirm.message"></h3>
            </div>
            <div class="layer-body zero-margin">
                <!-- ko if: $root.confirm.additionalText() !== null -->
                <div class=" details-row">
                    <p data-bind="text: $root.confirm.additionalText"></p>
                </div>
                <!-- /ko -->
                <!-- ko if: $root.confirm.additionalHtml() !== null -->
                <div class="details-row" data-bind="html: $root.confirm.additionalHtml"></div>
                <!-- /ko -->
                <div class="details-row float-buttons minh-40">
                    <button class="cancel arcticmodal-close" data-bind="click: $root.confirm.cancel">Отмена</button>
                    <button class="approve arcticmodal-close" data-bind="click: $root.confirm.approve">ОК</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="g-hidden">
    <div class="box-modal" id="information-modal">
        <div class="layer zero-margin width-auto">
            <div class="layer-head">
                <h3 data-bind="text: $root.inform.message"></h3>
            </div>
            <div class="layer-body zero-margin">
                <!-- ko if: $root.inform.additionalText() !== null -->
                <div class=" details-row">
                    <p data-bind="text: $root.inform.additionalText"></p>
                </div>
                <!-- /ko -->
                <!-- ko if: $root.inform.additionalHtml() !== null -->
                <div class="details-row" data-bind="html: $root.inform.additionalHtml"></div>
                <!-- /ko -->
                <div class="details-row float-buttons minh-40">
                    <button class="approve arcticmodal-close" data-bind="click: $root.inform.approve">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="g-hidden">
    <div class="box-modal" id="change-user-password-modal">
        <div class="layer width-auto zero-margin">
            <div class="layer-head">
                <h3>Новый пароль</h3>
            </div>
            <div class="layer-body">
                <div class="details-row">
                    <div class="details-column width-98p">
                        <label class="title">Новый пароль</label>
                        <input type="password" id="new-password" validate
                               data-bind="value: $root.user.password.new,
                                   validationElement: $root.user.password.new,
                                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}" />
                    </div>
                    <div class="details-column width-98p">
                        <label class="title">Повторите пароль</label>
                        <input type="password" id="repeat-password" validate
                               data-bind="value: $root.user.password.repeat,
                                   validationElement: $root.user.password.repeat,
                                   event: {focusout: $root.events.focusout, focusin: $root.events.focusin}" />
                    </div>
                </div>
                <div class="details-row float-buttons">
                    <button data-bind="click: $root.user.password.cancel" class="cancel arcticmodal-close">Отмена</button>
                    <button class="approve" accept-validation id="bAppuro"
                            title="Пароли не совпадают"
                            data-bind="click: $root.user.password.approve.bind($root)">Изменить пароль</button>
                </div>
            </div>
        </div>
    </div>
</div>
