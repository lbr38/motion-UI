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
function loadStream(reload = false)
{
    loadCameras(reload);
    reloadTimestamp();
}

/**
 *  Load cameras image and hide loading div
 */
function loadCameras(reload = false)
{
    timeout = 0;

    // If reload is true, set timeout to 2000ms, to wait for the DOM to be fully reloaded
    if (reload) {
        timeout = 2000;
    }

    // Wait for the DOM to be fully reloaded
    setTimeout(function () {
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
            }).attr('src', cameraImageSrc + '&' + new Date().getTime());
        });
    }, timeout);
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
 *  Event: change live grid layout
 */
$(document).on('click','.live-layout-btn',function () {

    var gridLayout = $(this).attr('columns');

    $('#camera-grid-container').css('grid-template-columns', 'repeat(' + gridLayout + ', 1fr)');

    document.cookie = "liveGridLayout=" + gridLayout + "; Secure";
});

/**
 *  Event: show / hide http basic auth fields
 */
$(document).on('click','.basic-auth-switch',function () {
    if ($(this).is(':checked')) {
        $('.basic-auth-fields').show();
    } else {
        $('.basic-auth-fields').hide();
    }
});

/**
 *  Event: show/hide additional 'motion url' field if refresh > 0 and motion detection is enabled
 */
$(document).on('click', 'input[type=checkbox][param-name="motion-detection-enable"]', function () {
    // Retrieve parent form name
    var form = '#' + $(this).closest('form').attr('id');
    var refresh = $(form).find('input.form-param[param-name="refresh"]').val();

    if ($(this).is(':checked') && refresh > 0) {
        $(form).find('.motion-url-field').show();
    } else {
        $(form).find('.motion-url-field').hide();
    }
});
$(document).on('change', 'input.form-param[param-name="refresh"]', function () {
    // Retrieve parent form name
    var form = '#' + $(this).closest('form').attr('id');
    var refresh = $(this).val();
    var motionDetectionEnable = $(form).find('input[type=checkbox][param-name="motion-detection-enable"]').is(':checked');

    if (motionDetectionEnable && refresh > 0) {
        $(form).find('.motion-url-field').show();
    } else {
        $(form).find('.motion-url-field').hide();
    }
});

function getFormParams(form)
{
    var params = {};

    /**
     *  Search all inputs with class 'form-param' in the form
     */
    $(form).find('.form-param').each(function () {
        // Getting param name in the 'param-name' attribute of each input
        var param_name = $(this).attr('param-name');

        /**
         *  If input is a checkbox and it is checked then its value is 'true'
         *  Else its value is 'false'
         */
        if ($(this).attr('type') == 'checkbox') {
            if ($(this).is(":checked")) {
                var param_value = 'true';
            } else {
                var param_value = 'false';
            }

        /**
         *  If input is not a checkbox then get its value
         */
        } else {
            var param_value = $(this).val();
        }

        params[param_name] = param_value;
    });

    // Return the params
    return params;
}

/**
 *  Event: Add a new camera
 */
$(document).on('submit','#new-camera-form',function () {
    event.preventDefault();

    // Get form params
    params = getFormParams('#new-camera-form');

    ajaxRequest(
        // Controller:
        'camera',
        // Action:
        'add',
        // Data:
        {
            params: params
        },
        // Print success alert:
        true,
        // Print error alert:
        true,
        // Reload containers:
        [ 'cameras/list' ],
        // Execute functions :
        [ 'loadStream(true)' ]
    );

    return false;
});

/**
 *  Event: edit camera global settings
 */
$(document).on('submit','#edit-global-settings-form',function () {
    event.preventDefault();

    var id = $(this).attr('camera-id');

    // Get form params
    params = getFormParams('#edit-global-settings-form');

    ajaxRequest(
        // Controller:
        'camera',
        // Action:
        'edit-global-settings',
        // Data:
        {
            id: id,
            params: params
        },
        // Print success alert:
        true,
        // Print error alert:
        true,
        // Reload containers:
        [ 'cameras/list' ],
        // Execute functions :
        [
            'loadStream(true)',
            'reloadEditForm(' + id + ')'
        ]
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
            [
                "closePanel('edit-camera')",
                "loadStream(true)"
            ]
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
    max = parseInt(max);

    /**
     *  If current index is greater or equal to max, then reset index to 0
     *  This means that the timelapse has reached the end (previous play was finished)
     *  So we start again from the beginning
     */
    if (index >= max) {
        index = 0;
    }

    /**
     *  Define timelapse object
     */
    const timelapse = {
        images: pictures,
        index: index,
        imgElement: $("#timelapse-picture"),
        loadNextImage: function () {
            if (this.index < this.images.length) {
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
                var picture = pictures[this.index];

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
                 *  Create a new Image object and set its src to the target picture path
                 */
                const nextImage = new Image();
                nextImage.src = path;

                /**
                 *  Once the image is fully loaded, update the image <img> and the slider value
                 */
                nextImage.onload = () => {
                    // Image is fully loaded, update the slider value
                    $('#picture-slider').val(this.index);

                    // Update the picture time
                    $('#picture-time').text(hour + ':' + min + ':' + sec);

                    this.imgElement.attr("src", path);

                    /**
                     *  Always define index from the current slider value (in case the user changed the slider value while the timelapse is playing)
                     *  and increment it by 1
                     */
                    this.index++;

                    setTimeout(() => this.loadNextImage(), 150); // Pause
                };
            }
        }
    };

    /**
     *  Start timelapse
     */
    timelapse.loadNextImage();
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
