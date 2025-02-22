/**
 *  Event: save settings
 */
$(document).on('submit','#settings-form',function () {
    event.preventDefault();

    var settings_params = {};

    $('#settings-form').find('.settings-param').each(function () {
        var name = $(this).attr('setting-name');

        if ($(this).is(":checkbox")) {
            if ($(this).is(":checked")) {
                var value = 'true';
            } else {
                var value = 'false';
            }
        /**
         *  If the input is a radio button then we only retrieve its value if it is checked, otherwise we move on to the next parameter
         */
        } else if ($(this).attr('type') == 'radio') {
            if ($(this).is(":checked")) {
                var value = $(this).val();
            } else {
                return; // return is the equivalent of 'continue' for jquery loops .each()
            }
        } else {
            var value = $(this).val();
        }

        settings_params[name] = value;
    });

    ajaxRequest(
        // Controller:
        'settings',
        // Action:
        'edit',
        // Data:
        {
            settings_params_json: JSON.stringify(settings_params)
        },
        // Print success alert:
        true,
        // Print error alert:
        true,
        // Reload containers:
        ['motion/events/list'],
    );

    return false;
});
