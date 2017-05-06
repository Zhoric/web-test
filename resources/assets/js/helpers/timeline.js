ko.bindingHandlers.timeline = {
    init: function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
        var binding = allBindingsAccessor();
        var options = {
            dateFormat: "%d.%m.%Y",
            color: "#5D8CAE",
            width: $(element).width(),
            radius: 7,
            showLabels: true,
            labelFormat: "%Y"
        };
        TimeKnots.draw(element, binding.timeline(), options);
        ko.utils.domNodeDisposal.addDisposeCallback(element, function () {
            $(element).remove();
        });
    },
    update: function (element, valueAccessor) {}
};