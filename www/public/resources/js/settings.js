/**
 *  Event: save settings
 */
$(document).on('click','#save-settings-btn',function () {
    var settings_params = {};

    $('.slide-panel-container[slide-panel=settings]').find('.settings-param').each(function () {
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
 *  Event: enable / disable motion configuration's advanced edition mode
 */
$(document).on('click','#motion-advanced-edition-mode',function () {
    var cameraId = $(this).attr('camera-id');

    if ($(this).is(':checked')) {
        advancedEditionMode(true);
    } else {
        advancedEditionMode(false);
    }

    /**
     *  Reload edit form
     */
    reloadEditForm(cameraId);
});

/**
 * Ajax: edit global settings
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
            /**
             *  Close settings div and reload page
             */
            closePanel('settings');

            setTimeout(function () {
                location.reload();
            }, 500);
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 *  Ajax: enable / disable motion configuration's advanced edition mode
 */
function advancedEditionMode(status)
{
    $.ajax({
        type: "POST",
        url: "ajax/controller.php",
        data: {
            controller: "settings",
            action: "advancedEditionMode",
            status: status
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}