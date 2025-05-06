/**
 *  Event: display PTZ buttons
 */
$(document).on('click','.display-ptz-btns',function (e) {
    // Prevent parent to be triggered
    e.stopPropagation();

    var cameraId = $(this).attr('camera-id');

    if ($(this).hasClass('visible')) {
        $(this).removeClass('visible');
        $('div.camera-ptz-btn-container[camera-id="' + cameraId + '"]').hide();
    } else {
        $(this).addClass('visible');
        $('div.camera-ptz-btn-container[camera-id="' + cameraId + '"]').css('display', 'flex');
    }
});

/**
 *  Event: click on PTZ button
 */
$(document).on('click','.camera-ptz-btn',function (e) {
    // Prevent parent to be triggered
    e.stopPropagation();

    var cameraId = $(this).attr('camera-id');
    var direction = $(this).attr('direction');
    var moveType = $(this).attr('move-type');
    var moveSpeed = $('input.camera-ptz-move-speed[camera-id="' + cameraId + '"]').val();

    ajaxRequest(
        // Controller:
        'camera/ptz',
        // Action:
        'move',
        // Data:
        {
            cameraId: cameraId,
            direction: direction,
            moveType: moveType,
            moveSpeed: moveSpeed
        },
        // Print success alert:
        false,
        // Print error alert:
        true
    );
});

/**
 *  Event: click on PTZ stop button
 */
$(document).on('click','.camera-ptz-stop-btn',function () {
    var cameraId = $(this).attr('camera-id');

    ajaxRequest(
        // Controller:
        'camera/ptz',
        // Action:
        'stop',
        // Data:
        {
            cameraId: cameraId
        },
        // Print success alert:
        false,
        // Print error alert:
        true
    );
});
