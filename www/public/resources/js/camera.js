$(document).ready(function () {
    loadStream();

    /**
     *  Setting live grid layout
     */
    var gridLayout = getCookie('liveGridLayout');
    if (gridLayout != null) {
        $('#camera-grid-container').css('grid-template-columns', 'repeat('+gridLayout+', 1fr)');
    }
});

/**
 *  Load stream page (cameras image and timestamp)
 */
function loadStream()
{
    loadCameras();
    reloadImage();
    reloadTimestamp();
}

/**
 *  Load cameras image and hide loading div
 */
function loadCameras()
{
    /**
     *  Quit if there is no camera to load
     */
    if ($('.camera-container').length == 0) {
        return;
    }

    /**
     *  For each camera container, load the camera image and hide the loading div
     */
    $('.camera-container').each(function () {
        /**
         *  Retrieve camera loading div and camera image div
         */
        const cameraLoadingDiv = $(this).find('div.camera-loading');
        const cameraImageDiv = $(this).find('div.camera-image');

        /**
         *  Retrieve camera 'img' tag and its 'data-src' attribute
         */
        const cameraImageImg = cameraImageDiv.find('img');
        const cameraImageSrc = cameraImageImg.attr('data-src');

        /**
         *  Find 'img' tag inside camera image div and set its 'src' attribute to the 'data-src' attribute
         */
        cameraImageDiv.find('img').on('load', function () {
            /**
             *  Print log message
             */
            console.log('Camera(s) loaded');

            /**
             *  Once the image is loaded, hide the loading div and show the image div
             */
            cameraLoadingDiv.hide();
            cameraImageDiv.show();
        }).attr('src', cameraImageSrc);
    });
}

function reloadTimestamp()
{
    /**
     *  Quit if there is no timestamp to reload
     */
    if ($('.camera-image').find('p.camera-image-timestamp').length == 0) {
        return;
    }
    // console.log(Intl.DateTimeFormat().resolvedOptions().timeZone)

    /**
     *  Refresh timestamp every second
     */
    setInterval(function () {
        /**
         *  Get date and time to YYYY-MM-DD HH:MM:SS format, with system timezone
         */
        var date = new Date();
        var year = date.getFullYear();
        var month = (date.getMonth() + 1).toString().padStart(2, '0');
        var day = date.getDate().toString().padStart(2, '0');
        var hours = date.getHours().toString().padStart(2, '0');
        var minutes = date.getMinutes().toString().padStart(2, '0');
        var seconds = date.getSeconds().toString().padStart(2, '0');
        var dateTime = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;

        $('.camera-image').find('p.camera-image-timestamp').each(function () {
            $(this).text(dateTime);
        });
    }, 1000);
}

/**
 *  Regulary reload cameras image
 */
function reloadImage()
{
    /**
     *  Quit if there is no camera image to reload
     */
    if ($('.camera-image').find('img[camera-type="image"]').length == 0) {
        return;
    }

    setInterval(function () {
        console.log('Reloading camera(s) static image');

        /**
         *  Get current Unix timestamp
         */
        var currentTimestamp = Math.floor(Date.now() / 1000);

        /**
         *  Get all camera type 'image' and their refresh param
         */
        $('img[camera-type="image"]').each(function () {
            var cameraId = $(this).attr('camera-id');
            var refreshInterval = $(this).attr('camera-refresh');
            var cameraTimestamp = $(this).attr('refresh-timestamp');

            /**
             *  On first page load, set camera 'next reload' timestamp
             */
            if (cameraTimestamp == '') {
                $(this).attr('refresh-timestamp', (Math.floor((Date.now() / 1000) + parseInt(refreshInterval))));
            }

            /**
             *  If current timestamp matches the 'next reload' timestamp, then reload image src to get a new image
             *  Then set camera 'next reload' timestamp
             */
            if (cameraTimestamp == currentTimestamp) {
                $(this).on('load', function () {
                    /**
                     *  Print log message
                     */
                    console.log('Camera reloaded');

                    /**
                     *  Always make sure to hide the 'camera-unavailable' and 'camera-loading' divs
                     */
                    $('div.camera-unavailable[camera-id=' + cameraId + ']').hide();
                    $('div.camera-loading[camera-id=' + cameraId + ']').hide();
                    $('div.camera-image[camera-id=' + cameraId + ']').show();
                }).attr({
                    'src': '/image?id=' + cameraId + '&' + currentTimestamp,
                    'refresh-timestamp': (Math.floor((Date.now() / 1000) + parseInt(refreshInterval)))
                });
            }
        });
    }, 1000);
}

