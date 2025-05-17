/**
 *  Set camera as unavailable
 *  @param {*} cameraId
 */
function setUnavailable(cameraId, message = null)
{
    $('.video-container[camera-id=' + cameraId + ']').css('display', 'none');
    $('.camera-unavailable[camera-id=' + cameraId + ']').css('display', 'flex');

    if (message != null) {
        $('.camera-unavailable[camera-id=' + cameraId + ']').find('p.note').text(message);
    }
}
