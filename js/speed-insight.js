$.noConflict(); 
jQuery(document).ready(function ($) {

    var data = {
        action: 'si-get-stats',
        url: MyAjax.urlToCheck,
    };

    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    $.post(MyAjax.ajaxurl, data, function (response) {
//        console.log(response);
        $('#si-desktop').text(response['desktop-score']);
        $('#si-desktop').parents('.si-stats').addClass(response['desktop-class']);
        $('#si-mobile').text(response['mobile-score']);
        $('#si-mobile').parents('.si-stats').addClass(response['mobille-class']);
        $('#si-mobile-usability').text(response['usability-score']);
        $('#si-mobile-usability').parents('.si-stats').addClass(response['usability-class']);
    });
}); 