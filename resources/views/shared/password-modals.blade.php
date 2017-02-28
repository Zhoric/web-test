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
<div class="g-hidden">
    <div class="box-modal" id="change-success-modal">
        <div class="layer zero-margin width-auto">
            <div class="layer-head">
                <h3>Пароль успешно изменён</h3>
            </div>
            <div class="layer-body zero-margin">
                <div class="details-row float-buttons">
                    <button class="approve arcticmodal-close">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>



