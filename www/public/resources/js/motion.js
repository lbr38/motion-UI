/**
 * Set video preview thumbnail as unavailable
 * @param {*} fileId
 */
function setVideoThumbnailUnavailable(fileId)
{
    $('.media-thumbnail[file-id=' + fileId + ']').replaceWith('<div class="file-unavailable play-video-btn pointer" file-id="' + fileId + '"><p>Preview<br>unavailable</p></div>');
}

/**
 *  Function: get selected media Id and delete them
 */
function deleteMedia()
{
    var mediaId = [];

    /**
     *  Get all selected checkboxes and their file-id (media) attribute
     */
    $('#events-captures-div').find('input[class=event-media-checkbox]:checked').each(function () {
        id = $(this).attr('file-id');
        mediaId.push(id);
    });

    /**
     *  Wait for previous confirm box to be removed
     */
    setTimeout(function () {
        confirmBox('Are you sure you want to delete the selected media(s)?', function () {
            deleteMediaAjax(mediaId);
        });
    }, 10);
}

/**
 *  Function: get selected media Id and download them
 */
function downloadMedia()
{
    filesForDownload = [];

    /**
     *  Get all selected checkboxes and their file-id (media) attribute
     */
    $('#events-captures-div').find('input[class=event-media-checkbox]:checked').each(function () {
        filesForDownload.push({ fileId: $(this).attr('file-id'), filename: $(this).attr('file-name') });
    });

    /**
     *  Append a temporary <a> element to download files
     */
    var temporaryDownloadLink = document.createElement("a");
    temporaryDownloadLink.style.display = 'none';

    document.body.appendChild(temporaryDownloadLink);

    for (var n = 0; n < filesForDownload.length; n++) {
        var download = filesForDownload[n];
        temporaryDownloadLink.setAttribute('href', '/media?id=' + download.fileId);
        temporaryDownloadLink.setAttribute('download', download.filename);

        /**
         *  Click on the <a> element to start download
         */
        temporaryDownloadLink.click();
    }

    /**
     *  Remove temporary <a> element
     */
    document.body.removeChild(temporaryDownloadLink);
}

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
 *  Event: send a test email
 */
$(document).on('click','#send-test-email-btn',function () {
    var mailRecipient = $(this).attr('mail-recipient');
    sendTestEmail(mailRecipient);
});

/**
 *  Event: select stats dates
 */
$(document).on('change','.stats-date-input',function () {
    var dateStart = $('.stats-date-input[name=dateStart]').val();
    var dateEnd = $('.stats-date-input[name=dateEnd]').val();

    statsDateSelect(dateStart, dateEnd);
});

/**
 *  Event: select events dates
 */
$(document).on('change','.event-date-input',function () {
    var dateStart = $('.event-date-input[name=dateStart]').val();
    var dateEnd = $('.event-date-input[name=dateEnd]').val();

    eventDateSelect(dateStart, dateEnd);
});

/**
 *  Event: print next 5 events
 */
$(document).on('click','.event-next-btn',function () {
    /**
     *  Retrieve event date
     */
    var eventDate = $(this).attr('event-date');

    /**
     *  Retrieve event offset value (used for pagination), it is stored in the parent container
     */
    var offset = $('.event-date-container[event-date="' + eventDate + '"]').attr('offset');

    // Increment + 5
    offset = parseInt(offset) + 5;

    /**
     *  Set cookie for PHP to load the right events
     */
    setCookie('motion/events/list/' + eventDate + '/offset', offset, 1);

    /**
     *  Print loading veil
     */
    printLoadingVeilByParentClass('event-date-container[event-date="' + eventDate + '"]');

    /**
     *  Reload the event container matching the date
     */
    $('.event-date-container[event-date="' + eventDate + '"]').load(location.href + ' .event-date-container[event-date="' + eventDate + '"] > *');

    /**
     *  Set the new offset value in the parent container
     */
    $('.event-date-container[event-date="' + eventDate + '"]').attr('offset', offset);
});

/**
 *  Event: print previous 5 events
 */
