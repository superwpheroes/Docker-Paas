jQuery(document).ready(function ($) {
    $('a[data-toggle="modal-mobile-menu"]').on('click', function (e) {
        e.preventDefault();
        var menuDefault = $('#menu-main').html();
        $('#mobile-menu-modal .modal-body .custom-menu-content').html("<ul>" + menuDefault + "</ul>");
        $('.fusion-mobile-nav-holder, .fusion-mobile-sticky-nav-holder, #menu-main').hide();
        $('#mobile-menu-modal').show();
        setTimeout(function () {
            $('#mobile-menu-modal .modal-content').addClass('fade-in');
        }, 100);
    });

    $('.fullscreen .close').on('click', function (e) {
        e.preventDefault();
        $('.fusion-mobile-nav-holder, .fusion-mobile-sticky-nav-holder').hide();
        $('#mobile-menu-modal .modal-content').removeClass('fade-in');
        setTimeout(function () {
            $('#mobile-menu-modal').hide();
            $('#mobile-menu-modal .modal-body .custom-menu-content').html("");
        }, 100);
    });
});


