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
async function loadStream()
{
    await loadCameras();
    reloadTimestamp();
}

async function loadCameras(cameraId = null)
{
    /**
     *  Quit if there is no camera to load
     */
    if ($('.camera-container').length == 0) {
        return;
    }

    /**
     *  If a camera id is provided, then load only this camera
     *  Otherwise, load all cameras
     */
    if (cameraId != null) {
        var cameraContainers = $('.camera-container[camera-id="' + cameraId + '"]').toArray();
    } else {
        /**
         *  For each camera container, load the camera image and hide the loading div
         */
        var cameraContainers = $('.camera-container').toArray();
    }

    await Promise.all(cameraContainers.map(async(container) => {
        /**
         *  If there is no camera-image div (case where the stream is disabled), then ignore it
         */
        if ($(container).find('div.camera-image').find('video').length == 0) {
            return;
        }

        /**
         *  If the video stream is disabled for this camera (has attribute "disabled"), then ignore it
         */
        if ($(container).find('div.camera-image').find('video').attr('disabled')) {
            return;
        }

        /**
         *  Retrieve camera Id
         */
        const cameraId = $(container).find('div.camera-image').attr('camera-id');

        /**
         *  Get camera stream technology
         */
        const streamTechnology = $(container).find('div.camera-image').attr('stream-technology');

        /**
         *  Get camera width
         */
        const cameraWidth = $(container).find('div.camera-image').attr('width');

        /**
         *  Connect to the camera using WebRTC
         *  See js/stream/webrtc.js
         */
        if (streamTechnology == 'webrtc') {
            const video = document.querySelector('video[camera-id="' + cameraId + '"]');

            // Open connection to the camera
            connect(cameraId);

            if (video) {
                let videoTimeout = null;

                /**
                 *  Custom timeout check to pint a timeout error message if the camera is not available
                 *  The setTimeout() function will be canceled if the connection is successful (video is playing)
                 */
                function setVideoTimeout()
                {
                    clearVideoTimeout();
                    videoTimeout = setTimeout(() => {
                        console.warn('Camera #' + cameraId + ' connection timeout');
                        setUnavailable(cameraId, 'Connection timeout');
                    }, 30000);
                }

                function clearVideoTimeout()
                {
                    if (videoTimeout) {
                        clearTimeout(videoTimeout);
                        videoTimeout = null;
                    }
                }

                // Restart the timeout when the video is playing
                video.addEventListener('playing', setVideoTimeout);

                // Restart the timeout when the video is updated (frame is changed)
                video.addEventListener('timeupdate', setVideoTimeout);

                // Start the timeout check
                setVideoTimeout();
            }
        }

        /**
         *  Connect to the camera using MSE or MJPEG
         *  See js/stream/video-stream.js
         */
        if (streamTechnology == 'mse' || streamTechnology == 'mjpeg') {
            // const params = new URLSearchParams(location.search);

            // support multiple streams and multiple modes
            const width = '1 0 ' + cameraWidth;

            // videoElement must be the <video> with the camera-id attribute
            const video = document.querySelector('video[camera-id="' + cameraId + '"]');

            /** @type {VideoStream} */
            const videoElement = document.createElement('video-stream');
            videoElement.mode = streamTechnology;
            videoElement.style.flex = width;
            videoElement.src = new URL('api/ws?src=camera_' + cameraId, location.href);
            videoElement.cameraId = cameraId;

            // Replace existing video with videoElement
            video.replaceWith(videoElement);
        }
    }));
}

/**
 * Update camera timestamp
 * @returns
 */
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
        true
    ).then(function () {
        reloadContainer('cameras/list');

        setTimeout(function () {
            loadStream();
        }, 1000);
    });

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
        [ 'cameras/list' ]
    ).then(function () {
        reloadEditForm(id);
        setTimeout(function () {
            loadStream();
        }, 1000);
    });

    return false;
});

/**
 *  Event: Delete a camera
 */
$(document).on('click','.delete-camera-btn',function () {
    var cameraId = $(this).attr('camera-id');

    confirmBox(
        {
            'title': 'Delete camera',
            'message': 'Are you sure you want to delete this camera?',
            'buttons': [
            {
                'text': 'Delete',
                'color': 'red',
                'callback': function () {
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
                        true
                    ).then(function () {
                        mypanel.close('camera/edit');

                        // Remove camera container
                        $('.camera-container[camera-id="' + cameraId + '"]').remove();
                    });
                }
            }]
        }
    );
});

