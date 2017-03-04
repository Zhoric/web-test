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

<div class="g-hidden">
    <div class="box-modal" id="confirm-modal">
        <div class="layer zero-margin width-auto">
            <div class="layer-head">
                <h3 data-bind="$root.confirm.message"></h3>
            </div>
            <div class="layer-body zero-margin">
                <div class="details-row float-buttons">
                    <button class="approve arcticmodal-close" data-bind="$root.confirm.approve">ОК</button>
                    <button class="cancel" data-bind="click: $root.confirm.cancel">Отмена</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="g-hidden">
    <div class="box-modal" id="inform-modal">
        <div class="layer zero-margin width-auto">
            <div class="layer-head">
                <h3 data-bind="$root.inform.message"></h3>
            </div>
            <div class="layer-body zero-margin">
                <div class="details-row float-buttons">
                    <button class="approve arcticmodal-close" data-bind="$root.inform.approve">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>
