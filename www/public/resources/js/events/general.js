/**
 *  Event: get panel
 */
$(document).on('click','.get-panel-btn',function () {
    mypanel.get($(this).attr('panel'));
});

/**
 *  Event: close panel
 */
$(document).on('click','.slide-panel-close-btn',function () {
    mypanel.close($(this).attr('slide-panel'));
});

/**
 *  Event: close modal window
 */
$(document).on('click','.modal-window-close-btn',function () {
    $(".modal-window-container").remove();
});

/**
 *  Event: click on a toggle button
 */
$(document).on('click','.toggle-btn',function () {
    $($(this).attr('target')).toggle();
});
