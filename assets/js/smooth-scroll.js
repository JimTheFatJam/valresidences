$('.scroll-button').click(function (event) {
    var targetId = $(this).attr('data-target');
    var target = $(targetId);

    if (target.length) {
        event.preventDefault();
        $('html, body').animate({
            scrollTop: target.offset().top
        }, 1000, function () {
            var $target = $(target);
            $target.focus();
            if (!$target.is(":focus")) {
                $target.attr('tabindex', '-1');
                $target.focus();
            }
        });
    }
});
