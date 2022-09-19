/**
 *  Event: print userspace div
 */
$(document).on('click','#print-userspace-btn',function () {
    openSlide('#userspace-div');
});

/**
 *  Event: hide userspace div
 */
$(document).on('click','#hide-userspace-btn',function () {
    closeSlide('#userspace-div');
});

/**
 *  Event: change password
 */
$(document).on('submit','#new-password-form',function () {
    event.preventDefault();

    var username = $(this).attr('username');
    var currentPassword = $(this).find('input[name=actual-password]').val();
    var newPassword = $(this).find('input[name=new-password]').val();
    var newPasswordRetype = $(this).find('input[name=new-password-retype]').val();

    changePassword(username, currentPassword, newPassword, newPasswordRetype);

    return false;
});

/**
 * Ajax: change user password
 * @param {*} username
 * @param {*} currentPassword
 * @param {*} newPassword
 * @param {*} newPasswordRetype
 */
function changePassword(username, currentPassword, newPassword, newPasswordRetype)
{
    $.ajax({
        type: "POST",
        url: "controllers/userspace/ajax.php",
        data: {
            action: "changePassword",
            username: username,
            currentPassword: currentPassword,
            newPassword: newPassword,
            newPasswordRetype: newPasswordRetype
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'success');
        },
        error : function (jqXHR, ajaxOptions, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });
}