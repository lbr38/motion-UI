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
        confirmBox(
            {
                'title': 'Delete medias',
                'message': 'Are you sure you want to delete the selected media(s)?',
                'buttons': [
                {
                    'text': 'Delete',
                    'color': 'red',
                    'callback': function () {
                        ajaxRequest(
                            // Controller:
                            'motion',
                            // Action:
                            'deleteFile',
                            // Data:
                            {
                                mediaId: mediaId
                            },
                            // Print success alert:
                            true,
                            // Print error alert:
                            true,
                            // Reload containers:
                            ['motion/events/list']
                        );
                    }
                }]
            }
        );
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
        // Set the href attribute to the file path, also include the filename for the android app to make sure it downloads the file with the correct name
        temporaryDownloadLink.setAttribute('href', '/media?id=' + download.fileId + '&filename=' + download.filename);
        // Set the download attribute to force download
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

function reloadMotionConfigEditForm(id)
{
    setTimeout(function () {
        ajaxRequest(
            // Controller:
            'general',
            // Action:
            'get-panel',
            // Data:
            {
                name: 'motion/edit',
                params: {
                    'id': id
                }
            },
            // Print success alert:
            false,
            // Print error alert:
            true
        ).then(function () {
            // Get the #camera-edit-motion-config-form-container from jsonValue.message
            content = $(jsonValue.message).find('#camera-edit-motion-config-form-container').html();

            // Replace the content
            $('#camera-edit-motion-config-form-container').html(content);
        });
    }, 50);
}

/**
 *  Start / stop motion service
 */
$(document).on('click','.start-stop-service-btn',function () {
    var status = $(this).attr('status');

    if (status == 'start') {
        printAlert('Starting motion capture, please wait...');
    }
    if (status == 'stop') {
        printAlert('Stopping motion capture, please wait...');
    }

    ajaxRequest(
        // Controller:
        'motion',
        // Action:
        'start-stop',
        // Data:
        {
            status: status
        },
        // Print success alert:
        false,
        // Print error alert:
        true,
        // Reload containers:
        [],
        // Execute functions :
        []
    );

    setTimeout(function () {
        reloadContainer('motion/buttons/main');
    }, 3500);
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
 *  Event: acquit all events
 */
$(document).on('click','.acquit-events-btn',function () {
    printAlert('Acquitting all events, please wait...');

    ajaxRequest(
        // Controller:
        'motion',
        // Action:
        'acquit-events',
        // Data:
        {},
        // Print success alert:
        true,
        // Print error alert:
        true,
        // Reload containers:
        [ 'motion/events/list', 'buttons/bottom' ]
    );
});

/**
 * Function: print events between selected dates
 * @param {*} dateStart
 * @param {*} dateEnd
 */
function eventDateSelect(dateStart, dateEnd)
{
    /**
     *  Add specified dates into cookies
     */
    document.cookie = "eventDateStart="+dateStart+";max-age=900;";
    document.cookie = "eventDateEnd="+dateEnd+";max-age=900;";

    /**
     *  Then reload events div
     */
    reloadContainer('motion/events/list');
}

/**
 *  Event: select events dates
 */
$(document).on('change','.event-date-input',function () {
    date = $(this).val();

    document.cookie = "event-date=" + date + ";max-age=900;";

    reloadContainer('motion/events/list');
});

/**
 *  Event: vizualize event image
 */
$(document).on('click','.play-picture-btn',function () {
    var fileId = $(this).attr('file-id');

    html = '<div id="fullscreen">'
    + '<div class="flex align-item-center">'
    + '<img src="/media?id=' + fileId + '" title="Full screen event picture" />'
    + '</div>'
    + '<div class="flex align-item-center justify-center">'
    + '<img src="/assets/icons/close.svg" class="close-fullscreen-btn pointer lowopacity" title="Close fullscreen">'
    + '</div>'
    + '</div>';

    // Append the fullscreen div to the body
    $('body').append(html);
});

/**
 *  Event: close event picture or video
 */
$(document).on('click','.event-print-file-close-btn',function () {
    /**
     *  Mask container div
     */
    $('.event-print-file-container').hide();

    /**
     *  Clear div
     */
    $('.event-print-file').html('');
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
        closeConfirmBox();
        $('#events-captures-div').find('input[class=event-media-checkbox]').removeAttr('style');
        $('#events-captures-div').find('.select-all-media-checkbox').hide();
        return;
    }

    /**
     *  Print confirm box to delete selected medias
     */
    confirmBox(
        {
            'id': 'download-delete-media',
            'title': 'Download or delete selected media(s)',
            'message': '',
            'buttons': [
            {
                'text': 'Download',
                'color': 'blue',
                'callback': function () {
                    downloadMedia();
                }
            },
            {
                'text': 'Delete',
                'color': 'red',
                'callback': function () {
                    deleteMedia();
                }
            }]
        }
    );

    /**
     *  Print related 'Select all' button
     */
    $('#events-captures-div').find('.select-all-media-checkbox[event-id="' + eventId + '"]').css('display', 'initial');

    /**
     *  Print all related checkboxes with opacity 1
     */
    $('#events-captures-div').find('input[class=event-media-checkbox][event-id="' + eventId + '"]').css("visibility", "visible");
    $('#events-captures-div').find('input[class=event-media-checkbox][event-id="' + eventId + '"]').css("opacity", "1");
});

/**
 *  Event: on 'Select all' button click
 */
$(document).on('click',".select-all-media-checkbox",function () {
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
        // Hide 'select all' button
        $(this).hide();
        // Hide confirm box
        closeConfirmBox();
    } else {
        $('#events-captures-div').find('input[class=event-media-checkbox][event-id="' + eventId + '"]').prop('checked', true);
    }
});

