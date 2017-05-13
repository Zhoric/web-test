ko.bindingHandlers.timeline = {
    init: function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
        var binding = allBindingsAccessor();
        var timeline = binding.timeline();
        var options = {
            dateFormat: "%d.%m.%Y",
            color: "#5D8CAE",
            width: $(element).width(),
            radius: 7,
            showLabels: true,
            labelFormat: "%Y"
        };

        TimeKnots.draw(element, timeline, options);
        ko.utils.domNodeDisposal.addDisposeCallback(element, function () {
            $(element).remove();
        });
        $('.timeline-event').click(function(e){
            var elem = e.target;
            var series = $('.timeline-event');
            var i = $.inArray($(elem).toArray()[0], series.toArray());
            if (i != -1 && timeline[i].hasOwnProperty('id')){
                window.location.href = "/admin/result/" + timeline[i].id;
            }
        });
    },
    update: function (element, valueAccessor) {}
};