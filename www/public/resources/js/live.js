
/**
 *  Setting live grid layout
 */
var gridLayout = getCookie('liveGridLayout');
if (gridLayout != null) {
    $('#camera-container').css('grid-template-columns', 'repeat('+gridLayout+', 1fr)');
}

/**
 *  Event: change live grid layout
 */
$(document).on('click','.live-layout-btn',function () {
    var columns = $(this).attr('columns');

    $('#camera-container').css('grid-template-columns', 'repeat(' + columns + ', 1fr)');
    document.cookie = "liveGridLayout=" + columns + "; Secure";
});

/**
 *  Event: print new camera div
 */
$(document).on('click','#print-new-camera-btn',function () {
    openSlide('#new-camera-div');
});

/**
 *  Event: hide settings div
 */
$(document).on('click','#hide-new-camera-btn',function () {
    closeSlide('#new-camera-div');
});

/**
 *  Event: Add a new camera
 */
$(document).on('submit','#new-camera-form',function () {
    event.preventDefault();

    var cameraName = $(this).find('input[type=text][name=camera-name]').val();
    var cameraUrl = $(this).find('input[type=text][name=camera-url]').val();

    addCamera(cameraName, cameraUrl);

    return false;
});

/**
 *  Event: Edit camera configuration
 */
$(document).on('submit','.edit-camera-configuration-form',function () {
    event.preventDefault();

    var cameraId = $(this).attr('camera-id');
    var cameraName = $(this).find('input[type=text][name=camera-name]').val();
    var cameraUrl = $(this).find('input[type=text][name=camera-url]').val();
    var cameraRotate = $(this).find('select[name=camera-rotate]').val();
    var cameraRefresh = $(this).find('input[type=number][name=camera-refresh]').val();

    editCamera(cameraId, cameraName, cameraUrl, cameraRotate, cameraRefresh);

    return false;
});

/**
 *  Event: Delete a camera
 */
$(document).on('click','.delete-camera-btn',function () {
    var cameraId = $(this).attr('camera-id');

    confirmBox('Are you sure you want to delete this camera?', function () {
        deleteCamera(cameraId);
    });
});

/**
 *  Event: show camera configuration form
 */
$(document).on('click','.configure-camera-btn',function () {
    var cameraId = $(this).attr('camera-id');
    openSlide('.camera-configuration-div[camera-id='+cameraId+']');
});

/**
 *  Event: hide camera configuration form
 */
$(document).on('click','.hide-camera-configuration-btn',function () {
    var cameraId = $(this).attr('camera-id');

    closeSlide('.camera-configuration-div[camera-id='+cameraId+']');
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
 * Ajax : add a new camera
 * @param {*} cameraName
 * @param {*} cameraUrl
 */
function addCamera(cameraName, cameraUrl)
{
    $.ajax({
        type: "POST",
        url: "ajax/controller.php",
        data: {
            controller: "live",
            action: "addCamera",
            cameraName: cameraName,
            cameraUrl: cameraUrl
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadContentById('camera-container');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax : edit camera configuration
 * @param {*} cameraId
 * @param {*} cameraName
 * @param {*} cameraUrl
 * @param {*} cameraRotate
 * @param {*} cameraRefresh
 */
function editCamera(cameraId, cameraName, cameraUrl, cameraRotate, cameraRefresh)
{
    $.ajax({
        type: "POST",
        url: "ajax/controller.php",
        data: {
            controller: "live",
            action: "editCamera",
            cameraId: cameraId,
            cameraName: cameraName,
            cameraUrl: cameraUrl,
            cameraRotate: cameraRotate,
            cameraRefresh: cameraRefresh
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadContentById('camera-container');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Ajax : delete camera
 * @param {*} cameraId
 */
function deleteCamera(cameraId)
{
    $.ajax({
        type: "POST",
        url: "ajax/controller.php",
        data: {
            controller: "live",
            action: "deleteCamera",
            cameraId: cameraId
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
            reloadContentById('camera-container');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}

/**
 * Reload camera image
 * @param {*} cameraId
 */
function reloadImage(cameraId)
{
    $.ajax({
        type: "POST",
        url: "ajax/controller.php",
        data: {
            controller: "live",
            action: "reloadImage",
            cameraId: cameraId
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
           /**
            *  Force the browser to refresh the image by reloading the new image downloaded
            *  Use timestamp to bypass browser cache
            */
            $('.camera-container[camera-id='+cameraId+']').find('.camera-loading').remove();
            $('.camera-unavailable[camera-id='+cameraId+']').hide();
            $('.camera-image[camera-id='+cameraId+']').find('img').attr("src", "/resources/.live/camera"+cameraId+"/image.jpg?"+new Date().getTime());
            $('.camera-image[camera-id='+cameraId+']').find('img').show();
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            /**
             *  Print 'unavailable' div if the an error was returned
             */
            $('.camera-container[camera-id='+cameraId+']').find('.camera-loading').remove();
            $('.camera-image[camera-id='+cameraId+']').hide();
            $('.camera-unavailable[camera-id='+cameraId+']').css('display', 'flex');
        },
    });
}