/**
 *  Event: Start motion capture
 */
$(document).on('click','#start-motion-btn',function () {
    startStopMotion('start');
});

/**
 *  Event: Stop motion capture
 */
$(document).on('click','#stop-motion-btn',function () {
    startStopMotion('stop');
});

/**
 *  Event: enable autostart
 */
$(document).on('click','#enable-autostart-btn',function () {
    enableAutostart('enabled');
});

/**
 *  Event: disable autostart
 */
$(document).on('click','#disable-autostart-btn',function () {
    enableAutostart('disabled');
});

/**
 *  Event: configure autostart
 */
$(document).on('click','#configure-autostart-btn',function () {
    $("#autostart-div").slideToggle('100');
});

/**
 *  Event: enable / disable autostart on device presence
 */
$(document).on('click','#enable-device-presence-btn',function () {
    if ($(this).is(':checked')) {
        enableDevicePresence('enabled');
    } else {
        enableDevicePresence('disabled');
    }
});

/**
 *  Event: Enable alerts
 */
$(document).on('click','#enable-alert-btn',function () {
    enableAlert('enabled');
});

/**
 *  Event: Disable alerts
 */
$(document).on('click','#disable-alert-btn',function () {
    enableAlert('disabled');
});

/**
 *  Event: configure alerts
 */
$(document).on('click','#configure-alerts-btn',function () {
    $("#alert-div").slideToggle('100');
});

/**
 *  Event: show how to configure alerts div
 */
$(document).on('click','#how-to-alert-btn',function () {
    $("#how-to-alert-container").slideToggle('100');
});

/**
 *  Event: duplicate motion configuration file
 */
$(document).on('click','.duplicate-motion-conf-btn',function () {
    var filename = $(this).attr('filename');

    duplicateConf(filename);
});

/**
 *  Event: delete motion configuration file
 */
$(document).on('click','.delete-motion-conf-btn',function () {
    var filename = $(this).attr('filename');

    deleteConfirm('Are you sure you want to delete ' + filename + '?', function () {
        deleteConf(filename)
    });
});

/**
 *  Event: rename motion configuration file
 */
$(document).on('keypress','.rename-motion-conf-input',function () {
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if (keycode == '13') {
        var filename = $(this).attr('filename');
        var newName = $(this).val();

        renameConf(filename, newName);
    }
    event.stopPropagation();
});

/**
 *  Event: Show/hide motion configuration file
 */
$(document).on('click','.show-motion-conf-btn',function () {
    var filename = $(this).attr('filename');

    $("div[filename='" + filename + "']").slideToggle('100');
});

/**
 *  Event: Configure motion autostart
 */
$(document).on('submit','#autostart-conf-form',function () {
    event.preventDefault();

    var mondayStart = $(this).find('input[type=time][name="monday-start"]').val();
    var mondayEnd = $(this).find('input[type=time][name="monday-end"]').val();
    var tuesdayStart = $(this).find('input[type=time][name="tuesday-start"]').val();
    var tuesdayEnd = $(this).find('input[type=time][name="tuesday-end"]').val();
    var wednesdayStart = $(this).find('input[type=time][name="wednesday-start"]').val();
    var wednesdayEnd = $(this).find('input[type=time][name="wednesday-end"]').val();
    var thursdayStart = $(this).find('input[type=time][name="thursday-start"]').val();
    var thursdayEnd = $(this).find('input[type=time][name="thursday-end"]').val();
    var fridayStart = $(this).find('input[type=time][name="friday-start"]').val();
    var fridayEnd = $(this).find('input[type=time][name="friday-end"]').val();
    var saturdayStart = $(this).find('input[type=time][name="saturday-start"]').val();
    var saturdayEnd = $(this).find('input[type=time][name="saturday-end"]').val();
    var sundayStart = $(this).find('input[type=time][name="sunday-start"]').val();
    var sundayEnd = $(this).find('input[type=time][name="sunday-end"]').val();

    configureAutostart(mondayStart, mondayEnd, tuesdayStart, tuesdayEnd, wednesdayStart, wednesdayEnd, thursdayStart, thursdayEnd, fridayStart, fridayEnd, saturdayStart, saturdayEnd, sundayStart, sundayEnd);

    return false;
});

/**
 *  Add a new device
 */
$(document).on('submit','#device-presence-form',function () {
    event.preventDefault();

    var name = $(this).find('input[type=text][name="device-name"]').val();
    var ip = $(this).find('input[type=text][name="device-ip"]').val();

    addDevice(name, ip);

    return false;
});

/**
 *  Event: Remove known device
 */
$(document).on('click','.remove-device-btn',function () {
    var id = $(this).attr('device-id');
    removeDevice(id);
});

/**
 *  Event: Configure alerts
 */
