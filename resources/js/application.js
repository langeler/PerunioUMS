(function ($) {
    $('#show_password_field').on('click', function (e) {
        e.preventDefault();
        $(this).fadeOut(200, function () {
            $('#password_field').fadeIn();
        });

    })

})(jQuery);