/**
 *  Event: change live grid layout
 */
$(document).on('click','.live-layout-btn',function () {

    var gridLayout = $(this).attr('columns');

    $('#camera-grid-container').css('grid-template-columns', 'repeat(' + gridLayout + ', 1fr)');

    document.cookie = "liveGridLayout=" + gridLayout + "; Secure";
});

/**
 *  Event: on output type select (new camera form)
 */
$(document).on('click','input[type=radio][name=output-type]',function () {
    var outputType = $('#new-camera-form').find('input[type=radio][name=output-type]:checked').val();

    if (outputType == 'image') {
        $('#new-camera-form').find('.camera-refresh-field').show();
        $('#new-camera-form').find('input[type=checkbox][name=camera-motion-enable]').prop("checked", false);
    } else {
        $('#new-camera-form').find('input[type=checkbox][name=camera-motion-enable]').prop("checked", true);
        $('#new-camera-form').find('.camera-refresh-field').hide();
        $('#new-camera-form').find('.camera-stream-url').hide();
    }
});

/**
 *  Event: print additional 'stream url' field if output type is 'image' and motion detection is enabled
 */
$(document).on('click','input[type=checkbox][name=camera-motion-enable]',function () {
    var outputType = $('#new-camera-form').find('input[type=radio][name=output-type]:checked').val();

    if (outputType == 'image' && $('input[type=checkbox][name=camera-motion-enable]').is(':checked')) {
        $('#new-camera-form').find('.camera-stream-url').show();
    } else {
        $('#new-camera-form').find('.camera-stream-url').hide();
    }
});

/**
 *  Event: enable / disable motion detection
 */
$(document).on('click','input[type=checkbox][name=edit-camera-motion-enable]',function () {
    var id = $(this).attr('camera-id');
    var form = $('#camera-global-settings-form[camera-id="' + id + '"]');
    var outputType = form.attr('output-type');

    if (outputType == 'image' && form.find('input[type=checkbox][name=edit-camera-motion-enable]').is(':checked')) {
        form.find('.camera-stream-url').show();
    } else {
        form.find('.camera-stream-url').hide();
    }
});

/**
 *  Event: Add a new camera
 */
$(document).on('submit','#new-camera-form',function () {
    event.preventDefault();

    var name = $(this).find('input[type=text][name=camera-name]').val();
    var url = $(this).find('input[type=text][name=camera-url]').val();
    var outputType = $(this).find('input[type=radio][name=output-type]:checked').val();
    var outputResolution = $(this).find('select[name=output-resolution]').val();
    var refresh = $(this).find('input[type=number][name=camera-refresh]').val();
    var username = $(this).find('input[type=text][name=camera-username]').val();
    var password = $(this).find('input[type=password][name=camera-password]').val();
    var liveEnable = $(this).find('input[type=checkbox][name=camera-live-enable]').is(':checked');
    var motionEnable = $(this).find('input[type=checkbox][name=camera-motion-enable]').is(':checked');
    var timelapseEnable = $(this).find('input[type=checkbox][name=camera-timelapse-enable]').is(':checked');
    var streamUrl = $(this).find('input[type=text][name=camera-stream-url]').val();

    ajaxRequest(
        // Controller:
        'camera',
        // Action:
        'add',
        // Data:
        {
            name: name,
            url: url,
            streamUrl: streamUrl,
            outputType: outputType,
            outputResolution: outputResolution,
            refresh: refresh,
            liveEnable: liveEnable,
            motionEnable: motionEnable,
            timelapseEnable: timelapseEnable,
            username: username,
            password: password
        },
        // Print success alert:
        true,
        // Print error alert:
        true,
        // Reload containers:
        [ 'cameras/list' ],
        // Execute functions :
        [ loadStream() ]
    );

    return false;
});