$(document).on('submit','#alert-conf-form',function () {
    event.preventDefault();

    var mondayStart = $(this).find('input[type=time][name="monday-start"]').val();
    var mondayEnd = $(this).find('input[type=time][name="monday-end"]').val();
    var tuesdayStart = $(this).find('input[type=time][name="tuesday-start"]').val();
    var tuesdayEnd = $(this).find('input[type=time][name="tuesday-end"]').val();
    var wednesdayStart = $(this).find('input[type=time][name="wednesday-start"]').val();
    var wednesdayEnd = $(this).find('input[type=time][name="wednesday-end"]').val();
    var thursdayStart = $(this).find('input[type=time][name="thursday-start"]').val();
    var thursdayEnd = $(this).find('input[type=time][name="thursday-end"]').val();
    var fridayStart = $(this).find('input[type=time][name="friday-start"]').val();
    var fridayEnd = $(this).find('input[type=time][name="friday-end"]').val();
    var saturdayStart = $(this).find('input[type=time][name="saturday-start"]').val();
    var saturdayEnd = $(this).find('input[type=time][name="saturday-end"]').val();
    var sundayStart = $(this).find('input[type=time][name="sunday-start"]').val();
    var sundayEnd = $(this).find('input[type=time][name="sunday-end"]').val();
    var mailRecipient = $(this).find('input[type=email][name="mail-recipient"]').val();
    var muttConfig = $(this).find('input[type=text][name="mutt-config"]').val();

    configureAlert(mondayStart, mondayEnd, tuesdayStart, tuesdayEnd, wednesdayStart, wednesdayEnd, thursdayStart, thursdayEnd, fridayStart, fridayEnd, saturdayStart, saturdayEnd, sundayStart, sundayEnd, mailRecipient, muttConfig);

    return false;
});

/**
 *  Event: Configure motion
 */
$(document).on('submit','.motion-configuration-form',function () {
    event.preventDefault();

    var options_array = [];

    /**
     *  Get the name of the configuration file
     */
    var filename = $(this).attr('filename');

    /**
     *  Get all the parameters and their value in the form
     */

    /**
     *  D'abord on compte le nombre d'input de class 'sourceConfForm-optionName' dans ce formulaire
     */
    var countTotal = $(this).find('input[name=option-name]').length

    /**
     *  Chaque paramètre de configuration et leur valeur associée possèdent un id
     *  On récupère le nom du paramètre et sa valeur associée ayant le même id et on push le tout dans un tableau
     */
    if (countTotal > 0) {
        for (let i = 0; i < countTotal; i++) {
            /**
             *  Get parameter status (slider checked or not)
             */
            if ($(this).find('input[name=option-status][option-id=' + i + ']').is(':checked')) {
                var option_status = 'enabled';
            } else {
                var option_status = '';
            }
            /**
             *  Get parameter name and its value
             */
            var option_name = $(this).find('input[name=option-name][option-id=' + i + ']').val();
            var option_value = $(this).find('input[name=option-value][option-id=' + i + ']').val()

            /**
             *  Push all to options_array
             */
            options_array.push(
                {
                    status: option_status,
                    name: option_name,
                    value: option_value
                }
            );
        }
    }

    configure(filename, options_array);

    return false;
});

/**
 * Ajax: start or stop motion capture
 * @param {*} status
 */
