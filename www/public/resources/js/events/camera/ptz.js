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
 *  Event: prevent click on PTZ buttons to trigger parent (camera image) click event
 */
$(document).on('click','.camera-ptz-btn, .camera-ptz-stop-btn',function (e) {
    e.stopPropagation();
});

/**
 *  Event: press on PTZ button
 *  pointerdown is used instead of click, because click is only triggered when the button is released
 */
$(document).on('pointerdown','.camera-ptz-btn',function (e) {
    // Only primary button (left click, touch or pen)
    if (e.button !== 0) {
        return;
    }

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
 *  Event: press on PTZ stop button
 */
$(document).on('pointerdown','.camera-ptz-stop-btn',function (e) {
    // Only primary button (left click, touch or pen)
    if (e.button !== 0) {
        return;
    }

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