/**
 *  Event: edit camera global settings
 */
$(document).on('submit','#camera-global-settings-form',function () {
    event.preventDefault();

    var refresh = '';

    var id = $(this).attr('camera-id');
    var name = $(this).find('input[type=text][name=edit-camera-name]').val();
    var url = $(this).find('input[type=text][name=edit-camera-url]').val();
    var outputResolution = $(this).find('select[name=edit-output-resolution]').val();
    var streamUrl = $(this).find('input[type=text][name=edit-camera-stream-url]').val();
    var rotate = $(this).find('select[name=edit-camera-rotate]').val();
    var textLeft = $(this).find('input[type=text][name=edit-camera-text-left]').val();
    var textRight = $(this).find('input[type=text][name=edit-camera-text-right]').val();
    var username = $(this).find('input[type=text][name=edit-camera-username]').val();
    var password = $(this).find('input[type=password][name=edit-camera-password]').val();
    var liveEnable = $(this).find('input[type=checkbox][name=edit-camera-live-enable]').is(':checked');
    var motionEnable = $(this).find('input[type=checkbox][name=edit-camera-motion-enable]').is(':checked');
    var timelapseEnable = $(this).find('input[type=checkbox][name=edit-camera-timelapse-enable]').is(':checked');

    ajaxRequest(
        // Controller:
        'camera',
        // Action:
        'edit-global-settings',
        // Data:
        {
            id: id,
            name: name,
            url: url,
            streamUrl: streamUrl,
            outputResolution: outputResolution,
            refresh: refresh,
            rotate: rotate,
            textLeft: textLeft,
            textRight: textRight,
            liveEnable: liveEnable,
            motionEnable: motionEnable,
            timelapseEnable: timelapseEnable,
            username: username,
            password: password
        },
        // Print success alert:
        true,
        // Print error alert:
        true,
        // Reload containers:
        [ 'cameras/list' ],
        // Execute functions :
        [ loadStream(), reloadEditForm(id) ]
    );

    return false;
});


/**
 *  Event: edit camera stream settings
 */
$(document).on('submit','#camera-stream-settings-form',function () {
    event.preventDefault();

    // Default value
    var refresh = 3;
    var id = $(this).attr('camera-id');
    var timestampLeft = $(this).find('input[type=checkbox][name="camera-stream-setting-timestamp-left"]').is(':checked');
    var timestampRight = $(this).find('input[type=checkbox][name="camera-stream-setting-timestamp-right"]').is(':checked');
    // If refresh field exists, get its value
    if ($(this).find('input[type=number][name="camera-stream-setting-refresh"]').length > 0) {
        var refresh = $(this).find('input[type=number][name="camera-stream-setting-refresh"]').val();
    }

    ajaxRequest(
        // Controller:
        'camera',
        // Action:
        'edit-stream-settings',
        // Data:
        {
            id: id,
            refresh: refresh,
            timestampLeft: timestampLeft,
            timestampRight: timestampRight
        },
        // Print success alert:
        true,
        // Print error alert:
        true,
        // Reload containers:
        [ 'cameras/list' ],
        // Execute functions :
        [ loadStream(), reloadEditForm(id) ]
    );

    return false;
});

/**
 *  Event: Delete a camera
 */
$(document).on('click','.delete-camera-btn',function () {
    var cameraId = $(this).attr('camera-id');

    confirmBox('Are you sure you want to delete this camera?', function () {
        ajaxRequest(
            // Controller:
            'camera',
            // Action:
            'delete',
            // Data:
            {
                cameraId: cameraId,
            },
            // Print success alert:
            true,
            // Print error alert:
            true,
            // Reload containers:
            [ 'cameras/list' ],
            // Execute functions :
            [ closePanel('edit-camera'), loadStream() ]
        );
    });
});

/**
 *  Event: show camera configuration form
 */
$(document).on('click','.configure-camera-btn',function () {
    var cameraId = $(this).attr('camera-id');

    /**
     *  Ask the server to generate the configuration form
     */
    getEditForm(cameraId);
});

/**
 *  Event: show camera timelapse
 */
