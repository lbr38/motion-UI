/**
 * Set video preview thumbnail as unavailable
 * @param {*} fileId
 */
function setVideoThumbnailUnavailable(fileId)
{
    $('.media-thumbnail[file-id=' + fileId + ']').replaceWith('<div class="file-unavailable play-video-btn pointer" file-id="' + fileId + '"><p>Preview unavailable</p></div>');
}