/**
 *  Open websocket connection with server
 */
websocket_client();

$(document).ready(function () {
    /**
     *  Reload top and bottoms buttons to reload CPU load
     */
    setInterval(function () {
        reloadContainer('buttons/top');
    }, 5000);
});

/**
 *  Event: hide slided window on escape button press
 */
$(document).keyup(function (e) {
    if (e.key === "Escape") {
        closePanel();
        closeAlert();
        closeConfirmBox();
        $('.modal-window-container').remove();
    }
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
            reloadContainer('header/general-log-messages');
        },
        error : function (jqXHR, textStatus, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
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
    setCookie('tables/' + table + '/offset', offset, 1);

    reloadTable(table, offset);
});

/**
 *  Reload opened or closed elements that where opened/closed before reloading
 */
function reloadOpenedClosedElements()
{
    /**
     *  Retrieve sessionStorage with key finishing by /opened (<element>/opened)
     */
    var openedElements = Object.keys(sessionStorage).filter(function (key) {
        return key.endsWith('/opened');
    });

    /**
     *  If there are /opened elements set to true, open them
     */
    openedElements.forEach(function (element) {
        if (sessionStorage.getItem(element) == 'true') {
            var element = element.replace('/opened', '');
            $(element).show();
        }
        if (sessionStorage.getItem(element) == 'false') {
            var element = element.replace('/opened', '');
            $(element).hide();
        }
    });
}

/**
 * Ajax: Get and reload table
 * @param {*} table
 * @param {*} offset
 */
function reloadTable(table, offset)
{
    // printLoading();

    ajaxRequest(
        // Controller:
        'general',
        // Action:
        'getTable',
        // Data:
        {
            table: table,
            offset: offset,
            sourceUrl: window.location.href,
            sourceUri: window.location.pathname,
            sourceGetParameters: getGetParams()
        },
        // Print success alert:
        false,
        // Print error alert:
        true
    ).then(function () {
        // Replace table with itself, with new content
        $('.reloadable-table[table="' + table + '"]').replaceWith(jsonValue.message);
    });

    /**
     *  Hide loading icon
     */
    // hideLoading();
}