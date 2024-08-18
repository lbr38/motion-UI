/**
 *  Set camera as unavailable
 *  @param {*} cameraId
 */
function setUnavailable(cameraId)
{
    $('.camera-container[camera-id=' + cameraId + ']').find('.camera-loading').hide();
    $('.camera-container[camera-id=' + cameraId + ']').find('.camera-image').hide();
    $('.camera-container[camera-id=' + cameraId + ']').find('.camera-unavailable').css('display', 'flex');
}
