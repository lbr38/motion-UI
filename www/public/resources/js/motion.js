/**
 *  Create empty motion status and events charts
 */
var ctx = document.getElementById('motion-event-chart').getContext('2d');
var myEventChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [
        {
            data: [],
            label: "Total events per day",
            borderColor: '#3e95cd',
            fill: false
        },
        {
            data: [],
            label: "Total files recorded per day",
            borderColor: '#ea974d',
            fill: false
        }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        tension: 0.2,
        scales: {
            x: {
                display: true,
            },
            y: {
                beginAtZero: true,
                display: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
    }
});
var yLabels = {
    0 : 'inactive',
    1 : 'active'
}
var ctx = document.getElementById('motion-status-chart').getContext('2d');
var myMotionStatusChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            data: [],
            label: "Motion service activity (48h)",
            borderColor: '#d8524e',
            fill: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        tension: 0.2,
        scales: {
            x: {
                display: true,
            },
            y: {
                beginAtZero: true,
                display: true,
                ticks: {
                    stepSize: 1,
                    callback: function (value, index, values) {
                        return yLabels[value];
                    }
                }
            }
        },
    }
});

/**
 *  Inject charts labels and data
 */
loadAllStatsCharts();

/**
 *  Load and generate stats charts
 */
function loadAllStatsCharts()
{
    /**
     *  Get labels and data
     */
    var eventLabels = $('#motion-event-chart-labels-data').attr('labels').split(", ");
    var eventData = $('#motion-event-chart-labels-data').attr('event-data').split(", ");
    var filesData = $('#motion-event-chart-labels-data').attr('files-data').split(", ");

    var statusLabels = $('#motion-status-chart-labels-data').attr('labels').split(", ");
    var statusData = $('#motion-status-chart-labels-data').attr('status-data').split(", ");

    /**
     *  Inject/update labels and data into the charts
     */
    myEventChart.data.labels = eventLabels;
    myEventChart.data.datasets[0].data = eventData;
    myEventChart.data.datasets[1].data = filesData;
    myEventChart.update();

    myMotionStatusChart.data.labels = statusLabels;
    myMotionStatusChart.data.datasets[0].data = statusData;
    myMotionStatusChart.update();
}

/**
 * Function: print stats between selected dates
 * @param {*} dateStart
 * @param {*} dateEnd
 */
function statsDateSelect(dateStart, dateEnd)
{
    /**
     *  Add specified dates into cookies
     */
    document.cookie = "statsDateStart="+dateStart+";max-age=900;";
    document.cookie = "statsDateEnd="+dateEnd+";max-age=900;";

    /**
     *  Then reload stats div
     */
    reloadContentById('motion-stats-labels-data');

    /**
     *  Wait for the div reload, then reload charts
     */
    setTimeout(function () {
        loadAllStatsCharts();
    }, 500);
}

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
    reloadContentById('events-captures-div');
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
 *  Event: show 'how to configure alerts' div
 */
$(document).on('click','#how-to-alert-btn',function () {
    $("#how-to-alert-container").slideToggle('100');
});

/**
 *  Event: validate select event dates form
 */
$(document).on('submit','#statsDateForm',function () {
    event.preventDefault();

    var dateStart = $(this).find('input[type=date][name="dateStart"]').val();
    var dateEnd = $(this).find('input[type=date][name="dateEnd"]').val();

    statsDateSelect(dateStart, dateEnd);

    return false;
});

/**
 *  Event: validate select event dates form
 */
$(document).on('submit','#eventDateForm',function () {
    event.preventDefault();

    var dateStart = $(this).find('input[type=date][name="dateStart"]').val();
    var dateEnd = $(this).find('input[type=date][name="dateEnd"]').val();

    eventDateSelect(dateStart, dateEnd);

    return false;
});

/**
 *  Event: vizualize event image
 */
$(document).on('click','.play-image-btn',function () {
    var fileId = $(this).attr('file-id');

    visualize(fileId, 'image');
});

/**
 *  Event: vizualize event video
 */
$(document).on('click','.play-video-btn',function () {
    var fileId = $(this).attr('file-id');

    visualize(fileId, 'video');
});

/**
 *  Event: download event image or video
 */
$(document).on('click','.save-image-btn, .save-video-btn',function () {
    var fileId = $(this).attr('file-id');

    download(fileId);
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
                reloadContentById('motion-start-div');
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
 * Ajax: visualize event file
 * @param {*} fileId
 * @param {*} type
 */
function visualize(fileId, type)
{
    $.ajax({
        type: "POST",
        url: "controllers/motion/ajax.php",
        data: {
            action: "getEventFile",
            fileId: fileId
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            /**
             *  Inject image or video
             */
            if (type == 'image') {
                $('#event-print-file').html('<img src="resources/events-pictures/'+jsonValue.message+'" />');
            }
            if (type == 'video') {
                $('#event-print-file').html('<video controls><source src="resources/events-pictures/'+jsonValue.message+'"><p>You browser does not support embedded videos.</p></video>');
            }

            /**
             *  Show div and scroll to top
             */
            $('#event-print-file-div').show();
            scroll(0,0);
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax: download event file
 * @param {*} fileId
 */
function download(fileId)
{
    $.ajax({
        type: "POST",
        url: "controllers/motion/ajax.php",
        data: {
            action: "getEventFile",
            fileId: fileId
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);

            /**
             *  Append a new <a> tag with 'download' attribute that points to the file
             *  The 'download' attribute will force download the file and not redirect to it in a new tab
             *  Click on the generate <a> then remove it from the DOM
             */
            var a = $("<a />");
            a.attr("download", '');
            a.attr("href", 'resources/events-pictures/'+jsonValue.message);
            $("body").append(a);
            a[0].click();
            $("body").remove(a);
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