$(document).on('click','.timelapse-camera-btn',function () {
    var cameraId = $(this).attr('camera-id');

    ajaxRequest(
        // Controller:
        'timelapse',
        // Action:
        'get-timelapse',
        // Data:
        {
            cameraId: cameraId
        },
        // Print success alert:
        false,
        // Print error alert:
        true,
        // Reload containers:
        null,
        // Execute functions :
        [ "$('footer').append(jsonValue.message)" ]
    );
});

/**
 *  Event: select another timelapse date
 */
$(document).on('change','#timelapse-date-input',function () {
    var date = $(this).val();
    var cameraId = $(this).attr('camera-id');

    /**
     *  Insert timelapse-date-changed in local storage to stop the timelapse if it is playing
     */
    localStorage.setItem('timelapse-date-changed', true);

    ajaxRequest(
        // Controller:
        'timelapse',
        // Action:
        'get-timelapse-by-date',
        // Data:
        {
            cameraId: cameraId,
            date: date
        },
        // Print success alert:
        false,
        // Print error alert:
        true,
        // Reload containers:
        null,
        // Execute functions :
        [
            "$('#timelapse').replaceWith(jsonValue.message);"
        ]
    );
});

/**
 *  Event: play timelapse
 */
$(document).on('click','#timelapse-play-btn',function () {
    /**
     *  Call async function to play timelapse
     */
    playTimelapse()
});

/**
 *  Play timelapse
 *  @returns
 */
async function playTimelapse()
{
    /**
     *  Retrieve camera id, date, max range and all pictures names
     */
    var cameraId = $('#picture-slider').attr('camera-id');
    var date = $('#picture-slider').attr('date');
    var max = $('#picture-slider').attr('max');
    var pictures = $('timelapse-data').attr('pictures');

    /**
     *  Quit if no date was found
     */
    if (!date) {
        printAlert('No timelapse date found', 'error');
        return;
    }

    /**
     *  Quit if no max range was found
     */
    if (!max) {
        printAlert('No timelapse max range found', 'error');
        return;
    }

    /**
     *  Quit if no pictures were found
     */
    if (!pictures) {
        printAlert('No timelapse images to play', 'error');
        return;
    }

    /**
     *  Quit if max = 0
     */
    if (max == 0) {
        printAlert('No timelapse images to play', 'error');
        return;
    }

    /**
     *  Explode pictures string to array, to get all pictures with an index
     */
    var pictures = pictures.split(',');

    /**
     *  Remove pause mode from slider if it was in pause mode
     *  Change button to 'pause' button
     */
    $('#picture-slider').removeAttr('pause');
    $('#timelapse-play-btn').hide();
    $('#timelapse-pause-btn').css('display', 'inline-flex');

    /**
     *  Get the current slider value as index, and convert it to integer
     *  This will be the starting point of the timelapse
     */
    var index = parseInt($('#picture-slider').val());

    /**
     *  Convert max to integer
     */
    var max = parseInt(max);

    /**
     *  If current index is greater or equal to max, then reset index to 0
     *  This means that the timelapse has reached the end (previous play was finished)
     *  So we start again from the beginning
     */
    if (index >= max) {
        index = 0;
    }

    /**
     *  The loop will run until the index reaches the max range
     */
    while (index < max + 1) {
        /**
         *  Quit if timelapse div was closed
         */
        if ($('#timelapse').length == 0) {
            return;
        }

        /**
         *  Quit if timelapse is in pause mode
         */
        if ($('#picture-slider').attr('pause') == 'true') {
            return;
        }

        /**
         *  Quit if timelapse-date-changed is set in local storage
         *  This means that the user has changed the date while the timelapse is playing so we stop the timelapse and
         *  remove the 'timelapse-date-changed' from local storage
         */
        if (localStorage.getItem('timelapse-date-changed')) {
            localStorage.removeItem('timelapse-date-changed');
            return;
        }

        /**
         *  Get JPEG picture filename from the array
         *  e.g. timelapse_08-17-50.jpg
         */
        var picture = pictures[index];

        /**
         *  Define the path to the target picture
         *  e.g. /timelapse?id=14&picture=2024-06-04/timelapse_08-17-50.jpg
         */
        var path = '/timelapse?id=' + cameraId + '&picture=' + date + '/' + picture;

        /**
         *  Extract the time from the picture name
         */
        var time = picture.split('_')[1].split('.')[0];
        var hour = time.split('-')[0];
        var min = time.split('-')[1];
        var sec = time.split('-')[2];

        /**
         *  Update the image and the slider value
         */
        $('#timelapse-picture').attr('src', path);
        $('#picture-slider').val(index);

        /**
         *  Update the picture time
         */
        $('#picture-time').text(hour + ':' + min + ':' + sec);

        /**
         *  Wait 100ms before updating the index
         */
        await new Promise(r => setTimeout(r, 100));

        /**
         *  Always define index from the current slider value (in case the user changed the slider value while the timelapse is playing)
         *  and increment it by 1
         */
        index = parseInt($('#picture-slider').val()) + 1;
    }

    /**
     *  Change button to 'play' button
     */
    $('#timelapse-pause-btn').hide();
    $('#timelapse-play-btn').css('display', 'inline-flex');
}

