/**
 * Created by CTRL on 18-07-15.
 */
jQuery(document).ready(function () {
    jQuery('#themeselect').on('change', function () {
        var themeOptions = 'theme_mods_' + jQuery(':selected').val();
        jQuery('.childtheme-options').html('');
        jQuery('#theme-mods-optionname').attr('value', themeOptions);

        var data = {
            'action': 'my_action',
            'theme': themeOptions     // We pass php values differently!
        };
        // We can also pass the url value separately from ajaxurl for front end AJAX implementations
        jQuery.post(ajaxurl, data, function (response) {
            jQuery(response).appendTo('.childtheme-options');
        });
    });
});

jQuery(document).ready(function ($) {

});
