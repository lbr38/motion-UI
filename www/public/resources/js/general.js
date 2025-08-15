/**
 *  Open websocket connection with server
 */
websocket_client();

/**
 *  Event: hide slided window on escape button press
 */
$(document).keyup(function (e) {
    if (e.key === "Escape") {
        mypanel.close();
        myalert.close();
        myconfirmbox.close();
        $(".modal-window-container").remove();
    }
});

/**
 *  Event: exit browser fullscreen mode (Esc key)
 *  This is different than clicking the close button
 */
$(document).on('fullscreenchange' ,function () {
    // If fullscreen mode is exited, show or hide some elements and buttons that are available or hidden in normal mode
    if (!document.fullscreenElement) {
        // Show all stream images
        $('.camera-image').show();
        $('.fullscreen-close-btn').hide();
        $('.fullscreen-btn').css('display', 'flex');
        $('.timelapse-camera-btn').css('display', 'flex');
        $('#timelapse').remove();
    }
});

/**
 *  Event: print a copy icon on element with .copy class
 */
$(document).on('mouseenter','.copy',function () {
    // If the element is a <pre> tag, the copy icon is in the top right corner
    if ($(this).is('pre')) {
        $(this).append('<img src="/assets/icons/duplicate.svg" class="icon-lowopacity icon-copy-top-right margin-left-5" title="Copy to clipboard">');
    } else {
        $(this).append('<img src="/assets/icons/duplicate.svg" class="icon-lowopacity icon-copy margin-left-5" title="Copy to clipboard">');
    }
});

/**
 *  Event: remove copy icon on element with .copy class
 */
$(document).on('mouseleave','.copy',function () {
    $(this).find('.icon-copy').remove();
    $(this).find('.icon-copy-top-right').remove();
});

/**
 *  Event: copy parent text on click on element with .icon-copy class
 */
$(document).on('click','.icon-copy, .icon-copy-top-right',function (e) {
    // Prevent parent to be triggered
    e.stopPropagation();

    var text = $(this).parent().text().trim();

    navigator.clipboard.writeText(text).then(() => {
        myalert.print('Copied to clipboard', 'success');
    },() => {
        myalert.print('Failed to copy', 'error');
    });
});

/**
 *  Event: copy on click on element with .copy-input-onclick class
 */
$(document).on('click','.copy-input-onclick',function (e) {
    var text = $(this).val().trim();

    navigator.clipboard.writeText(text).then(() => {
        myalert.print('Copied to clipboard', 'success');
    },() => {
        myalert.print('Failed to copy', 'error');
    });
});

/**
 *  Event: mark log as read
 */
$(document).on('click','.acquit-log-btn',function () {
    var id = $(this).attr('log-id');

    $.ajax({
        type: "POST",
        url: "ajax/controller.php",
        data: {
            controller: "general",
            action: "acquitLog",
            id: id
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            mycontainer.reload('header/general-log-messages');
        },
        error : function (jqXHR, textStatus, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            myalert.print(jsonValue.message, 'error');
        },
    });
});

/**
 *  Event: click on a reloadable table page number
 */
$(document).on('click','.reloadable-table-page-btn',function () {
    /**
     *  Get table name and offset from parent
     */
    var table = $(this).parents('.reloadable-table').attr('table');
    var page = $(this).attr('page');

    /**
     *  Calculate offset (page * 10 - 10)
     */
    offset = parseInt(page) * 10 - 10;

    /**
     *  If offset is negative, set it to 0
     */
    if (offset < 0) {
        offset = 0;
    }

    /**
     *  Set cookie for PHP to load the right content
     *  e.g tables/tasks/list-done/offset
     */
    mycookie.set('tables/' + table + '/offset', offset, 1);

    mytable.reload(table, offset);
});
