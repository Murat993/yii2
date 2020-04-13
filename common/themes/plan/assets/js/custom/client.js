function setQueryStringParameter(name, value) {
    const params = new URLSearchParams(location.search);
    params.set(name, value);
    window.history.replaceState({}, "", decodeURIComponent(location.pathname+'?'+params));
}
console.log(22);
$(document).ready(function () {
    $('.datepicker').datepicker({
        dateFormat : "yy-mm-dd",
        minDate: new Date($('#hiddendelivdate').val()),
        monthNames : ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
        dayNamesMin : ['Вс','Пн','Вт','Ср','Чт','Пт','Сб']
    });
    $('#country').change(function () {
        if ($(this).val() !== ''){
            var data = $.ajax({
                type: 'GET',
                url: "/geo/get-cities?id_country=" + $(this).val()+ "",
                dataType: 'json',
                context: document.body,
                global: false,
                async: false,
                success: function (data) {
                    return data;
                }
            }).responseText;
            var parsed = JSON.parse(data);
            $('#city').empty(); // clear the current elements in select box
            $.each(parsed, function(key, value) {
                $('#city').append($('<option></option>').attr('value', key).text(value));
            });
        }
    });

    $(document).on('click', 'a[data-toggle="tab"]', function () {
        setQueryStringParameter('tab', $(this).data('key'));
    });
});
