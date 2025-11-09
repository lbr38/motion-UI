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
        'settings/edit',
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

/**
 *  Event: enable or disable debug mode
 */
$(document).on('click','#debug-mode-btn',function () {
    var enable = false;

    if ($(this).is(':checked')) {
        var enable = true;
    }

    ajaxRequest(
        // Controller:
        'settings/debug-mode',
        // Action:
        'enable',
        // Data:
        {
            enable: enable
        },
        // Print success alert:
        true,
        // Print error alert:
        true
    ).then(function () {
        mycontainer.reload('header/debug-mode');
    });
});

/**
 *  Event: view motion process log
 */
$(document).on('click', '#motion-log-btn', function () {
    var log = $('#motion-log-select').val();

    mymodal.loading();

    ajaxRequest(
        // Controller:
        'motion',
        // Action:
        'get-log',
        // Data:
        {
            log: log
        },
        // Print success alert:
        false,
        // Print error alert:
        true
    ).then(function () {
        mymodal.print(jsonValue.message, 'MOTION LOG');
    });
});

/**
 *  Event: view go2rtc process log
 */
$(document).on('click', '#go2rtc-log-btn', function () {
    var log = $('#go2rtc-log-select').val();

    mymodal.loading();

    ajaxRequest(
        // Controller:
        'go2rtc',
        // Action:
        'get-log',
        // Data:
        {
            log: log
        },
        // Print success alert:
        false,
        // Print error alert:
        true
    ).then(function () {
        mymodal.print(jsonValue.message, 'GO2RTC LOG');
    });
});
