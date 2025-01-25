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
