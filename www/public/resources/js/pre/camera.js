/**
 *  Set camera as unavailable
 *  @param {*} cameraId
 */
function setUnavailable(cameraId)
{
    $('.camera-container[camera-id=' + cameraId + ']').find('.camera-loading').remove();
    $('.camera-container[camera-id=' + cameraId + ']').find('.camera-image').remove();
    $('.camera-container[camera-id=' + cameraId + ']').find('.camera-unavailable').css('display', 'flex');
}
