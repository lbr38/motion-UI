/**
 *  Event: save settings
 */
$(document).on('click','#save-settings-btn',function () {
    var settings_params = {};

    $('.slide-panel-container[slide-panel="settings"]').find('.settings-param').each(function () {
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

    settings_params_json  = JSON.stringify(settings_params);

    editSetting(settings_params_json);
});

/**
 * Ajax: edit settings
 * @param {*} settings_params_json
 */
function editSetting(settings_params_json)
{
    $.ajax({
        type: "POST",
        url: "ajax/controller.php",
        data: {
            controller: "settings",
            action: "edit",
            settings_params_json: settings_params_json
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadContainer('motion/events/list');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}
