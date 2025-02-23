/**
 *  Event: enable ONVIF param in camera edit form
 */
$(document).on('click', '.form-param[param-name="onvif-enable"]', function () {
    // If checked, show additional ONVIF fields
    if ($(this).is(':checked')) {
        $('#onvif-fields').show();
    } else {
        $('#onvif-fields').hide();
    }
});
