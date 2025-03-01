/**
 *  Event: get panel
 */
$(document).on('click','.get-panel-btn',function () {
    var name = $(this).attr('panel');
    getPanel(name);
});

/**
 *  Slide panel closing
 */
$(document).on('click','.slide-panel-close-btn',function () {
    var name = $(this).attr('slide-panel');
    closePanel(name);
});

/**
 *  Event: close request log details
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
