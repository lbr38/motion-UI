/**
 *  Event : show camera controls on mouse enter (only for desktop)
 */
$(document).on('mouseenter', '.camera-container', function () {
    // Check if the screen width is greater than or equal to 1025px
    if (window.innerWidth >= 1025) {
        $(this).find('.camera-controls-container').stop(true, true).slideDown(100);
    }
});

/**
 *  Event : hide camera controls on mouse leave (only for desktop)
 */
$(document).on('mouseleave', '.camera-container', function () {
    // Check if the screen width is greater than or equal to 1025px
    if (window.innerWidth >= 1025) {
        $(this).find('.camera-controls-container').stop(true, true).slideUp(100);
    }
});

/**
 *  Event : show camera controls on mouse click (only for mobile)
 */
$(document).on('click', '.camera-image', function () {
    const cameraId = $(this).attr('camera-id');

    // Check if the screen width is less than or equal to 1024px
    if (window.innerWidth <= 1024) {
        // Check if the camera controls are already visible
        if ($(this).find('.camera-controls-container[camera-id="' + cameraId + '"]').is(':visible')) {
            // If visible, hide them
            $(this).find('.camera-controls-container[camera-id="' + cameraId + '"]').stop(true, true).slideUp(100);
        } else {
            // If not visible, show them
            $(this).find('.camera-controls-container[camera-id="' + cameraId + '"]').stop(true, true).slideDown(100);
        }
    }
});

/**
 *  Event: enable camera stream
 */
$(document).on('click','.enable-camera-stream-btn', function (e) {
    // Prevent parent to be triggered
    e.stopPropagation();

    const cameraId = $(this).attr('camera-id');
    const thisButton = $(this);

    ajaxRequest(
        // Controller:
        'camera/stream',
        // Action:
        'enable',
        // Data:
        {
            id: cameraId,
            enable: 'true'
        },
        // Print success alert:
        false,
        // Print error alert:
        true
    ).then(function () {
        // Hide the camera stream disabled message
        Camera.hideStreamDisabled(cameraId);

        // Set video element as enabled
        Camera.setEnabled(cameraId);

        // Load stream
        loadCameras(cameraId);

        // Change button icon
        $(thisButton).find('img').attr('src', '/assets/icons/videocam.svg');
        $(thisButton).find('img').attr('title', 'Disable stream');

        // Change class
        $(thisButton).removeClass('enable-camera-stream-btn');
        $(thisButton).addClass('disable-camera-stream-btn');

        // Show some buttons
        $('.display-ptz-btns[camera-id="' + cameraId + '"]').css('display', 'flex');
        $('.audio-btn[camera-id="' + cameraId + '"]').css('display', 'flex');
    });
});

/**
 *  Event: disable camera stream
 */
$(document).on('click','.disable-camera-stream-btn', function (e) {
    // Prevent parent to be triggered
    e.stopPropagation();

    const cameraId = $(this).attr('camera-id');
    const thisButton = $(this);

    ajaxRequest(
        // Controller:
        'camera/stream',
        // Action:
        'enable',
        // Data:
        {
            id: cameraId,
            enable: 'false'
        },
        // Print success alert:
        false,
        // Print error alert:
        true
    ).then(function () {
        // Déconnecter la caméra de manière sécurisée
        Camera.safeDisconnect(window.cameraInstances[cameraId], cameraId);

        // Afficher le message de stream désactivé
        $('.camera-disabled[camera-id="' + cameraId + '"]').css('display', 'flex');

        // Change button icon
        $(thisButton).find('img').attr('src', '/assets/icons/videocam-off.svg');
        $(thisButton).find('img').attr('title', 'Enable stream');

        // Change class
        $(thisButton).removeClass('disable-camera-stream-btn');
        $(thisButton).addClass('enable-camera-stream-btn');

        // Hide some buttons
        $('.display-ptz-btns[camera-id="' + cameraId + '"]').css('display', 'none');
        $('.audio-btn[camera-id="' + cameraId + '"]').css('display', 'none');
    });
});

/**
 *  Event: mute/unmute camera audio
 */
$(document).on('click','.audio-btn', function (e) {
    // Prevent parent to be triggered
    e.stopPropagation();

    var cameraId = $(this).attr('camera-id');

    // Unmute the camera audio
    const videoElement = document.querySelector('video[camera-id="' + cameraId + '"]');

    if (videoElement.muted) {
        videoElement.muted = false;
        $(this).find('img').attr('src', '/assets/icons/volume-on.svg');
    } else {
        videoElement.muted = true;
        $(this).find('img').attr('src', '/assets/icons/volume-off.svg');
    }
});

/**
 *  Event: set a camera on full screen
 */
$(document).on('click','.fullscreen-btn', function (e) {
    // Prevent parent to be triggered
    e.stopPropagation();

    const cameraId = $(this).attr('camera-id');
    const videoContainer = document.querySelectorAll('div.camera-image[camera-id="' + cameraId + '"]')[0];

    if (videoContainer.requestFullscreen) {
        videoContainer.requestFullscreen();
    } else if (videoContainer.webkitRequestFullscreen) { // Safari
        videoContainer.webkitRequestFullscreen();
    } else if (videoContainer.msRequestFullscreen) { // IE/Edge
        videoContainer.msRequestFullscreen();
    } else {
        // Fallback for browsers that don't support fullscreen API
        alert('Your browser does not support fullscreen mode.');
        return;
    }

    $('.fullscreen-btn[camera-id="' + cameraId + '"]').hide();
    $('.fullscreen-close-btn[camera-id="' + cameraId + '"]').css('display', 'flex');

    // Hide some buttons that are not available in fullscreen mode
    $('.timelapse-camera-btn[camera-id="' + cameraId + '"]').hide();
});

/**
 *  Event: close camera full screen
 */
$(document).on('click','.fullscreen-close-btn', function (e) {
    // Prevent parent to be triggered
    e.stopPropagation();

    const cameraId = $(this).attr('camera-id');

    if (document.exitFullscreen) {
        document.exitFullscreen();
    } else if (document.webkitExitFullscreen) { // Safari
        document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) { // IE/Edge
        document.msExitFullscreen();
    } else {
        alert('Your browser does not support fullscreen mode.');
        return;
    }

    $('.fullscreen-btn[camera-id="' + cameraId + '"]').css('display', 'flex');
    $('.fullscreen-close-btn[camera-id="' + cameraId + '"]').hide();

    // Show some buttons that are available in normal mode
    $('.timelapse-camera-btn[camera-id="' + cameraId + '"]').css('display', 'flex');
});