/**
 *  Event: pause timelapse
 */
$(document).on('click','#timelapse-pause-btn',function () {
    /**
     *  Change button to 'play' button
     *  Set the slider in pause mode
     */
    $('#timelapse-pause-btn').hide();
    $('#timelapse-play-btn').css('display', 'inline-flex');
    $('#picture-slider').attr('pause', true);
});

/**
 *  Event: hide camera configuration form
 */
$(document).on('click','.hide-camera-configuration-btn',function () {
    var cameraId = $(this).attr('camera-id');

    closePanel('.camera-configuration-div[camera-id='+cameraId+']');
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
 *  Event: close timelapse screen
 */
$(document).on('click','.close-timelapse-btn',function () {
    $('#timelapse').remove();
});

/**
 *  Event: set a camera on full screen
 */
$(document).on('click','.full-screen-camera-btn',function () {
    var cameraId = $(this).attr('camera-id');

    /**
     *  Add full-screen class to set the div on full screen
     */
    $('.camera-container[camera-id='+cameraId+']').addClass("full-screen");

    /**
     *  Show and hide certain buttons
     */
    $('.delete-camera-btn[camera-id='+cameraId+']').hide();
    $('.configure-camera-btn[camera-id='+cameraId+']').hide();
    $('.timelapse-camera-btn[camera-id='+cameraId+']').hide();
    $('.close-full-screen-container[camera-id='+cameraId+']').css('display', 'block');
});

/**
 *  Event: close camera full screen
 */
$(document).on('click','.close-full-screen-btn',function () {
    var cameraId = $(this).attr('camera-id');

    /**
     *  Remove full-screen class to set the div on normal screen
     */
    $('.camera-container[camera-id='+cameraId+']').removeClass("full-screen");

    /**
     *  Show and hide certain buttons
     */
    $('.delete-camera-btn[camera-id='+cameraId+']').show();
    $('.configure-camera-btn[camera-id='+cameraId+']').show();
    $('.timelapse-camera-btn[camera-id='+cameraId+']').show();
    $('.close-full-screen-container[camera-id='+cameraId+']').hide();
});

/**
 * Ajax: get camera configuration form
 * @param {*} id
 */
function getEditForm(id)
{
    $.ajax({
        type: "POST",
        url: "ajax/controller.php",
        data: {
            controller: "camera",
            action: "getEditForm",
            id: id
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            $('#camera-edit-form-container').html(jsonValue.message);
            openPanel('edit-camera');
        },
        error: function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax: reload camera configuration form
 * @param {*} id
 */
function reloadEditForm(id)
{
    setTimeout(function () {
        $.ajax({
            type: "POST",
            url: "ajax/controller.php",
            data: {
                controller: "camera",
                action: "getEditForm",
                id: id
            },
            dataType: "json",
            success: function (data, textStatus, jqXHR) {
                jsonValue = jQuery.parseJSON(jqXHR.responseText);
                $('#camera-edit-form-container').html(jsonValue.message);
            },
            error: function (jqXHR, ajaxOptions, thrownError) {
                jsonValue = jQuery.parseJSON(jqXHR.responseText);
                printAlert(jsonValue.message, 'error');
            },
        });
    }, 50);
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