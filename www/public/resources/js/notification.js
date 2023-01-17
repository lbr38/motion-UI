/**
 *  Event: print notification div
 */
$(document).on('click','#print-notification-btn',function () {
    openSlide('#notification-div');
});

/**
 *  Event: hide notification div
 */
$(document).on('click','#hide-notification-btn',function () {
    closeSlide('#notification-div');
});