function startStopMotion(status)
{
    $.ajax({
        type: "POST",
        url: "controllers/motion/ajax.php",
        data: {
            action: "startStopMotion",
            status: status
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert('Request taken into account, please wait until refresh in 5sec', 'success');
            /**
             *  Reload div after 5sec
             */
            setTimeout(function () {
                reloadContentById('motion-start-container');
            }, 5000);
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax: enable autostart
 * @param {*} status
 */
function enableAutostart(status)
{
    $.ajax({
        type: "POST",
        url: "controllers/motion/ajax.php",
        data: {
            action: "enableAutostart",
            status: status
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            reloadContentById('motion-autostart-container');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax: configure motion autostart
 * @param {*} mondayStart
 * @param {*} mondayEnd
 * @param {*} tuesdayStart
 * @param {*} tuesdayEnd
 * @param {*} wednesdayStart
 * @param {*} wednesdayEnd
 * @param {*} thursdayStart
 * @param {*} thursdayEnd
 * @param {*} fridayStart
 * @param {*} fridayEnd
 * @param {*} saturdayStart
 * @param {*} saturdayEnd
 * @param {*} sundayStart
 * @param {*} sundayEnd
 */
function configureAutostart(mondayStart, mondayEnd, tuesdayStart, tuesdayEnd, wednesdayStart, wednesdayEnd, thursdayStart, thursdayEnd, fridayStart, fridayEnd, saturdayStart, saturdayEnd, sundayStart, sundayEnd)
{
    $.ajax({
        type: "POST",
        url: "controllers/motion/ajax.php",
        data: {
            action: "configureAutostart",
            mondayStart: mondayStart,
            mondayEnd: mondayEnd,
            tuesdayStart: tuesdayStart,
            tuesdayEnd: tuesdayEnd,
            wednesdayStart: wednesdayStart,
            wednesdayEnd: wednesdayEnd,
            thursdayStart: thursdayStart,
            thursdayEnd: thursdayEnd,
            fridayStart: fridayStart,
            fridayEnd: fridayEnd,
            saturdayStart: saturdayStart,
            saturdayEnd: saturdayEnd,
            sundayStart: sundayStart,
            sundayEnd: sundayEnd
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax: enable / disable autostart on device presence
 * @param {*} status
 */
function enableDevicePresence(status)
{
    $.ajax({
        type: "POST",
        url: "controllers/motion/ajax.php",
        data: {
            action: "enableDevicePresence",
            status: status
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadContentById('autostart-div');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax: add a new device
 * @param {*} name
 * @param {*} ip
 */
function addDevice(name, ip)
{
    $.ajax({
        type: "POST",
        url: "controllers/motion/ajax.php",
        data: {
            action: "addDevice",
            name: name,
            ip: ip
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadContentById('autostart-div');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax: remove known device
 * @param {*} id
 */
function removeDevice(id)
{
    $.ajax({
        type: "POST",
        url: "controllers/motion/ajax.php",
        data: {
            action: "removeDevice",
            id: id
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadContentById('autostart-div');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax: enable alerts
 * @param {*} status
 */
function enableAlert(status)
{
    $.ajax({
        type: "POST",
        url: "controllers/motion/ajax.php",
        data: {
            action: "enableAlert",
            status: status
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            reloadContentById('motion-alert-container');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax: configure alerts
 * @param {*} mondayStart
 * @param {*} mondayEnd
 * @param {*} tuesdayStart
 * @param {*} tuesdayEnd
 * @param {*} wednesdayStart
 * @param {*} wednesdayEnd
 * @param {*} thursdayStart
 * @param {*} thursdayEnd
 * @param {*} fridayStart
 * @param {*} fridayEnd
 * @param {*} saturdayStart
 * @param {*} saturdayEnd
 * @param {*} sundayStart
 * @param {*} sundayEnd
 */
function configureAlert(mondayStart, mondayEnd, tuesdayStart, tuesdayEnd, wednesdayStart, wednesdayEnd, thursdayStart, thursdayEnd, fridayStart, fridayEnd, saturdayStart, saturdayEnd, sundayStart, sundayEnd, mailRecipient, muttConfig)
{
    $.ajax({
        type: "POST",
        url: "controllers/motion/ajax.php",
        data: {
            action: "configureAlert",
            mondayStart: mondayStart,
            mondayEnd: mondayEnd,
            tuesdayStart: tuesdayStart,
            tuesdayEnd: tuesdayEnd,
            wednesdayStart: wednesdayStart,
            wednesdayEnd: wednesdayEnd,
            thursdayStart: thursdayStart,
            thursdayEnd: thursdayEnd,
            fridayStart: fridayStart,
            fridayEnd: fridayEnd,
            saturdayStart: saturdayStart,
            saturdayEnd: saturdayEnd,
            sundayStart: sundayStart,
            sundayEnd: sundayEnd,
            mailRecipient: mailRecipient,
            muttConfig: muttConfig
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadContentById('alert-div');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax: configure motion config file
 * @param {*} filename
 * @param {*} options_array
 */
function configure(filename, options_array)
{
    $.ajax({
        type: "POST",
        url: "controllers/motion/ajax.php",
        data: {
            action: "configureMotion",
            filename: filename,
            options_array: options_array
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadContentById('motion-configuration-div');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax: duplicate motion config file
 * @param {*} filename
 */
function duplicateConf(filename)
{
    $.ajax({
        type: "POST",
        url: "controllers/motion/ajax.php",
        data: {
            action: "duplicateConf",
            filename: filename
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadContentById('motion-configuration-div');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax: delete motion config file
 * @param {*} filename
 */
function deleteConf(filename)
{
    $.ajax({
        type: "POST",
        url: "controllers/motion/ajax.php",
        data: {
            action: "deleteConf",
            filename: filename
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadContentById('motion-configuration-div');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax: rename motion config file
 * @param {*} filename
 * @param {*} newName
 */
function renameConf(filename, newName)
{
    $.ajax({
        type: "POST",
        url: "controllers/motion/ajax.php",
        data: {
            action: "renameConf",
            filename: filename,
            newName: newName
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadContentById('motion-configuration-div');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}