/**
 *  Event: show camera configuration form
 */
$(document).on('click','.configure-camera-btn',function () {
    var cameraId = $(this).attr('camera-id');

    mypanel.get('camera/edit', {'id': cameraId});
});

/**
 *  Event: show camera timelapse
 */
$(document).on('click','.timelapse-camera-btn',function (e) {
    // Prevent parent to be triggered
    e.stopPropagation();

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
        true
    ).then(function () {
        $('body').append(jsonValue.message);

        // Set timelapse on fullscreen
        const timelapseContainer = document.querySelector('div#timelapse');

        if (timelapseContainer.requestFullscreen) {
            timelapseContainer.requestFullscreen();
        } else if (timelapseContainer.webkitRequestFullscreen) { // Safari
            timelapseContainer.webkitRequestFullscreen();
        } else if (timelapseContainer.msRequestFullscreen) { // IE/Edge
            timelapseContainer.msRequestFullscreen();
        } else {
            // Fallback for browsers that don't support fullscreen API
            alert('Your browser does not support fullscreen mode.');
            return;
        }

        // Temporary hide all other stream images to avoid CPU loads
        $('.camera-image').hide();
    });
});

/**
 *  Event: select another timelapse date
 */
$(document).on('change','#timelapse-date-input',function () {
    var date = $(this).val();
    var cameraId = $(this).attr('camera-id');
    var status = $('#picture-slider').attr('status');

    /**
     *  Insert timelapse-date-changed in local storage to stop the timelapse if it is playing
     */
    if (status == 'playing') {
        localStorage.setItem('timelapse-date-changed', true);
    }

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
        true
    ).then(function () {
        /**
         *  Replace with new content
         */
        $('#timelapse').html(jsonValue.message);
    });
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
 */
function playTimelapse()
{
    /**
     *  Retrieve camera id, date, max range and all pictures names
     */
    var cameraId = $('#picture-slider').attr('camera-id');
    var date = $('#picture-slider').attr('date');
    var max = $('#picture-slider').attr('max');
    var pictures = $('timelapse-data').attr('pictures');
    var speed = $('#timelapse-speed-input').val();

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
     *  Set slider status to 'playing'
     *  Change button to 'pause' button
     */
    $('#picture-slider').attr('status', 'playing');
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
                if ($('#picture-slider').attr('status') == 'pause') {
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
                 *  Get timelapsed speed again, in case the user changed it while the timelapse is playing
                 */
                var speed = $('#timelapse-speed-input').val();

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
                    /**
                     *  Always define index from the current slider value (in case the user changed the slider value while the timelapse is playing)
                     *  and increment it by 1
                     */
                    this.index = parseInt($('#picture-slider').val()) + 1;

                    // Image is fully loaded, update the slider value
                    $('#picture-slider').val(this.index);

                    // Update the picture time
                    $('#picture-time').text(hour + ':' + min + ':' + sec);

                    this.imgElement.attr("src", path);

                    setTimeout(() => this.loadNextImage(), speed); // Pause
                };
            }

            /**
             *  If the index reaches the max range, then stop the timelapse
             */
            if (this.index == this.images.length) {
                $('#picture-slider').attr('status', 'pause');
                $('#timelapse-play-btn').css('display', 'inline-flex');
                $('#timelapse-pause-btn').hide();
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
    $('#picture-slider').attr('status', 'pause');
});

/**
 *  Event: close timelapse screen
 */
$(document).on('click','.close-timelapse-btn',function () {
    // Show all stream images
    $('.camera-image').show();

    $('#timelapse').remove();
});

/**
 *  Event: close camera full screen
 */
$(document).on('click','.close-fullscreen-btn',function () {
    // Show all stream images
    $('.camera-image').show();

    // Remove the fullscreen div
    $('#fullscreen').remove();
});

/**
 * Ajax: reload camera configuration form
 * @param {*} id
 */
function reloadEditForm(id)
{
    setTimeout(function () {
        ajaxRequest(
            // Controller:
            'general',
            // Action:
            'get-panel',
            // Data:
            {
                name: 'camera/edit',
                params: {
                    'id': id
                }
            },
            // Print success alert:
            false,
            // Print error alert:
            true
        ).then(function () {
            // Get the #camera-edit-form-container from jsonValue.message
            content = $(jsonValue.message).find('#camera-edit-form-container').html();

            // Replace the content
            $('#camera-edit-form-container').html(content);
        });
    }, 50);
}