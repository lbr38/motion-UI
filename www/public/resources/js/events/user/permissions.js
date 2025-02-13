
/**
 *  Event: Open user permission panel
 */
$(document).on('click','.user-permissions-edit-btn',function () {
    var id = $(this).attr('user-id');

    getPanel('general/user/permissions', {'Id': id});
});

/**
 *  Event: edit user permissions
 */
$(document).on('submit','#user-permissions-form',function () {
    event.preventDefault();

    var id = $(this).attr('user-id');
    var cameras = $(this).find('#user-permissions-cameras-select').val();

    // If no cameras are selected, set cameras to empty array
    if (empty(cameras)) {
        cameras = [''];
    }

    ajaxRequest(
        // Controller:
        'user/permissions',
        // Action:
        'cameras-access',
        // Data:
        {
            id: id,
            cameras: cameras
        },
        // Print success alert:
        true,
        // Print error alert:
        true
    );

    return false;
});