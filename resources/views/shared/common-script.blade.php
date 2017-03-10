<div class="loading">
    <img src="{{ URL::asset('images/loading.gif')}}" />
</div>
<script>
    var loading = $(".loading");
    $(document).ajaxStart(function () {
        loading.show();
    });
    $(document).ajaxStop(function () {
        loading.hide();
    });

    ko.validation.init({
        messagesOnModified: true,
        insertMessages:false,
        errorsAsTitle: true
    });
    ko.validation.locale('ru-RU');
</script>