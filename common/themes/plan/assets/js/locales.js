$( document ).ready(function() {
    $('.change-locale').click(function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        $.ajax({
            type: 'GET',
            url: href,
            success: function (data) {
                location.reload();
            }
        });
    });
});