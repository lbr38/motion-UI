/**
 *  Event: Enable alerts
 */
$(document).on('click','#enable-alert-btn',function () {
    enableAlert('yes');
});

/**
 *  Event: Disable alerts
 */
$(document).on('click','#disable-alert-btn',function () {
    enableAlert('no');
});

/**
 *  Event: show how to configure alerts div
 */
$(document).on('click','#how-to-alert-btn',function () {
    $("#how-to-alert-container").slideToggle('100');
});

/**
 *  Send alert configuration
 */
$(document).on('submit','#alert-conf-form',function () {
    event.preventDefault();

    var mondayStart = $('input[type=time][name="monday_start"]').val();
    var mondayEnd = $('input[type=time][name="monday_end"]').val();
    var tuesdayStart = $('input[type=time][name="tuesday_start"]').val();
    var tuesdayEnd = $('input[type=time][name="tuesday_end"]').val();
    var wednesdayStart = $('input[type=time][name="wednesday_start"]').val();
    var wednesdayEnd = $('input[type=time][name="wednesday_end"]').val();
    var thursdayStart = $('input[type=time][name="thursday_start"]').val();
    var thursdayEnd = $('input[type=time][name="thursday_end"]').val();
    var fridayStart = $('input[type=time][name="friday_start"]').val();
    var fridayEnd = $('input[type=time][name="friday_end"]').val();
    var saturdayStart = $('input[type=time][name="saturday_start"]').val();
    var saturdayEnd = $('input[type=time][name="saturday_end"]').val();
    var sundayStart = $('input[type=time][name="sunday_start"]').val();
    var sundayEnd = $('input[type=time][name="sunday_end"]').val();

    configureAlert(mondayStart, mondayEnd, tuesdayStart, tuesdayEnd, wednesdayStart, wednesdayEnd, thursdayStart, thursdayEnd, fridayStart, fridayEnd, saturdayStart, saturdayEnd, sundayStart, sundayEnd);

    return false;
});

/**
 * Ajax : enable alerts
 * @param {*} status
 */
function enableAlert(status)
{
    $.ajax({
        type: "POST",
        url: "controllers/ajax.php",
        data: {
            action: "enableAlert",
            status: status
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            reloadContentById('motion-alert-div');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax : configure alerts
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
function configureAlert(mondayStart, mondayEnd, tuesdayStart, tuesdayEnd, wednesdayStart, wednesdayEnd, thursdayStart, thursdayEnd, fridayStart, fridayEnd, saturdayStart, saturdayEnd, sundayStart, sundayEnd)
{
    $.ajax({
        type: "POST",
        url: "controllers/ajax.php",
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
            sundayEnd: sundayEnd
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadContentById('motion-alert-div');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}