$(document).on('click','.event-previous-btn',function () {
    /**
     *  Retrieve event date
     */
    var eventDate = $(this).attr('event-date');

    /**
     *  Retrieve event offset value (used for pagination), it is stored in the parent container
     */
    var offset = $('.event-date-container[event-date="' + eventDate + '"]').attr('offset');

    /**
     * Decrement - 5
     */
    offset = parseInt(offset) - 5;

    /**
     *  Offset cannot be negative
     */
    if (offset < 0) {
        offset = 0;
    }

    /**
     *  Set cookie for PHP to load the right events
     */
    setCookie('motion/events/list/' + eventDate + '/offset', offset, 1);

    /**
     *  Print loading veil
     */
    printLoadingVeilByParentClass('event-date-container[event-date="' + eventDate + '"]');

    /**
     *  Reload the event container matching the date
     */
    $('.event-date-container[event-date="' + eventDate + '"]').load(location.href + ' .event-date-container[event-date="' + eventDate + '"] > *');

    /**
     *  Set the new offset value in the parent container
     */
    $('.event-date-container[event-date="' + eventDate + '"]').attr('offset', offset);
});

/**
 *  Event: vizualize event image
 */
$(document).on('click','.play-picture-btn',function () {
    var fileId = $(this).attr('file-id');

    $('#event-print-file').html('<img src="/media?id=' + fileId + '" />');
    $('#event-print-file-div').show();
});

/**
 *  Event: vizualize event video
 */
$(document).on('click','.play-video-btn',function () {
    var fileId = $(this).attr('file-id');

    $('#event-print-file').html('<video controls><source src="/media?id=' + fileId + '"><p>You browser does not support embedded videos.</p></video>');
    $('#event-print-file-div').show();
});

/**
 *  Event: close event picture or video
 */
$(document).on('click','#event-print-file-close-btn',function () {
    /**
     *  Mask container div
     */
    $('#event-print-file-div').hide();

    /**
     *  Clear div
     */
    $('#event-print-file').html('');
});

/**
 *  Event: on event media checkbox checked
 */
$(document).on('click','input[class=event-media-checkbox]',function () {
    var eventId = $(this).attr('event-id');

    /**
     *  Count checked checkboxes
     */
    var count_checked = $('#events-captures-div').find('input[class=event-media-checkbox]:checked').length;

    /**
     *  If no checkbox is selected
     */
    if (count_checked == 0) {
        /**
         *  Hide confirm box, checkboxes and 'Select all' button
         */
        $('#newConfirmAlert').remove();
        $('#events-captures-div').find('input[class=event-media-checkbox]').removeAttr('style');
        $('#events-captures-div').find('.select-all-media-btn').hide();
        return;
    }

    /**
     *  Print confirm box to delete selected medias
     */
    confirmBox(
        '',
        function () {
            deleteMedia(); },
        'Delete',
        function () {
            downloadMedia(); },
        'Download'
    );

    /**
     *  Print related 'Select all' button
     */
    $('#events-captures-div').find('.select-all-media-btn[event-id="' + eventId + '"]').css('display', 'initial');

    /**
     *  Print all related checkboxes with opacity 1
     */
    $('#events-captures-div').find('input[class=event-media-checkbox][event-id="' + eventId + '"]').css("visibility", "visible");
    $('#events-captures-div').find('input[class=event-media-checkbox][event-id="' + eventId + '"]').css("opacity", "1");
});

/**
 *  Event: on 'Select all' button click
 */
$(document).on('click',".select-all-media-btn",function () {
    var eventId = $(this).attr('event-id');

    /**
     *  Count checked checkboxes
     */
    var count_checked = $('#events-captures-div').find('input[class=event-media-checkbox][event-id="' + eventId + '"]:checked').length;

    /**
     *  Count total checkbox
     */
    var count_total = $('#events-captures-div').find('input[class=event-media-checkbox][event-id="' + eventId + '"]').length;

    if (count_checked == count_total) {
        $('#events-captures-div').find('input[class=event-media-checkbox][event-id="' + eventId + '"]').prop('checked', false);
    } else {
        $('#events-captures-div').find('input[class=event-media-checkbox][event-id="' + eventId + '"]').prop('checked', true);
    }
});

