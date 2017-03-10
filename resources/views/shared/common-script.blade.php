<div class="LoadingImage">
    <img src="{{ URL::asset('images/custom-spinner.gif')}}" />
</div>
<script>
    var loading = $(".LoadingImage");
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