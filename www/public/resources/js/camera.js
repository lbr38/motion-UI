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

    deleteConfirm('Are you sure you want to delete this camera?', function () {
        deleteCamera(cameraId);
    });
});

/**
 *  Event: show camera configuration form
 */
$(document).on('click','.configure-camera-btn',function () {
    var cameraId = $(this).attr('camera-id');

    $('.camera-configuration-div[camera-id='+cameraId+']').slideToggle('100');
});

/**
 *  Event: set a camera on full screen
 */
$(document).on('click','.full-screen-camera-btn',function () {
    var cameraId = $(this).attr('camera-id');

    /**
     *  Add full-screen class to set the div on full screen
     */
    $('#camera'+cameraId+'-container').addClass("full-screen");

    /**
     *  Show and hide certain buttons
     */
    $('.delete-camera-btn[camera-id='+cameraId+']').hide();
    $('.configure-camera-btn[camera-id='+cameraId+']').hide();
    $('.full-screen-camera-btn[camera-id='+cameraId+']').hide();
    $('.close-full-screen-camera-btn[camera-id='+cameraId+']').show();
});

/**
 *  Event: close camera full screen
 */
 $(document).on('click','.close-full-screen-camera-btn',function () {
    var cameraId = $(this).attr('camera-id');

    /**
     *  Remove full-screen class to set the div on normal screen
     */
    $('#camera'+cameraId+'-container').removeClass("full-screen");

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
            url: "controllers/camera/ajax.php",
            data: {
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
            url: "controllers/camera/ajax.php",
            data: {
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
            url: "controllers/camera/ajax.php",
            data: {
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
            url: "controllers/camera/ajax.php",
            data: {
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
                $("#camera"+cameraId+"-container").find('.loading-camera-image').remove();
                $('#camera' + cameraId + '-unavailable').hide();
                $("#camera"+cameraId+"-image").find('img').attr("src", ".live/camera"+cameraId+"/image?"+new Date().getTime());
                $("#camera"+cameraId+"-image").find('img').show();
            },
            error : function (jqXHR, ajaxOptions, thrownError) {
                jsonValue = jQuery.parseJSON(jqXHR.responseText);
                /**
                 *  Print 'unavailable' div if the an error was returned
                 */
                $("#camera"+cameraId+"-container").find('.loading-camera-image').remove();
                $("#camera"+cameraId+"-image").hide();
                $("#camera" + cameraId + "-unavailable").show();
            },
        });
 }