window.imghoveractivate = function (windowWidth) {
    jQuery("a.imghover").each(function () {
        if (windowWidth >= 426) {
            jQuery(this).on("hover, mouseover",function (e) {
                e.preventDefault();
                blockwidth = jQuery(this).width() / 2, imgwidth = jQuery(this).children("img").width() / 2, width = imgwidth - blockwidth + 10, jQuery(this).children("img").css("left", -width), jQuery(this).children("svg").css("left", -width), blockheight = jQuery(this).height() / 2, imgheight = jQuery(this).children("img").height() / 2, height = imgheight - blockheight, jQuery(this).children("img").css("top", -height);
                jQuery(this).children("span").css('z-index', '100');
                jQuery(this).children("img").css('z-index', '99');
            });
            jQuery(this).on("mouseout, mouseleave", function (e) {
                 e.preventDefault();
                jQuery(this).children("span").css('z-index', '0');
                jQuery(this).children("img").css('z-index', '-1');
            });
        } else {
            jQuery(this).children("img").css('top', '0').css('left', '0');
        }
    });
};
jQuery(document).ready(function () {
    var windowWidth = jQuery(window).width();
    if (jQuery(".hundred-percent-fullwidth").find(".imghover").length > 0) {
        jQuery(".hundred-percent-fullwidth").css('overflow', 'visible');
    }
    imghoveractivate(windowWidth);
});

jQuery(window).resize(function () {
    var windowWidth = jQuery(window).width();
    imghoveractivate(windowWidth);
});