/**
 *  Event: save motion configuration file
 */
$(document).on('submit','.camera-motion-settings-form',function () {
    event.preventDefault();

    var options_array = [];

    /**
     *  Get the name of the configuration file
     */
    var cameraId = $(this).attr('camera-id');

    /**
     *  Get all the parameters and their value in the form
     */

    /**
     *  First count all span that has name=option-name in the form
     */
    var countTotal = $(this).find('span[name=option-name]').length

    /**
     *  Every configuration param and its value have an Id
     *  Getting param name and the value that have the same Id, then push it all into an array
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
            var option_name = $(this).find('span[name=option-name][option-id=' + i + ']').attr('value');
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

    /**
     *  Add additional parameter if any
     */
    if ($(this).find('input[name=additional-option-status]').is(':checked')) {
        var option_status = 'enabled';
    } else {
        var option_status = '';
    }
    var option_name = $(this).find('input[name=additional-option-name]').val();
    var option_value = $(this).find('input[name=additional-option-value]').val();

    options_array.push(
        {
            status: option_status,
            name: option_name,
            value: option_value
        }
    );

    configure(cameraId, options_array);

    return false;
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
 *  Event: Add a new device
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

    configureAlert(mondayStart, mondayEnd, tuesdayStart, tuesdayEnd, wednesdayStart, wednesdayEnd, thursdayStart, thursdayEnd, fridayStart, fridayEnd, saturdayStart, saturdayEnd, sundayStart, sundayEnd, mailRecipient);

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
        url: "ajax/controller.php",
        data: {
            controller: "motion",
            action: "startStopMotion",
            status: status
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            if (status == 'start') {
                printAlert('Starting motion service, please wait...', 'success');
            }
            if (status == 'stop') {
                printAlert('Stopping motion service, please wait...', 'success');
            }
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
        url: "ajax/controller.php",
        data: {
            controller: "motion",
            action: "enableAutostart",
            status: status
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            reloadContainer('motion/buttons/main');
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
        url: "ajax/controller.php",
        data: {
            controller: "motion",
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
        url: "ajax/controller.php",
        data: {
            controller: "motion",
            action: "enableDevicePresence",
            status: status
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadPanel('autostart');
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
        url: "ajax/controller.php",
        data: {
            controller: "motion",
            action: "addDevice",
            name: name,
            ip: ip
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadPanel('autostart');
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
        url: "ajax/controller.php",
        data: {
            controller: "motion",
            action: "removeDevice",
            id: id
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadPanel('autostart');
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
        url: "ajax/controller.php",
        data: {
            controller: "motion",
            action: "enableAlert",
            status: status
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            reloadContainer('motion/buttons/main');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax: send a test email
 */
function sendTestEmail(mailRecipient)
{
    $.ajax({
        type: "POST",
        url: "/ajax/controller.php",
        data: {
            controller: "motion",
            action: "sendTestEmail",
            mailRecipient: mailRecipient
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
        },
        error: function (jqXHR, textStatus, thrownError) {
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
 * @param {*} mailRecipient
 */
function configureAlert(mondayStart, mondayEnd, tuesdayStart, tuesdayEnd, wednesdayStart, wednesdayEnd, thursdayStart, thursdayEnd, fridayStart, fridayEnd, saturdayStart, saturdayEnd, sundayStart, sundayEnd, mailRecipient)
{
    $.ajax({
        type: "POST",
        url: "ajax/controller.php",
        data: {
            controller: "motion",
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
            mailRecipient: mailRecipient
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadPanel('alert');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax: configure motion config file
 * @param {*} cameraId
 * @param {*} options_array
 */
function configure(cameraId, options_array)
{
    $.ajax({
        type: "POST",
        url: "ajax/controller.php",
        data: {
            controller: "motion",
            action: "configureMotion",
            cameraId: cameraId,
            options_array: options_array
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadEditForm(cameraId);
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax: delete event media file
 * @param {*} mediaId
 */
function deleteMediaAjax(mediaId)
{
    $.ajax({
        type: "POST",
        url: "ajax/controller.php",
        data: {
            controller: "motion",
            action: "deleteFile",
            mediaId: mediaId
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