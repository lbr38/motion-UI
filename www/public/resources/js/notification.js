/**
 *  Event: mark notification as read
 */
$(document).on('click','.acquit-notification-btn',function () {
    var id = $(this).attr('notification-id');

    acquitNotification(id);
});

/**
 * Ajax: Mark notification as read
 * @param {string} id
 */
function acquitNotification(id)
{
    $.ajax({
        type: "POST",
        url: "ajax/controller.php",
        data: {
            controller: "notification",
            action: "acquit",
            id: id
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            myalert.print(jsonValue.message, 'success');
            mypanel.reload('general/notification');
            mycontainer.reload('buttons/bottom');
        },
        error : function (jqXHR, textStatus, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            myalert.print(jsonValue.message, 'error');
        },
    });
}