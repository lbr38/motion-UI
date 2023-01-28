/**
 *  Event: print settings div
 */
$(document).on('click','#print-settings-btn',function () {
    openSlide('#settings-div');
});

/**
 *  Event: hide settings div
 */
$(document).on('click','#hide-settings-btn',function () {
    closeSlide('#settings-div');
});

/**
 *  Event: save settings
 */
$(document).on('click','#save-settings-btn',function () {
    var settings_params = {};

    $('#settings-div').find('.settings-param').each(function () {
        var name = $(this).attr('setting-name');
        if ($(this).is(":checked")) {
            var value = 'true';
        } else {
            var value = 'false';
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
    if ($(this).is(':checked')) {
        advancedEditionMode(true);

        /**
         *  Print all advanced parameters fields
         */
        $('.advanced-param').css('display', 'table-row');
        $('#advanced-edition-mode-warning').show();
    } else {
        advancedEditionMode(false);

        /**
         * Hide all advanced parameters fields
         */
        $('.advanced-param').hide();
        $('#advanced-edition-mode-warning').hide();
    }
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
            closeSlide('#settings-div');

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