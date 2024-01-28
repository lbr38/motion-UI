$(document).ready(function () {
    loadCameras();
    reloadImage();

    /**
     *  Setting live grid layout
     */
    var gridLayout = getCookie('liveGridLayout');
    if (gridLayout != null) {
        $('#camera-grid-container').css('grid-template-columns', 'repeat('+gridLayout+', 1fr)');
    }
});

/**
 *  Load cameras image and hide loading div
 */
function loadCameras()
{
    /**
     *  Just wait a little bit to be sure that all camera divs are loaded
     */
    setTimeout(function () {
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
                console.log('Camera image loaded');

                /**
                 *  Once the image is loaded, hide the loading div and show the image div
                 */
                cameraLoadingDiv.hide();
                cameraImageDiv.show();
            }).attr('src', cameraImageSrc);
        });
    }, 500);
}

/**
 *  Regulary reload cameras image
 */
function reloadImage()
{
    if (!$('.camera-image').find('img[camera-type="image"]')) {
        return;
    }

    setInterval(function () {
        console.log('Reloading cameras image');
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
                $(this).attr('src', '/image?id=' + cameraId + '&' + currentTimestamp);
                $(this).attr('refresh-timestamp', (Math.floor((Date.now() / 1000) + parseInt(refreshInterval))));
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
    var textLeft = $(this).find('input[type=text][name=camera-text-left]').val();
    var textRight = $(this).find('input[type=text][name=camera-text-right]').val();
    var refresh = $(this).find('input[type=number][name=camera-refresh]').val();
    var username = $(this).find('input[type=text][name=camera-username]').val();
    var password = $(this).find('input[type=password][name=camera-password]').val();
    var liveEnable = $(this).find('input[type=checkbox][name=camera-live-enable]').is(':checked');
    var motionEnable = $(this).find('input[type=checkbox][name=camera-motion-enable]').is(':checked');
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
            textLeft: textLeft,
            textRight: textRight,
            refresh: refresh,
            liveEnable: liveEnable,
            motionEnable: motionEnable,
            username: username,
            password: password
        },
        // Reload containers:
        [ 'getting-started', 'motion/buttons/main', 'cameras/list' ],
        // Execute functions :
        [ loadCameras() ]
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
    var outputType = $(this).attr('output-type');
    var outputResolution = $(this).find('select[name=edit-output-resolution]').val();
    var streamUrl = $(this).find('input[type=text][name=edit-camera-stream-url]').val();
    var rotate = $(this).find('select[name=edit-camera-rotate]').val();
    var textLeft = $(this).find('input[type=text][name=edit-camera-text-left]').val();
    var textRight = $(this).find('input[type=text][name=edit-camera-text-right]').val();
    var username = $(this).find('input[type=text][name=edit-camera-username]').val();
    var password = $(this).find('input[type=password][name=edit-camera-password]').val();
    var liveEnable = $(this).find('input[type=checkbox][name=edit-camera-live-enable]').is(':checked');
    var motionEnable = $(this).find('input[type=checkbox][name=edit-camera-motion-enable]').is(':checked');

    if (outputType == 'image') {
        var refresh = $(this).find('input[type=number][name=edit-camera-refresh]').val();
    }

    ajaxRequest(
        // Controller:
        'camera',
        // Action:
        'edit',
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
            username: username,
            password: password
        },
        // Reload containers:
        [ 'getting-started', 'motion/buttons/main', 'cameras/list' ],
        // Execute functions :
        [ loadCameras(), reloadEditForm(id) ]
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
            // Reload containers:
            [ 'getting-started', 'motion/buttons/main', 'cameras/list' ],
            // Execute functions :
            [ loadCameras() ]
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
    $('.full-screen-camera-btn[camera-id='+cameraId+']').hide();
    $('.close-full-screen-camera-btn[camera-id='+cameraId+']').css('display', 'block');
});

/**
 *  Event: close camera full screen
 */
$(document).on('click','.close-full-screen-camera-btn',function () {
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
    $('.full-screen-camera-btn[camera-id='+cameraId+']').show();
    $('.close-full-screen-camera-btn[camera-id='+cameraId+']').hide();
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