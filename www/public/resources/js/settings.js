/**
 *  Event: print settings div
 */
$(document).on('click','#print-settings-btn',function () {
    $('#settings-container').show('100');
});

/**
 *  Event: hide settings div
 */
$(document).on('click','#hide-settings-btn',function () {
    $('#settings-container').hide('100');
});

/**
 *  Event: save settings
 */
$(document).on('click','#save-settings-btn',function () {
    var settings_params = {};

    $('#settings').find('.settings-param').each(function () {
        var name = $(this).attr('setting-name');
        if ($(this).is(":checked")) {
            var value = 'yes';
        } else {
            var value = 'no';
        }

        settings_params[name] = value;
    });

    settings_params_json  = JSON.stringify(settings_params);

    editSetting(settings_params_json);
});

/**
 * Ajax: edit global settings
 * @param {*} settings_params_json
 */
function editSetting(settings_params_json)
{
    $.ajax({
        type: "POST",
        url: "controllers/settings/ajax.php",
        data: {
            action: "edit",
            settings_params_json: settings_params_json
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');

            /**
             *  Close settings div and reload page
             */
            $.when($('#settings-container').hide('100')).done(function () {
                location.reload();
            });
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}