/**
 *  Event: get motion configuration form
 */
$(document).on('click','.get-motion-config-form-btn',function () {
    var id = $(this).attr('camera-id');

    getPanel('motion/edit', {'id': id});
});

/**
 *  Event: Collapse motion parameter div
 */
$(document).on('click','.motion-param-collapse-btn',function () {
    var id = $(this).attr('param-id');

    $('.motion-param-div[param-id=' + id + ']').toggle();
});

/**
 *  Event: Delete motion parameter
 */
$(document).on('click','.motion-param-delete-btn',function (e) {
    // Prevent parent to be triggered
    e.stopPropagation();

    var cameraId = $(this).attr('camera-id');
    var name = $(this).attr('param-name');

    confirmBox(
        {
            'title': 'Delete parameter',
            'message': 'Are you sure you want to delete ' + name + ' parameter?',
            'buttons': [
            {
                'text': 'Delete',
                'color': 'red',
                'callback': function () {
                    ajaxRequest(
                        // Controller:
                        'motion',
                        // Action:
                        'delete-param-from-config',
                        // Data:
                        {
                            cameraId: cameraId,
                            name: name
                        },
                        // Print success alert:
                        true,
                        // Print error alert:
                        true,
                        // Reload containers:
                        ['motion/events/list']
                    ).then(function () {
                        reloadMotionConfigEditForm(cameraId);
                    });
                }
            }]
        }
    );
});

/**
 *  Event: edit motion configuration file
 */
$(document).on('submit','#camera-motion-settings-form',function () {
    event.preventDefault();

    var params = {};

    /**
     *  Get the name of the configuration file
     */
    var cameraId = $(this).attr('camera-id');

    /**
     *  Get all the parameters and their value in the form
     */

    /**
     *  First count all span that has name=param-name in the form
     */
    var countTotal = $(this).find('span[name=param-name]').length

    /**
     *  Every configuration param and its value have an Id
     *  Getting param name and the value that have the same Id, then push it all into an array
     */
    if (countTotal > 0) {
        for (let i = 0; i < countTotal; i++) {
            var status = 'disabled';

            /**
             *  Get parameter name and its value
             */
            var name = $(this).find('span[name=param-name][param-id=' + i + ']').attr('value');
            var value = $(this).find('input[name=param-value][param-id=' + i + ']').val()
            if ($(this).find('input[name=param-status][param-id=' + i + ']').is(':checked')) {
                var status = 'enabled';
            }

            params[name] = {
                status: status,
                value: value
            }
        }
    }

    /**
     *  Add additional parameter if any
     */
    var status = 'disabled';

    var name = $(this).find('input[name=additional-param-name]').val();
    var value = $(this).find('input[name=additional-param-value]').val();
    if ($(this).find('input[name=additional-param-status]').is(':checked')) {
        var status = 'enabled';
    }

    if (name != '' && value != '') {
        params[name] = {
            status: status,
            value: value
        }
    }

    ajaxRequest(
        // Controller:
        'motion',
        // Action:
        'configure-motion',
        // Data:
        {
            cameraId: cameraId,
            params: params
        },
        // Print success alert:
        true,
        // Print error alert:
        true
    ).then(function () {
        reloadMotionConfigEditForm(cameraId);
    });

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
            reloadPanel('motion/autostart');
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
            reloadPanel('motion/autostart');
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
            reloadPanel('motion/autostart');
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
 *  Ajax: send a test email
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
            reloadPanel('motion/alert');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}
