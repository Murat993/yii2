$( document ).ready(function() {
    // if ($('#success-flash').text() !== '') {
    //     console.log($('#success-flash'));
    //         new PNotify({
    //             title: 'hello',
    //             text: $('#success-flash').text()
    //         });
    // }
    // if (($('#error-flash').text() !== '')){
    //     console.log($('#error-flash').text());
    //     new PNotify({
    //         title: 'hello',
    //         text: $('#error-flash').text()
    //     });
    // }



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